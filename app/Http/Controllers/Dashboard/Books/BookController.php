<?php

namespace App\Http\Controllers\Dashboard\Books;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\ProductGroup;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Log;

class BookController extends BaseController
{
    public function index()
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $this->shareCommonData();

        return view('dashboard.index', [
            'View' => 'dashboard.books.index',
            'title' => 'Управление книгами | Единая система BaID',
            'PageName' => 'Ассортимент',
            'InPageName' => 'Управление книгами',
            'CreateObject' => '/seller/books/create',
        ]);
    }

    public function ajax(Request $request)
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $sellerId = Auth::user()->getSellerId();

        try {
            $filters = [
                'type' => $request->input('type'),
                'author' => $request->input('author'),
                'status' => $request->input('status'),
                'min_price' => $request->input('min_price'),
                'max_price' => $request->input('max_price'),
                'search' => $request->input('search'),
            ];

            $minPrice = is_numeric($filters['min_price']) ? (float)$filters['min_price'] : null;
            $maxPrice = is_numeric($filters['max_price']) ? (float)$filters['max_price'] : null;

            if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
                [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
            }

            $filters['min_price'] = $minPrice;
            $filters['max_price'] = $maxPrice;

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);

            $query = Book::query();

            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }
            if (!empty($filters['author'])) {
                $query->where('author', 'like', '%' . $filters['author'] . '%');
            }
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['search'])) {
                $query->where('name', 'like', '%' . $filters['search'] . '%');
            }

            if ($filters['min_price'] !== null) {
                $query->where('price', '>=', $filters['min_price']);
            }
            if ($filters['max_price'] !== null) {
                $query->where('price', '<=', $filters['max_price']);
            }

            if (!Auth::user()->isAdmin()) {
                $query->where('book_seller_id', $sellerId);
            }
            $totalRecords = $query->count();

            $booksQuery = clone $query;
            $books = $booksQuery
                ->skip($start)
                ->take($length)
                ->get();

            $formattedBooks = [];
            foreach ($books as $book) {
                try {
                    $imageHtml = $this->getBookImageHtml($book);

                    $moderation = $book->ModerationCheck();
                    $warningHtml = !$moderation['status']
                        ? '<p class="badge bg-soft-'.$moderation['class'].' text-'.$moderation['class'].'">'.$moderation['error'].'</p>'
                        : '';

                    $nameHtml = '
                                  <div class="d-flex align-items-center gap-3">
                                      <div class="table-book-cover">
                                          ' . $imageHtml . '
                                      </div>
                                      <div>
                                          <a href="/seller/books/' . $book->id . '" class="table-product-name">' .
                                                          htmlspecialchars($book->name ?? 'Без названия') . '</a>
                                          ' . $warningHtml . '
                                          <div class="table-product-actions">
                                              <a href="/seller/books/' . $book->id . '" class="action-btn action-btn-edit">
                                                  <i class="feather-edit-2 me-1"></i> Изменить
                                              </a>
                                              <a href="javascript:void(0);" class="action-btn action-btn-delete delete_book" data-id="' . $book->id . '">
                                                  <i class="feather-trash-2 me-1"></i> Удалить
                                              </a>
                                          </div>
                                      </div>
                                  </div>';

                    $formattedBooks[] = [
                        'id' => $book->id,
                        'name' => $nameHtml,
                        'type' => $this->formatBookType($book->type),
                        'author' => htmlspecialchars($book->author ?? 'Не указан'),
                        'status' => $book->status,
                        'price' => ($book->price ?? 0) . ' руб.',
                    ];
                } catch (\Exception $e) {
                    Log::error('Error formatting book data: ' . $e->getMessage());
                    $formattedBooks[] = [
                        'id' => $book->id,
                        'name' => 'Ошибка загрузки',
                        'type' => 'Ошибка данных',
                        'author' => 'Не указан',
                        'status' => 'error',
                        'price' => '0 руб.',
                    ];
                }
            }

            $types = ['ebook' => 'Электронная книга', 'audiobook' => 'Аудиокнига'];
            $statuses = ['draft', 'moderation', 'approved', 'rejected', 'published'];

            return response()->json([
                'draw' => (int)$draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$totalRecords,
                'data' => $formattedBooks,
                'filters' => $filters,
                'types' => $types,
                'statuses' => $statuses,
            ]);
        } catch (\Exception $e) {
            Log::error('DataTable error: ' . $e->getMessage());

            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Произошла ошибка при загрузке данных',
            ], 500);
        }
    }

    private function formatBookType(string $type): string
    {
        $types = [
            'ebook' => 'Электронная книга',
            'audiobook' => 'Аудиокнига',
        ];

        return $types[$type] ?? $type;
    }

    private function getBookImageHtml(Book $book): string
    {
        $width = 100;
        $height = 150;

        $imagePath = $book->image_path ?? null;

        if (empty($imagePath) || !Storage::disk('s3')->exists($imagePath)) {
            $safeName = htmlspecialchars($book->name ?? 'Без названия');
            $safeAuthor = htmlspecialchars($book->author ?? 'Автор');

            return <<<HTML
                <div style="width: {$width}px; height: {$height}px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px; position: relative; overflow: hidden; color: white; font-family: sans-serif;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1;">
                        <div style="position: absolute; top: 10%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 30%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 50%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 70%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 90%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                    </div>
                    <i class="feather-book" style="font-size: 1.5rem; opacity: 0.8; margin-bottom: 8px;"></i>
                    <div style="font-size: 8px; line-height: 1.3; max-height: 40px; overflow: hidden; text-align: center; padding: 0 4px;">
                        {$safeName}
                    </div>
                    <div style="width: 30px; height: 2px; background: rgba(255,255,255,0.5); margin: 6px 0;"></div>
                    <div style="font-size: 6px; opacity: 0.9; max-height: 25px; overflow: hidden; text-align: center;">
                        {$safeAuthor}
                    </div>
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 4px;">
                        <div style="width: 100%; height: 3px; background: rgba(255,255,255,0.2); border-radius: 2px;"></div>
                    </div>
                </div>
                HTML;
        }

        $imageUrl = Storage::disk('s3')->url($imagePath);
        $safeName = htmlspecialchars($book->name ?? '');

        return <<<HTML
            <img src="{$imageUrl}"
                 alt="{$safeName}"
                 style="width: {$width}px; height: {$height}px; object-fit: cover; object-position: center; background-color: #f8f9fa;"
                 loading="lazy">
            HTML;
    }

    public function show($id)
    {
        if (!Auth::user()->groupInfo()->hasPermission('create_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $this->shareCommonData();
        $genres = ProductGroup::where('ShopMode', 8)->get();

        if(Auth::user()->isAdmin()) {
            $sellers = Seller::all();
        }

        if ($id != 'create') {
            $book = Book::findOrFail($id);
            return view('dashboard.index', [
                "View" => "dashboard.books.show",
                "title" => $book->name . " | Единая система BaID",
                'PageName' => 'Ассортимент',
                'InPageName' => $book->name,
                'genres' => $genres,
                'sellers' => isset($sellers) ? $sellers : '',
                'book' => $book,
            ]);
        }

        return view('dashboard.index', [
            "View" => "dashboard.books.show",
            "title" => "Создание книги | Единая система BaID",
            'PageName' => 'Ассортимент',
            'InPageName' => 'Создание книги',
            'genres' => $genres,
            'sellers' => isset($sellers) ? $sellers : '',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:ebook,audiobook',
            'name' => 'required|string|max:255',
            'genre_id' => 'required|int',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'isbn' => 'nullable|string|max:13',
            'language' => 'nullable|string|max:2',
            'annotation' => 'nullable|string',
            'narrator' => 'nullable|string|max:255',
            'book_seller_id' => 'required|int',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'price' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $book = Book::create([
                'book_seller_id' => $validated['book_seller_id'],
                'type' => $validated['type'],
                'name' => $validated['name'],
                'genre_id' => $validated['genre_id'],
                'author' => $validated['author'],
                'publisher' => $validated['publisher'] ?? null,
                'publication_year' => $validated['publication_year'] ?? null,
                'isbn' => $validated['isbn'] ?? null,
                'language' => $validated['language'] ?? null,
                'annotation' => $validated['annotation'] ?? null,
                'narrator' => $validated['narrator'] ?? null,
                'status' => 'draft',
                'moderation_status' => 'pending',
                'price' => $validated['price'] ?? null,
            ]);

            if ($request->hasFile('image')) {
                $this->saveBookImage($book, $request->file('image'));
            }

            session('book_id', $book->id);
        });

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно создана',
            'data' => ['book_id' => session('book_id')],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'type' => 'in:ebook,audiobook',
            'name' => 'required|string|max:255',
            'genre_id' => 'required|int',
            'status' => 'in:draft,complete',
            'is_active' => 'boolean',
            'author' => 'string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'isbn' => 'nullable|string|max:13',
            'annotation' => 'nullable|string',
            'narrator' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'price' => 'nullable|numeric',
            'book_seller_id' => 'required|int',
        ]);

        DB::transaction(function () use ($request, $book, $validated) {
            $updateData = [
                'book_seller_id' => $validated['book_seller_id'],
                'type' => $validated['type'] ?? $book->type,
                'name' => $validated['name'],
                'genre_id' => $validated['genre_id'],
                'author' => $validated['author'] ?? $book->author,
                'publisher' => $validated['publisher'] ?? $book->publisher,
                'publication_year' => $validated['publication_year'] ?? $book->publication_year,
                'isbn' => $validated['isbn'] ?? $book->isbn,
                'language' => $validated['language'] ?? $book->language,
                'annotation' => $validated['annotation'] ?? $book->annotation,
                'narrator' => $validated['narrator'] ?? $book->narrator,
                'status' => $validated['status'] ?? $book->status,
                'is_active' => $request->boolean('is_active'),
                'price' => $validated['price'] ?? null,
            ];

            $book->update($updateData);

            if ($request->hasFile('image')) {
                $this->saveBookImage($book, $request->file('image'), true);
            } elseif ($request->has('remove_image') && $request->remove_image === 'true') {
                if ($book->image_path) {
                    Storage::disk('s3')->delete($book->image_path);
                    $book->update(['image_path' => null]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно обновлена',
            'book' => $book->fresh(),
        ]);
    }

    private function saveBookImage(Book $book, \Illuminate\Http\UploadedFile $file, bool $replace = false)
    {
        $folder = "books/{$book->id}";

        if (!Storage::disk('s3')->exists($folder)) {
            Storage::disk('s3')->makeDirectory($folder);
        }

        if ($replace && $book->image_path) {
            Storage::disk('s3')->delete($book->image_path);
        }

        $extension = $file->extension();
        $filename = "cover_" . Str::random(12) . ".{$extension}";
        $path = "{$folder}/{$filename}";

        Storage::disk('s3')->putFileAs($folder, $file, $filename);

        $book->update(['image_path' => $path]);
    }

    public function chapterShow($bookId, $chapterId)
    {
        if (!Auth::user()->groupInfo()->hasPermission('create_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $this->shareCommonData();

        $book = Book::findOrFail($bookId);
        $chapter = BookChapter::where('book_id', $bookId)
            ->where('id', $chapterId)
            ->firstOrFail();

        return view('dashboard.index', [
            'View' => 'dashboard.books.chapterShow',
            'title' => "Глава: {$chapter->title} | {$book->name}",
            'PageName' => 'Ассортимент',
            'InPageName' => $book->name,
            'book' => $book,
            'chapter' => $chapter,
        ]);
    }

    public function chapterCreate($bookId)
    {
        if (!Auth::user()->groupInfo()->hasPermission('create_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $this->shareCommonData();

        $book = Book::findOrFail($bookId);

        $chapter = BookChapter::where('book_id', $bookId)
            ->orderByDesc('order')
            ->first();

        $order = $chapter ? $chapter->order + 1 : 1;

        return view('dashboard.index', [
            'View' => 'dashboard.books.chapterShow',
            'title' => "Новая глава | {$book->name}",
            'order' => $order,
            'PageName' => 'Ассортимент',
            'InPageName' => $book->name,
            'book' => $book,
        ]);
    }

    public function storeChapter(Request $request, $bookId)
    {
        $book = Book::findOrFail($bookId);

        $rules = [
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'is_free_preview' => 'boolean',
        ];

        if ($book->isEbook()) {
            $rules['content'] = 'required|string';
        } elseif ($book->isAudiobook()) {
            $rules['audio_file'] = 'required|file|mimes:mp3|max:512000';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $book, $validated) {
            $chapterData = [
                'book_id' => $book->id,
                'title' => $validated['title'],
                'order' => $validated['order'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'draft',
                'is_free_preview' => $validated['is_free_preview'] ?? false,
                ''
            ];

            if ($book->isEbook()) {
                $chapterData['content'] = $validated['content'];
                $chapterData['duration'] = mb_strlen($validated['content'], 'UTF-8');
            }

            if ($book->isAudiobook() && $request->hasFile('audio_file')) {
                $file = $request->file('audio_file');
                $path = Storage::disk('s3')->putFileAs(
                    "books/{$book->id}/audio",
                    $file,
                    "chapter_{$validated['order']}_" . time() . '.mp3'
                );

                $chapterData['audio_file_path'] = $path;
                $chapterData['file_size'] = $file->getSize();
                $chapterData['duration'] = $this->getAudioDuration($file);
            }

            $chapter = BookChapter::create($chapterData);

            if ($book->isAudiobook()) {
                $book->updateTotalDuration();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Глава успешно добавлена',
        ]);
    }

    public function updateChapter(Request $request, $bookId, $chapterId)
    {
        $chapter = BookChapter::where('book_id', $bookId)->findOrFail($chapterId);
        $book = $chapter->book;

        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|required|integer',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:draft,published',
            'is_free_preview' => 'boolean',
        ];

        if ($book->isEbook()) {
            $rules['content'] = 'sometimes|required|string';
        } elseif ($book->isAudiobook()) {
            $rules['audio_file'] = 'nullable|file|mimes:mp3|max:512000';
            $rules['remove_audio'] = 'nullable|in:0,1';
        }

        $validated = $request->validate($rules);

        $chapterData = $request->except(['audio_file', 'remove_audio', '_method']);

        // Электронная книга: обновляем контент
        if ($book->isEbook() && isset($validated['content'])) {
            $chapterData['content'] = $validated['content'];
            $chapterData['duration'] = mb_strlen($validated['content'], 'UTF-8');
        }

        // Аудиокнига: обработка флага удаления и нового файла
        if ($book->isAudiobook()) {
            // Удаление существующего аудио
            if ($request->has('remove_audio') && $request->remove_audio == '1' && $chapter->audio_file_path) {
                Storage::disk('s3')->delete($chapter->audio_file_path);
                $chapterData['audio_file_path'] = null;
                $chapterData['file_size'] = null;
                $chapterData['duration'] = 0;
            }

            // Загрузка нового файла
            if ($request->hasFile('audio_file')) {
                if ($chapter->audio_file_path) {
                    Storage::disk('s3')->delete($chapter->audio_file_path);
                }

                $file = $request->file('audio_file');
                $path = Storage::disk('s3')->putFileAs(
                    "books/{$bookId}/audio",
                    $file,
                    "chapter_{$chapter->id}_" . time() . '.mp3'
                );

                $chapterData['audio_file_path'] = $path;
                $chapterData['file_size'] = $file->getSize();
                $chapterData['duration'] = $this->getAudioDuration($file);
            }
        }

        $chapter->update($chapterData);

        if ($book->isAudiobook()) {
            $book->updateTotalDuration();
        }

        return response()->json([
            'success' => true,
            'chapter' => $chapter->fresh(),
        ]);
    }

    private function getAudioDuration($file): int
    {
        if (class_exists(\getID3::class)) {
            try {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($file->getPathname());
                return (int)($fileInfo['playtime_seconds'] ?? 0);
            } catch (\Exception $e) {
                Log::error('getID3 error: ' . $e->getMessage());
            }
        }
        return 0; // Заглушка, если библиотека не установлена
    }

    public function updateModerationStatus(Request $request, $id)
    {
        $request->validate([
            'moderation_status' => 'required|in:pending,approved,rejected',
        ]);

        $book = Book::findOrFail($id);
        $book->update(['moderation_status' => $request->moderation_status]);

        return response()->json([
            'success' => true,
            'book' => $book,
        ]);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if (!Auth::user()->getFirstSeller()->id === $book->seller_id) {
            if (!Auth::user()->groupInfo()->hasPermission('delete_products')) {
                abort(403, 'У вас нет прав на удаление этой книги');
            }
        }

        DB::transaction(function () use ($book) {
            BookChapter::where('book_id', $book->id)->delete();

            if ($book->image_path) {
                Storage::disk('s3')->delete($book->image_path);
            }

            $folder = "books/{$book->id}";
            if (Storage::disk('s3')->exists($folder)) {
                $files = Storage::disk('s3')->files($folder);
                if (!empty($files)) {
                    Storage::disk('s3')->delete($files);
                }
            }

            $book->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно удалена',
        ]);
    }

    public function chaptersAjax(Request $request, $bookId)
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_products')) {
            return response()->json(['error' => 'Нет прав'], 403);
        }

        $book = Book::findOrFail($bookId);

        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);

            $query = BookChapter::query()->where('book_id', $bookId);

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where('title', 'like', "%{$search}%");
            }

            $totalRecords = $query->count();

            $chapters = $query
                ->skip($start)
                ->take($length)
                ->get();

            $formatted = [];
            foreach ($chapters as $chapter) {
                $duration = '';
                if ($chapter->duration_seconds) {
                    $minutes = floor($chapter->duration_seconds / 60);
                    $seconds = $chapter->duration_seconds % 60;
                    $duration = sprintf('%02d:%02d', $minutes, $seconds);
                } else {
                    $duration = '—';
                }

                $actions = '
                    <div class="btn-group btn-group-sm">
                        <a href="/seller/books/' . $bookId . '/chapters/' . $chapter->id . '" class="btn btn-outline-primary">
                            Изменить
                        </a>
                        <button type="button" class="btn btn-outline-danger delete_chapter" data-id="' . $chapter->id . '">
                            Удалить
                        </button>
                    </div>';

                $formatted[] = [
                    'id' => $chapter->id,
                    'number' => $chapter->number ?? '-',
                    'title' => htmlspecialchars($chapter->title ?? 'Без названия'),
                    'duration' => $duration,
                    'file' => $chapter->file_path ? basename($chapter->file_path) : 'Нет файла',
                    'actions' => $actions,
                ];
            }

            return response()->json([
                'draw' => (int)$draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$totalRecords,
                'data' => $formatted,
            ]);
        } catch (\Exception $e) {
            Log::error('Chapters AJAX error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Ошибка при загрузке списка глав',
            ], 500);
        }
    }
}

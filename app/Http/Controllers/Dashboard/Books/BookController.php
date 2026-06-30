<?php

namespace App\Http\Controllers\Dashboard\Books;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\ProductGroup;
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

            // Валидация цен
            $minPrice = is_numeric($filters['min_price']) ? (float)$filters['min_price'] : null;
            $maxPrice = is_numeric($filters['max_price']) ? (float)$filters['max_price'] : null;

            if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
                [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
            }

            $filters['min_price'] = $minPrice;
            $filters['max_price'] = $maxPrice;

            // Параметры пагинации DataTable
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);

            // Основной запрос
            $query = Book::query();

            // Применяем фильтры
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

            // Фильтрация по цене
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

            // Получаем данные с пагинацией
            $booksQuery = clone $query;
            $books = $booksQuery
                ->skip($start)
                ->take($length)
                ->get();

            // Форматируем данные для DataTable
            $formattedBooks = [];
            foreach ($books as $book) {
                try {
                    $imageHtml = $this->getBookImageHtml($book);

                    $nameHtml = '
                        <div class="hstack gap-4">
                            <div class=" border-0">' . $imageHtml . '</div>
                            <div>
                                <a href="/seller/books/' . $book->id . '" class="text-truncate-2-line">' .
                                  htmlspecialchars($book->name ?? 'Без названия') . '</a>
                                <div class="project-list-action fs-12 d-flex align-items-center gap-3 mt-2">
                                    <a href="/seller/books/' . $book->id . '">Изменить</a>
                                    <span class="vr text-muted"></span>
                                    <a href="javascript:void(0);" class="text-danger delete_book" data-id="' . $book->id . '">Удалить</a>
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

    /**
     * Форматирует тип книги для отображения
     */
    private function formatBookType(string $type): string
    {
        $types = [
            'ebook' => 'Электронная книга',
            'audiobook' => 'Аудиокнига',
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Возвращает HTML-код для изображения книги (или заглушки)
     */
    private function getBookImageHtml(Book $book): string
    {
        $width = 100;
        $height = 150;

        // Получаем путь к изображению.
        // ВАЖНО: Убедись, что в БД в колонке image_path хранится именно путь (например, uploads/books/1/cover.jpg), а не URL.
        $imagePath = $book->image_path ?? null;

        // Если пути нет или файл не существует на S3 — показываем заглушку
        if (empty($imagePath) || !Storage::disk('s3')->exists($imagePath)) {
            $safeName = htmlspecialchars($book->name ?? 'Без названия');
            $safeAuthor = htmlspecialchars($book->author ?? 'Автор');

            return <<<HTML
                <div style="width: {$width}px; height: {$height}px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px; position: relative; overflow: hidden; color: white; font-family: sans-serif;">
                    <!-- Декоративные линии фона -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1;">
                        <div style="position: absolute; top: 10%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 30%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 50%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 70%; left: 15%; width: 70%; height: 1px; background: #fff;"></div>
                        <div style="position: absolute; top: 90%; left: 10%; width: 80%; height: 1px; background: #fff;"></div>
                    </div>

                    <!-- Иконка книги -->
                    <i class="feather-book" style="font-size: 1.5rem; opacity: 0.8; margin-bottom: 8px;"></i>

                    <!-- Название книги -->
                    <div style="font-size: 12px; line-height: 1.3; max-height: 40px; overflow: hidden; text-align: center; padding: 0 4px;">
                        {$safeName}
                    </div>

                    <!-- Разделитель -->
                    <div style="width: 30px; height: 2px; background: rgba(255,255,255,0.5); margin: 6px 0;"></div>

                    <!-- Автор -->
                    <div style="font-size: 10px; opacity: 0.9; max-height: 25px; overflow: hidden; text-align: center;">
                        {$safeAuthor}
                    </div>

                    <!-- Нижний декоративный элемент -->
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 4px;">
                        <div style="width: 100%; height: 3px; background: rgba(255,255,255,0.2); border-radius: 2px;"></div>
                    </div>
                </div>
                HTML;
        }

        // Если картинка есть — генерируем URL и возвращаем тег img
        // Используем временный URL, если нужен ограниченный доступ, или обычный url() для публичного
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

        if ($id != 'create') {
            $book = Book::findOrFail($id);
            return view('dashboard.index', [
                "View" => "dashboard.books.show",
                "title" => $book->name . " | Единая система BaID",
                'PageName' => 'Ассортимент',
                'InPageName' => $book->name,
                'genres' => $genres,
                'book' => $book,
            ]);
        }

        return view('dashboard.index', [
            "View" => "dashboard.books.show",
            "title" => "Создание книги | Единая система BaID",
            'PageName' => 'Ассортимент',
            'InPageName' => 'Создание книги',
            'genres' => $genres,
        ]);
    }
    /**
     * Создание новой книги
     */
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
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240', // до 10 МБ
            'price' => 'nullable|numeric',
        ]);

        $sellerId = Auth::user()->getFirstSeller()->id;

        DB::transaction(function () use ($validated, $sellerId) {
            $book = Book::create([
                'seller_id' => $sellerId,
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

            // Сохраняем изображение, если оно есть
            if ($request->hasFile('image')) {
                $this->saveBookImage($book, $request->file('image'));
            }
        });

        session('book_id', $book->id);

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно создана',
            'data' => ['book_id' => session('book_id')],
        ], 201);
    }
    /**
     * Обновление книги
     */
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
        ]);

        DB::transaction(function () use ($request, $book, $validated) {
            // Обновляем основные поля
            $updateData = [
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
                'is_active' => $validated['is_active'] ?? $book->is_active,
                'price' => $validated['price'] ?? null,
            ];

            $book->update($updateData);
            \Log::debug("{$request->image}");
            // Обработка изображения
            if ($request->hasFile('image')) {
                $this->saveBookImage($book, $request->file('image'), true);
            } elseif ($request->has('remove_image') && $request->remove_image === 'true') {
                // Удаление изображения по флагу
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
    /**
     * Сохраняет изображение книги в S3
     * @param bool $replace Если true — удаляет старое изображение перед сохранением нового
     */
    private function saveBookImage(Book $book, \Illuminate\Http\UploadedFile $file, bool $replace = false)
    {
        // Папка для книги
        $folder = "books/{$book->id}";

        // Создаём папку, если её нет
        if (!Storage::disk('s3')->exists($folder)) {
            Storage::disk('s3')->makeDirectory($folder);
        }

        // Удаляем старое изображение, если нужно
        if ($replace && $book->image_path) {
            Storage::disk('s3')->delete($book->image_path);
        }

        // Уникальное имя файла
        $extension = $file->extension();
        $filename = "cover_" . Str::random(12) . ".{$extension}";
        $path = "{$folder}/{$filename}";

        // Сохраняем файл
        Storage::disk('s3')->putFileAs($folder, $file, $filename);
        \Log::debug("{$path}");
        // Обновляем путь к изображению
        $book->update(['image_path' => $path]);
    }

    /**
     * Удаляет изображение книги из S3 и очищает поле в базе данных.
     *
     * @param Book $book
     * @return bool Возвращает true, если файл был успешно удален или его не существовало.
     */
    private function deleteBookImage(Book $book): bool
    {
        // 1. Если пути нет — удалять нечего, считаем успешным
        if (empty($book->image_path)) {
            return true;
        }

        $path = $book->image_path;

        try {
            // 2. Проверяем, существует ли файл на S3 перед удалением (хорошая практика для S3)
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);

                // Опционально: логирование успешного удаления
                \Log::debug("S3 Image deleted: {$path}");
            } else {
                // Файл не найден на диске. Это может быть нормой (уже удален вручную),
                // но стоит залогировать как предупреждение, если это странно для твоего процесса.
                \Log::warning("S3 Image not found for deletion: {$path} (Book ID: {$book->id})");
            }

            // 3. Очищаем поле в базе данных ТОЛЬКО если удаление прошло успешно (или файла не было)
            // Мы не делаем update внутри try-catch блока удаления файла, чтобы избежать рассинхронизации,
            // но в данном простом сценарии это допустимо.
            $book->update(['image_path' => null]);

            return true;

        } catch (\Exception $e) {
            // Критическая ошибка: файл не удалился, но мы не хотим ломать весь процесс удаления книги.
            \Log::error("Failed to delete S3 image: {$path}. Error: " . $e->getMessage());

            // ВАЖНО: Реши, что делать дальше.
            // Вариант А (строгий): Вернуть false, и пусть контроллер решит, удалять ли саму книгу.
            // Вариант Б (мягкий): Все равно очистить БД, считая, что файл "потерян", но запись о нем не нужна.

            // Здесь выбран ВАРИАНТ Б (мягкий), так как чаще всего важно просто убрать ссылку из интерфейса.
            // Если тебе нужна строгая проверка, раскомментируй return false ниже.

            $book->update(['image_path' => null]);
            return false;
        }
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
            'View' => 'dashboard.books.chapter.show',
            'title' => "Глава: {$chapter->title} | {$book->name}",
            'PageName' => 'Ассортимент',
            'InPageName' => $book->name,
            'book' => $book,
            'chapter' => $chapter,
        ]);
    }
    /**
     * Удаление книги (с удалением папки в S3 и всех связанных записей)
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        // Проверка прав
        if (!Auth::user()->getFirstSeller()->id === $book->seller_id) {
            // Либо проверка прав через groupInfo, если у вас другая логика
            if (!Auth::user()->groupInfo()->hasPermission('delete_products')) {
                abort(403, 'У вас нет прав на удаление этой книги');
            }
        }

        DB::transaction(function () use ($book) {
            // Удаляем главы книги
            BookChapter::where('book_id', $book->id)->delete();

            // Если есть изображение — удаляем из S3
            if ($book->image_path) {
                Storage::disk('s3')->delete($book->image_path);
            }

            // Опционально: удаляем всю папку книги в S3 (например, для аудиофайлов глав)
            $folder = "books/{$book->id}";
            if (Storage::disk('s3')->exists($folder)) {
                $files = Storage::disk('s3')->files($folder);
                if (!empty($files)) {
                    Storage::disk('s3')->delete($files);
                }
                // Папку удалять не обязательно — S3 не хранит пустые папки как объекты
            }

            // Удаляем книгу
            $book->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно удалена',
        ]);
    }
    /**
     * AJAX-метод для загрузки списка глав книги (для DataTable внутри страницы книги)
     */
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

            // Простой поиск по названию главы
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
                // Длительность: если есть поле duration_seconds — показываем, иначе заглушка
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\UserBookmark;
use App\Models\UserBookProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookReaderController extends BaseController
{

    public function index($id){
        $this->shareCommonData();
        $book = Book::findOrFail($id);
        return view('books.reader.index', compact('book'));
    }
    /**
     * Получить информацию о книге для читалки
     */
    public function getBookInfo($bookId)
    {
        $book = Book::with(['publishedChapters' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($bookId);

        $userId = Auth::id();

        $bookmark = $book->getUserBookmark($userId);
        $progress = $book->getUserProgress($userId);

        return response()->json([
            'book' => [
                'id' => $book->id,
                'title' => $book->name,
                'author' => $book->author,
                'type' => $book->type,
                'annotation' => $book->annotation,
                'chapters' => $book->publishedChapters->map(function($chapter) {
                    return [
                        'id' => $chapter->id,
                        'title' => $chapter->title,
                        'order' => $chapter->order,
                        'duration' => $chapter->duration,
                        'is_free_preview' => $chapter->is_free_preview,
                    ];
                }),
            ],
            'bookmark' => $bookmark,
            'progress' => $progress,
        ]);
    }

    /**
     * Получить содержимое главы
     */
    public function getChapterContent($bookId, $chapterId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = BookChapter::where('book_id', $bookId)
            ->where('status', 'published')
            ->findOrFail($chapterId);

        return response()->json([
            'chapter' => [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'content' => $chapter->content,
                'audio_url' => $chapter->getAudioUrl(),
                'duration' => $chapter->duration,
                'order' => $chapter->order,
            ],
            'next_chapter_id' => $this->getNextChapterId($bookId, $chapter->order),
            'previous_chapter_id' => $this->getPreviousChapterId($bookId, $chapter->order),
        ]);
    }

    /**
     * Сохранить закладку
     */
    public function saveBookmark(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'chapter_id' => 'required|exists:book_chapters,id',
            'position' => 'nullable|integer',
            'scroll_position' => 'nullable|integer',
            'audio_position' => 'nullable|integer',
            'playback_speed' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        $bookmark = UserBookmark::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'book_id' => $request->book_id,
            ],
            $request->only([
                'chapter_id',
                'position',
                'scroll_position',
                'audio_position',
                'playback_speed',
                'note',
            ])
        );

        // Обновляем прогресс
        $this->updateReadingProgress($request->book_id);

        return response()->json([
            'success' => true,
            'bookmark' => $bookmark,
        ]);
    }

    /**
     * Обновить прогресс чтения
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'reading_time' => 'required|integer', // в секундах
        ]);

        $this->updateReadingProgress(
            $request->book_id,
            $request->reading_time
        );

        return response()->json(['success' => true]);
    }

    /**
     * Отметить главу как прочитанную
     */
    public function markChapterCompleted(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'chapter_id' => 'required|exists:book_chapters,id',
        ]);

        $userId = Auth::id();

        // Получаем или создаем прогресс
        $progress = UserBookProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'book_id' => $request->book_id,
            ],
            [
                'completed_chapters' => 0,
                'total_chapters' => 0,
                'progress_percentage' => 0,
                'total_reading_time' => 0,
            ]
        );

        // Увеличиваем счетчик прочитанных глав
        $progress->completed_chapters += 1;
        $progress->last_read_at = now();
        $progress->updateProgress();

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    private function getNextChapterId($bookId, $currentOrder)
    {
        $nextChapter = BookChapter::where('book_id', $bookId)
            ->where('status', 'published')
            ->where('order', '>', $currentOrder)
            ->orderBy('order')
            ->first();

        return $nextChapter ? $nextChapter->id : null;
    }

    private function getPreviousChapterId($bookId, $currentOrder)
    {
        $previousChapter = BookChapter::where('book_id', $bookId)
            ->where('status', 'published')
            ->where('order', '<', $currentOrder)
            ->orderBy('order', 'desc')
            ->first();

        return $previousChapter ? $previousChapter->id : null;
    }

    private function updateReadingProgress($bookId, $readingTime = 0)
    {
        $userId = Auth::id();

        $progress = UserBookProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'book_id' => $bookId,
            ],
            [
                'completed_chapters' => 0,
                'total_chapters' => 0,
                'progress_percentage' => 0,
                'total_reading_time' => 0,
            ]
        );

        $progress->total_reading_time += $readingTime;
        $progress->last_read_at = now();
        $progress->save();
    }
}

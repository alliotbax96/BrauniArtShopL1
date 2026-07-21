<?php
namespace App\Http\Controllers\Books;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\UserBookProgress;
use App\Models\UserBookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class PlayerController extends Controller
{
    /**
     * Показать плеер
     */
    public function show($bookId)
    {
        $book = Book::findOrFail($bookId);

        if (!$book->isPurchasedBy(Auth::id())) {
            abort(403, 'Книга не куплена');
        }

        if (!$book->isAudiobook()) {
            abort(404);
        }

        $chapters = $book->publishedChapters()->orderBy('order')->get();
        $progress = UserBookProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $bookId],
            ['completed_chapters' => 0, 'total_chapters' => $book->getChapterCount()]
        );

        // Определяем стартовую главу и позицию
        $startChapterId = $progress->current_chapter_id ?? $chapters->first()?->id;
        $startPosition = $progress->current_position ?? 0;
        $playbackSpeed = $progress->playback_speed ?? 1.0;

        return view('player.index', compact(
            'book', 'chapters', 'progress', 'startChapterId', 'startPosition', 'playbackSpeed'
        ));
    }

    /**
     * Сохранение прогресса
     */
    public function saveProgress(Request $request, $bookId)
    {
        $request->validate([
            'chapter_id' => 'required|exists:book_chapters,id',
            'position' => 'required|integer|min:0',
            'playback_speed' => 'nullable|numeric|min:0.5|max:3'
        ]);

        UserBookProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $bookId],
            [
                'current_chapter_id' => $request->chapter_id,
                'current_position' => $request->position,
                'playback_speed' => $request->playback_speed ?? 1.0,
                'last_read_at' => now()
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Создание/обновление закладки
     */
    public function toggleBookmark(Request $request, $bookId)
    {
        $request->validate([
            'chapter_id' => 'required|exists:book_chapters,id',
            'position' => 'required|integer',
            'note' => 'nullable|string|max:500'
        ]);

        $bookmark = UserBookmark::where([
            'user_id' => Auth::id(),
            'book_id' => $bookId,
            'chapter_id' => $request->chapter_id,
        ])->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json(['status' => 'deleted']);
        }

        UserBookmark::create([
            'user_id' => Auth::id(),
            'book_id' => $bookId,
            'chapter_id' => $request->chapter_id,
            'audio_position' => $request->position,
            'note' => $request->note,
        ]);

        return response()->json(['status' => 'created']);
    }

    /**
     * Получить закладки для книги
     */
    public function getBookmarks($bookId)
    {
        $bookmarks = UserBookmark::with('chapter')
            ->where('user_id', Auth::id())
            ->where('book_id', $bookId)
            ->get();

        return response()->json($bookmarks);
    }
}

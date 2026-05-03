<?php

namespace App\Http\Controllers\Products;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\BaseController;

class ReviewController extends BaseController
{
    /**
     * Создание нового отзыва
     */
    public function store(Request $request, $reviewableType, $reviewableId)
    {
        // Проверяем авторизацию
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Для оставления отзыва необходимо авторизоваться'
            ]);
        }

        // Определяем модель по типу
        $modelClass = match ($reviewableType) {
            'product' => \App\Models\Product::class,
            'quests' => \App\Models\Quest::class,
            // добавьте другие типы по необходимости
            default => null,
        };

        if (!$modelClass) {
            return response()->json(['error' => 'Неизвестный тип сущности'], 400);
        }

        $reviewable = $modelClass::findOrFail($reviewableId);

        // Валидация данных
        try {
            $validated = $request->validate([
                'estimation' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ]);
        }

        // Проверяем, не оставлял ли пользователь уже отзыв на эту сущность
        $existingReview = Review::where('user_id', Auth::id())
            ->where('reviewable_id', $reviewableId)
            ->where('reviewable_type', $modelClass)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with(['error' => 'Вы уже оставили отзыв на этот товар']);
        }

        // Создаём отзыв
        $review = Review::create([
            'user_id' => Auth::id(),
            'reviewable_id' => $reviewableId,
            'reviewable_type' => $modelClass,
            'estimation' => $validated['estimation'],
            'comment' => $validated['comment'] ?? null
        ]);

        return redirect()->back();
    }

    /**
     * Получение отзывов сущности
     */
    public function show($reviewableType, $reviewableId)
    {
        $modelClass = match ($reviewableType) {
            'product' => \App\Models\Product::class,
            'quests' => \App\Models\Quest::class,
            default => null,
        };

        if (!$modelClass) {
            return response()->json(['error' => 'Неизвестный тип сущности'], 400);
        }

        $reviewable = $modelClass::findOrFail($reviewableId);
        $reviews = $reviewable->reviews()->with('user')->get();
        $averageRating = $reviewable->reviews()->avg('estimation') ?? 0;

        return response()->json([
            'average_rating' => round($averageRating, 2),
            'total_reviews' => $reviews->count(),
            'reviews' => $reviews
        ]);
    }

    /**
     * Удаление отзыва (только для автора или администратора)
     */
    public function destroy(Review $review)
    {
        if (Auth::id() !== $review->user_id && !Auth::user()->is_admin) {
            return response()->json(['error' => 'У вас нет прав для удаления этого отзыва']);
        }

        $review->delete();

        return response()->json(['message' => 'Отзыв удалён']);
    }
}

<?php

namespace App\Http\Controllers\Products;

use App\Models\Product;
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
    public function store(Request $request, Product $product)
    {
        // Проверяем авторизацию
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Для оставления отзыва необходимо авторизоваться'
            ]);
        }

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

        // Проверяем, не оставлял ли пользователь уже отзыв на этот товар
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with(['error' => 'Вы уже оставили отзыв на этот товар']);
        }

        // Создаём отзыв
        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'estimation' => $validated['estimation'],
            'comment' => $validated['comment'] ?? null
        ]);

        return redirect()->back();
    }

    /**
     * Получение отзывов товара
     */
    public function show(Product $product)
    {
        $reviews = $product->getProductReviews();
        $averageRating = $product->getProductEstimation();

        return response()->json([
            'average_rating' => $averageRating,
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

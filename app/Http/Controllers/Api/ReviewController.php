<?php

namespace App\Http\Controllers\Api;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends ApiBaseController
{
    /**
     * Получение отзывов товара
     */
    public function getProductReviews(int $productId, Request $request)
    {
        $perPage = $request->per_page ?? 20;

        $reviews = Review::where('reviewable_type', 'App\\Models\\Product')
            ->where('reviewable_id', $productId)
            ->with('user:id,name,avatar_url')
            ->latest()
            ->paginate($perPage);

        $averageRating = Review::where('reviewable_type', 'App\\Models\\Product')
            ->where('reviewable_id', $productId)
            ->avg('estimation') ?? 0;

        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->name ?? 'Аноним',
                'user_avatar' => $review->user->avatar_url ?? null,
                'rating' => $review->estimation,
                'comment' => $review->comment,
                'created_at' => $review->created_at->format('d.m.Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedReviews,
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
                'average_rating' => round($averageRating, 1)
            ]
        ]);
    }

    /**
     * Создание отзыва
     */
    public function store(Request $request, string $type, int $id)
    {
        $validator = Validator::make($request->all(), [
            'estimation' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $modelClass = match ($type) {
            'product' => \App\Models\Product::class,
            default => null
        };

        if (!$modelClass) {
            return $this->errorResponse('Неизвестный тип сущности', 400);
        }

        $reviewable = $modelClass::findOrFail($id);

        // Проверка на дубликат
        $existingReview = Review::where('user_id', auth()->id())
            ->where('reviewable_id', $id)
            ->where('reviewable_type', $modelClass)
            ->first();

        if ($existingReview) {
            return $this->errorResponse('Вы уже оставили отзыв', 400);
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'reviewable_id' => $id,
            'reviewable_type' => $modelClass,
            'estimation' => $request->estimation,
            'comment' => $request->comment
        ]);

        return $this->successResponse([
            'review' => [
                'id' => $review->id,
                'rating' => $review->estimation,
                'comment' => $review->comment,
                'created_at' => $review->created_at->format('d.m.Y H:i')
            ]
        ], 'Отзыв добавлен');
    }

    /**
     * Удаление отзыва
     */
    public function destroy(int $id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== auth()->id() && !auth()->user()->is_admin) {
            return $this->errorResponse('Нет прав для удаления', 403);
        }

        $review->delete();

        return $this->successResponse(null, 'Отзыв удалён');
    }
}

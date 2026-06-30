<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiBaseController
{
    /**
     * Профиль пользователя
     */
    public function profile()
    {
        $user = auth()->user()->load(['groups', 'sellers']);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar_url ?? null,
            'groups' => $user->groups->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'type' => $group->type
                ];
            }),
            'is_buyer' => $user->isBuyer(),
            'is_seller' => $user->isSeller(),
            'is_admin' => $user->isAdmin(),
            'created_at' => $user->created_at->format('d.m.Y')
        ]);
    }

    /**
     * Обновление профиля
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|unique:users,phone,' . auth()->id(),
            'avatar' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = auth()->user();
        $data = $request->only(['name', 'email', 'phone']);

        // Обработка аватара
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_url'] = asset('storage/' . $path);
        }

        $user->update($data);

        return $this->successResponse([
            'user' => $this->formatUser($user)
        ], 'Профиль обновлён');
    }

    private function formatUser($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar_url ?? null,
            'created_at' => $user->created_at->format('d.m.Y')
        ];
    }
}

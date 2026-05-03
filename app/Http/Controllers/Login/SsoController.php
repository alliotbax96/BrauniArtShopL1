<?php

namespace App\Http\Controllers\Login;

use App\Models\SsoToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class SsoController extends Controller
{
    public function initiateSso(Request $request)
    {
        // Проверяем, что пользователь авторизован
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Авторизуйтесь сначала');
        }

        $user = Auth::user();

        // Создаём токен
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(5); // Токен живёт 5 минут

        SsoToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Редирект на кабинет продавца с токеном
        $cabinetUrl = 'https://id.brauniart.shop/sso/auth?token=' . $token;

        return redirect($cabinetUrl);
    }

    public function handleSsoAuth(Request $request)
    {
        $tokenString = $request->query('token');

        if (!$tokenString) {
            abort(400, 'Токен не предоставлен');
        }

        // Ищем активный токен
        $ssoToken = SsoToken::where('token', $tokenString)
            ->where('expires_at', '>', now())
            ->first();

        if (!$ssoToken) {
            abort(403, 'Неверный или просроченный токен');
        }

        // Авторизуем пользователя
        Auth::login($ssoToken->user);

        // Удаляем токен после использования
        $ssoToken->delete();

        // Редирект обратно на витрину или на главную кабинета
        return redirect('/')->with('success', 'Авторизованы через SSO!');
    }
}

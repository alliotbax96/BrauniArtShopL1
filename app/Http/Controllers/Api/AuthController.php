<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use App\Services\CartService;
use App\Models\Cart;
use Illuminate\Support\Facades\Session;

class AuthController extends ApiBaseController
{
    /**
     * Отправка кода подтверждения
     */
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $phone = preg_replace('![^0-9]+!', '', $request->phone);
        $code = app()->environment('production') ? rand(1111, 9999) : 1111;

        // Сохраняем код в кеш на 10 минут
        $hash = md5($phone . $code);
        cache()->put('auth_code_' . $phone, $hash, 600);

        // В продакшене отправляем SMS
        if (app()->environment('production')) {
            // Здесь вызов сервиса отправки SMS
        }

        return $this->successResponse(null, 'Код отправлен');
    }

    /**
     * Проверка кода
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string|min:4|max:4'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $phone = preg_replace('![^0-9]+!', '', $request->phone);
        $code = $request->code;
        $hash = md5($phone . $code);
        $cachedHash = cache()->get('auth_code_' . $phone);

        if (!$cachedHash || $cachedHash !== $hash) {
            return $this->errorResponse('Неверный код подтверждения', 400);
        }

        // Помечаем код как проверенный
        cache()->put('auth_verified_' . $phone, true, 600);
        cache()->forget('auth_code_' . $phone);

        return $this->successResponse(['verified' => true], 'Код подтверждён');
    }

    /**
     * Вход или регистрация
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $phone = preg_replace('![^0-9]+!', '', $request->phone);

        // Проверяем, что телефон был верифицирован
        if (!cache()->get('auth_verified_' . $phone)) {
            return $this->errorResponse('Телефон не подтверждён', 400);
        }

        // Ищем или создаём пользователя
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // Регистрация нового пользователя
            $user = User::create([
                'name' => $request->name ?? 'Пользователь',
                'phone' => $phone,
                'email' => $phone . '@temp.com',
                'password' => Hash::make($phone)
            ]);

            // Присваиваем группу "buyer"
            $buyerGroup = \App\Models\Group::where('type', 'buyer')->first();
            if ($buyerGroup) {
                $user->groups()->attach($buyerGroup->id);
            }
        }

        // Авторизуем
        Auth::login($user);
        $token = $user->createToken('mobile-app')->plainTextToken;

        cache()->forget('auth_verified_' . $phone);

        // Объединяем корзины
        $this->mergeCarts($user);

        return $this->successResponse([
            'user' => $this->formatUser($user),
            'token' => $token
        ], 'Успешный вход');
    }

    /**
     * Регистрация с полными данными
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|unique:users,phone',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $phone = preg_replace('![^0-9]+!', '', $request->phone);

        if (!cache()->get('auth_verified_' . $phone)) {
            return $this->errorResponse('Телефон не подтверждён', 400);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $phone,
            'email' => $request->email ?? $phone . '@temp.com',
            'password' => Hash::make($phone)
        ]);

        $buyerGroup = \App\Models\Group::where('type', 'buyer')->first();
        if ($buyerGroup) {
            $user->groups()->attach($buyerGroup->id);
        }

        Auth::login($user);
        $token = $user->createToken('mobile-app')->plainTextToken;

        cache()->forget('auth_verified_' . $phone);

        return $this->successResponse([
            'user' => $this->formatUser($user),
            'token' => $token
        ], 'Регистрация успешна');
    }

    /**
     * Перенаправление на соцсеть
     */
    public function redirectToSocial(string $provider)
    {
        $supportedProviders = ['yandex', 'vkontakte'];

        if (!in_array($provider, $supportedProviders)) {
            return $this->errorResponse('Неподдерживаемый провайдер', 400);
        }

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return $this->successResponse(['redirect_url' => $url], 'Redirect URL');
    }

    /**
     * Обработка callback от соцсети
     */
    public function handleSocialCallback(Request $request, string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$socialAccount) {
                // Создаём нового пользователя
                $user = User::create([
                    'name' => $socialUser->getName() ?? 'Пользователь',
                    'email' => $socialUser->getEmail() ?? $socialUser->getId() . '@' . $provider . '.com',
                    'password' => Hash::make(uniqid())
                ]);

                $buyerGroup = \App\Models\Group::where('type', 'buyer')->first();
                if ($buyerGroup) {
                    $user->groups()->attach($buyerGroup->id);
                }

                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'token' => $socialUser->token,
                    'avatar' => $socialUser->getAvatar()
                ]);
            } else {
                $user = $socialAccount->user;
            }

            Auth::login($user);
            $token = $user->createToken('mobile-app')->plainTextToken;

            return $this->successResponse([
                'user' => $this->formatUser($user),
                'token' => $token
            ], 'Успешный вход через ' . $provider);

        } catch (\Exception $e) {
            \Log::error('Social auth error: ' . $e->getMessage());
            return $this->errorResponse('Ошибка авторизации через ' . $provider, 500);
        }
    }

    /**
     * Выход
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Выход выполнен');
    }

    /**
     * Объединение корзин
     */
    private function mergeCarts(User $user)
    {
        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if ($guestCart) {
            $cartService = app(CartService::class);
            $cartService->setCurrentCart($guestCart);
            $cartService->mergeWithUserCart($user->id);
        }
    }

    /**
     * Форматирование данных пользователя
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar_url ?? null,
            'groups' => $user->groups->pluck('type')->toArray(),
            'created_at' => $user->created_at->format('Y-m-d H:i:s')
        ];
    }
}

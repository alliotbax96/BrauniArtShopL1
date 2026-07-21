<?php

namespace App\Http\Controllers\Login;


use App\Http\Integrations\RedSMS\RedSMSConnector;
use App\Http\Integrations\RedSMS\Requests\SendCallCodeRequest;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use App\Services\CartService;
use App\Models\Cart;
use App\Models\User;
use App\Models\SocialAccount;

class LoginController extends BaseController
{
    public function authForm(Request $request){
        $this->shareCommonData($request);
        return view('index', ['view' => 'auth.login', 'title'=> 'Авторизация | Брауни Арт — маркетплейс качественных товаров с доставкой по России',]);
    }

    public function dashboardLogin(){
        return view('dashboard.auth.login');
    }

    /**
     * Перенаправление пользователя на провайдер Яндекс для авторизации
     */
    public function redirectToYandex(Request $request)
    {
        if($request->route()->getName() == 'auth.login.yandex') {
            $driver = Socialite::driver('yandex');
            // Добавляем redirect как параметр запроса
            $redirectUrl = route('auth.login.yandex.callback');
            return $driver->with([
                'redirect_uri' => $redirectUrl,
            ])->redirect();
        }
        return Socialite::driver('yandex')->redirect();
    }

    /**
     * Получение callback от Яндекса и обработка авторизации
     */
    public function handleYandexCallback(Request $request)
    {
        try {
            $yandexUser = Socialite::driver('yandex')->user();
            $user = $this->findOrCreateUser($yandexUser, 'yandex');

            if (!$user) {
                \Log::warning('User not found for Yandex ID: ' . $yandexUser->getId());
                return redirect('/login')->with('error', 'Аккаунт не найден. Для входа через Яндекс требуется предварительная регистрация.');
            }
            Auth::login($user);
            $token = $user->createToken('API Token')->plainTextToken;

            // Перегенерируем сессию после авторизации
            request()->session()->regenerate();

            // Обработка корзины
            $this->handleCartAfterSocialLogin($user);

            return redirect('/');
        } catch (\Exception $e) {
            \Log::error('Yandex auth error: ' . $e->getMessage());
            if($request->route()->getName() == 'auth.login.yandex.callback') {
                return redirect('/auth')->with('error', 'Ошибка авторизации через Яндекс');
            }
            return redirect('/login')->with('error', 'Ошибка авторизации через Яндекс');
        }
    }

    public function redirectToVK(Request $request){
        if($request->route()->getName() == 'auth.login.vk') {
            $driver = Socialite::driver('vkontakte');
            // Добавляем redirect как параметр запроса
            $redirectUrl = route('auth.login.vk.callback');
            return $driver->with([
                'redirect_uri' => $redirectUrl,
            ])->redirect();
        }
        return Socialite::driver('vkontakte')->redirect();
    }

    public function handleVKCallback(Request $request)
    {
        try {
            $vkUser = Socialite::driver('vkontakte')->user();
            $user = $this->findOrCreateUser($vkUser, 'vkontakte');

            if (!$user) {
                \Log::warning('User not found for VKID: ' . $vkUser->getId());
                return redirect('/login')->with('error', 'Аккаунт не найден. Для входа через VKID требуется предварительная регистрация.');
            }
            Auth::login($user);
            $token = $user->createToken('API Token')->plainTextToken;

            // Перегенерируем сессию после авторизации
            request()->session()->regenerate();

            // Обработка корзины
            $this->handleCartAfterSocialLogin($user);

            return redirect('/');
        } catch (\Exception $e) {
            \Log::error('VKID auth error: ' . $e->getMessage());
            if($request->route()->getName() == 'auth.login.vk.callback') {
                return redirect('/auth')->with('error', 'Ошибка авторизации через VKID');
            }
            return redirect('/login')->with('error', 'Ошибка авторизации через VKID');
        }
    }

    /**
     * Поиск или создание пользователя по данным из социальной сети
     */
    private function findOrCreateUser($socialUser, string $provider): User
    {
        // Ищем существующую привязку
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

//        if ($socialAccount) {
            return $socialAccount->user;
//        }
    }

    /**
     * Обработка корзины после социальной авторизации
     */
    private function handleCartAfterSocialLogin(User $user)
    {
        $guestSessionId = Session::getId();
        $cartService = app(CartService::class);
        $this->forceGuestCartDetection($cartService, $guestSessionId);
        $cartService->mergeWithUserCart($user->id);
    }

    public function sendCode(Request $request){
      if(!isset($request->phone)){
          return response()->json([
              "result" => false,
              "error" => "Не указан номер телефона!"
          ]);
      }
      $phone = $request->phone;
      $code = rand(1111, 9999);
//      $code = 1111;
      Cookie::queue(Cookie::make('auth', md5($phone.$code), 10));
      $send = $this->sendCodeCall($phone, $code);
      if($send['status'] = 'created') {
          return response()->json([
              'result' => true,
          ]);
      } else {
          return response()->json([
              'result' => false,
              'error' => $send->errors[0]
          ]);
      }
    }

    public function checkCode(Request $request, bool $json = true){
        $token = $request->cookie('auth');
        $phone = $request->phone;
        $code = $request->code;
        if(md5($phone.$code) == $token){
            if(!$json){
                return true;
            }
            return response()->json([
                "result" => true,
            ]);
        } else {
            if(!$json){
                return false;
            }
            return response()->json([
                "result" => false,
                "error" => 'Неверный код!'.$phone.$code
            ]);
        }
    }

    private function sendCodeCall(string $to, string $code): array
    {
        try {
            $connector = new RedSMSConnector();
            $request = new SendCallCodeRequest(
                route: 'fcall',
                to: $to,
                text: $code,
                login: $connector->login,
                token: $connector->token
            );

            $response = $connector->send($request);

            if (! $response->isSuccessful()) {
                throw new \RuntimeException(
                    "RedSMS API error: {$response->status()} - {$response->body()}"
                );
            }

            return $response->json();
        } catch (\Exception $e) {
            \Log::error('RedSMS sendCodeCall failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function login(Request $request)
    {
        if(!$this->checkCode($request, false)){
            return response()->json([
                "result" => false,
                "error" => "Введен неверный код подтверждения!"
            ]);
        }

        $credentials = [
            'phone' => preg_replace('![^0-9]+!', '', $request->phone),
            'password' => $request->phone
        ];
        // Сохраняем session_id гостевой корзины перед авторизацией
        $guestSessionId = Session::getId();

        if (Auth::attempt($credentials)) {
            // Перегенерируем сессию после авторизации
            $request->session()->regenerate();

            // Создаём новый экземпляр CartService
            $cartService = app(CartService::class);

            // Явно указываем, что нужно искать гостевую корзину по старому session_id
            $this->forceGuestCartDetection($cartService, $guestSessionId);

            // Выполняем слияние
            $cartService->mergeWithUserCart(Auth::user()->id);

            $token = Auth::user()->createToken('API Token')->plainTextToken;
            return response()->json([
                "result" => true,
                'token' => $token
            ]);
        }

        return response()->json([
            "result" => false,
            "error" => 'Пользователь не найден!'.json_encode($credentials)
        ]);
    }

    /**
     * Принудительно ищет гостевую корзину по указанному session_id и устанавливает её как текущую
     */
    private function forceGuestCartDetection(CartService $cartService, string $sessionId): void
    {
        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if ($guestCart) {
            $cartService->setCurrentCart($guestCart);
        }
    }


    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}


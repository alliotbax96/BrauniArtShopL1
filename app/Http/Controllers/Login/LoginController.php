<?php

namespace App\Http\Controllers\Login;

use App\Http\Integrations\RedSMS\RedSMSConnector;
use App\Http\Integrations\RedSMS\Requests\SendCallCodeRequest;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Services\CartService;
use App\Models\Cart;

class LoginController extends BaseController
{
    public function authForm(){
        $this->shareCommonData();
        $scripts[] = "/assets/scripts/auth/login.js";
        return view('index', ['view' => 'auth.login', 'scripts' => $scripts]);
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
      $code = 1111;
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

    public function checkCode(Request $request){
        $token = $request->cookie('auth');
        $phone = $request->phone;
        $code = $request->code;
        if(md5($phone.$code) == $token){
            return response()->json([
                "result" => true,
            ]);
        } else {
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
        $credentials = [
            'phone' => preg_replace('![^0-9]+!', '', $request->phone),
            'password' => $request->password
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

            return response()->json([
                "result" => true,
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


<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\UserGroupService;

class SignupController extends BaseController
{
    public function signupForm()
    {
        $this->shareCommonData();
        return view('index', ['view' => 'auth.signup', 'title'=> 'Регистрация | Брауни Арт — маркетплейс качественных товаров с доставкой по России',]);
    }

    /**
     * Обрабатывает данные формы регистрации и создаёт нового пользователя
     */
    public function register(Request $request)
    {
        $checkCode = LoginController::checkCode($request, false);
        if(!$checkCode){
            return response()->json([
                "result" => false,
                "error" => "Введен неверный код подтверждения!"
            ]);
        }

        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'code' => 'nullable|string|size:4',
        ], [
            'name.required' => 'Имя обязательно для заполнения',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Введите корректный email',
            'email.unique' => 'Пользователь с таким email уже существует',
            'phone.required' => 'Номер телефона обязателен для заполнения',
            'phone.unique' => 'Пользователь с таким номером телефона уже существует',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = [
                        'field' => $field,
                        'message' => $message
                    ];
                }
            }

            return response()->json([
                'result' => false,
                'errors' => $errors,
            ]);
        }

        try {
            // Создаём нового пользователя
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => preg_replace('![^0-9]+!', '', $request->phone),
                'password' => Hash::make($request->phone),
            ]);
            app(UserGroupService::class)->assignGroup($user, 'buyer');

            // Автоматически авторизуем пользователя после регистрации
            Auth::login($user);

            return response()->json([
                'result' => true,
                'message' => 'Регистрация прошла успешно! Добро пожаловать!',
                'redirect' => '/',
            ]);

        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'error' => 'Произошла ошибка при регистрации. Попробуйте ещё раз.',
            ]);
        }
    }
}

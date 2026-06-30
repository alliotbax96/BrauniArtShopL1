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
    public function signupForm(Request $request)
    {
        $this->shareCommonData($request);
        return view('index', ['view' => 'auth.signup', 'title'=> 'Регистрация | Брауни Арт — маркетплейс качественных товаров с доставкой по России',]);
    }

    public function dashboardSignup(){
        return view('dashboard.auth.signup');
    }

    /**
     * Обрабатывает данные формы регистрации и создаёт нового пользователя
     */
    public function register(Request $request)
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'required|string|max:20|unique_phone',
            'code' => 'nullable|string|size:4',
            'termsCondition'=> 'required'
        ], [
            'name.required' => 'Имя обязательно для заполнения',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Введите корректный email',
            'email.unique' => 'Пользователь с таким email уже существует',
            'phone.required' => 'Номер телефона обязателен для заполнения',
            'phone.unique_phone' => 'Пользователь с таким номером телефона уже существует',
            'termsCondition.required' => 'Необходимо согласится с условиями'
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

        $loginController = new LoginController();
        if(!$loginController->checkCode($request, false)){
            return response()->json([
                "result" => false,
                "errors" => ['0'=>['field'=>'code', 'message'=>"Введен неверный код подтверждения!"]]
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

            $token = $user->createToken('API Token')->plainTextToken;

            // Автоматически авторизуем пользователя после регистрации
            Auth::login($user);

            return response()->json([
                'result' => true,
                'message' => 'Регистрация прошла успешно! Добро пожаловать!',
                'redirect' => '/',
                'token' => $token
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

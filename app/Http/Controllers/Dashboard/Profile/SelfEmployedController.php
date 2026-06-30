<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Http\Controllers\Controller;
use App\Models\SelfEmployedRecord;
use Illuminate\Http\Request;
use Validator;

class SelfEmployedController extends Controller
{
    // Сохранение записи с начальным статусом
    public function saveSelfEmployedRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inn' => ['required', 'string', 'regex:/^\d{12}$/', 'size:12'],
            'user_id' => 'required|integer|exists:users,id',
        ], [
            'inn.required' => 'ИНН обязателен для заполнения',
            'inn.regex' => 'ИНН должен содержать ровно 12 цифр',
            'inn.size' => 'ИНН должен содержать ровно 12 цифр',
            'user_id.required' => 'ID пользователя обязателен для заполнения',
            'user_id.exists' => 'Указанный пользователь не существует',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Ищем существующую запись по user_id (одна запись на пользователя)
            $record = SelfEmployedRecord::where('user_id', $request->user_id)->first();

            if ($record) {
                // Если запись существует — обновляем, очищая все поля кроме user_id, inn, verification_status
                $updateData = [
                    'bank_account_number' => null,
                    'bic' => null,
                    'correspondent_account' => null,
                    'bank_name' => null,
                    'account_holder_name' => null,
                    // Обновляем только нужные поля
                    'inn' => $request->inn,
                    'verification_status' => $request->verification_status ?? 'notVerifiable',
                    'updated_at' => now(),
                ];

                $record->update($updateData);
            } else {
                // Если записи нет — создаём новую
                $record = SelfEmployedRecord::create([
                    'user_id' => $request->user_id,
                    'inn' => $request->inn,
                    'verification_status' => $request->verification_status ?? 'notVerifiable',
                    // Все остальные поля по умолчанию null
                    'bank_account_number' => null,
                    'bic' => null,
                    'correspondent_account' => null,
                    'bank_name' => null,
                    'account_holder_name' => null,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Данные сохранены для проверки',
                'data' => $record
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при сохранении записи самозанятого', [
                'inn' => $request->inn,
                'user_id' => $request->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Ошибка при сохранении данных'
            ], 500);
        }
    }

    /**
     * Обновление данных самозанятого (ИНН и банковские реквизиты)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        // Правила валидации
        $validator = Validator::make($request->all(), [
            'selfEmployedINN' => 'required|digits:12',
            'account_holder_name' => 'required|string|max:40',
            'bank_account_number' => 'required|digits:20',
            'bic' => 'required|digits:9',
            'correspondent_account' => 'required|digits:20',
            'bank_name' => 'required|string|max:100',
        ], [
            // Кастомные сообщения об ошибках
            'selfEmployedINN.required' => 'ИНН является обязательным полем',
            'selfEmployedINN.digits' => 'ИНН должен содержать 12 цифр',
            'account_holder_name.required' => 'ФИО получателя является обязательным полем',
            'account_holder_name.max' => 'ФИО не может превышать 40 символов',
            'bank_account_number.required' => 'Расчётный счёт является обязательным полем',
            'bank_account_number.digits' => 'Расчётный счёт должен содержать 20 цифр',
            'bic.required' => 'БИК является обязательным полем',
            'bic.digits' => 'БИК должен содержать 9 цифр',
            'correspondent_account.required' => 'Корреспондентский счёт является обязательным полем',
            'correspondent_account.digits' => 'Корреспондентский счёт должен содержать 20 цифр',
            'bank_name.required' => 'Название банка является обязательным полем',
            'bank_name.max' => 'Название банка не может превышать 100 символов',
        ], [
            // Человеко‑читаемые названия полей
            'selfEmployedINN' => 'ИНН',
            'account_holder_name' => 'ФИО получателя',
            'bank_account_number' => 'Расчётный счёт',
            'bic' => 'БИК',
            'correspondent_account' => 'Корреспондентский счёт',
            'bank_name' => 'Название банка',
        ]);

        // Если валидация не прошла
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Ошибка валидации данных'
            ], 422);
        }

        // Получаем или создаём запись самозанятого для пользователя
        $selfEmployed = SelfEmployedRecord::findOrFail($request->id);

        // Данные прошли валидацию — обновляем запись
        try {
            $selfEmployed->update([
                'account_holder_name' => $request->input('account_holder_name'),
                'bank_account_number' => $request->input('bank_account_number'),
                'bic' => $request->input('bic'),
                'correspondent_account' => $request->input('correspondent_account'),
                'bank_name' => $request->input('bank_name')
            ]);

            return response()->json([
                'success' => true,
                'data' => $selfEmployed,
                'message' => 'Данные успешно обновлены'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении данных: ' . $e->getMessage()
            ], 500);
        }
    }
}

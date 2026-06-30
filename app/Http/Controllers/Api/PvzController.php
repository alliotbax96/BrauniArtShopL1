<?php

namespace App\Http\Controllers\Api;

use App\Models\UserPvz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PvzController extends ApiBaseController
{
    /**
     * Поиск ПВЗ (интеграция со службой доставки)
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        // Здесь должна быть интеграция с API службы доставки (Яндекс, СДЭК и т.д.)
        // Для примера возвращаем тестовые данные
        $pvzs = [
            [
                'id' => 'pvz_1',
                'code' => 'MSK001',
                'name' => 'ПВЗ на Тверской',
                'address' => 'г. Москва, ул. Тверская, д. 15',
                'latitude' => 55.761665,
                'longitude' => 37.609776,
                'work_time' => '09:00-21:00',
                'phone' => '+7 (495) 123-45-67'
            ],
            [
                'id' => 'pvz_2',
                'code' => 'MSK002',
                'name' => 'ПВЗ на Арбате',
                'address' => 'г. Москва, ул. Арбат, д. 22',
                'latitude' => 55.749472,
                'longitude' => 37.590902,
                'work_time' => '10:00-22:00',
                'phone' => '+7 (495) 765-43-21'
            ]
        ];

        return $this->successResponse($pvzs);
    }

    /**
     * Получение информации о ПВЗ
     */
    public function show(string $id)
    {
        $pvz = [
            'id' => $id,
            'code' => 'MSK001',
            'name' => 'ПВЗ на Тверской',
            'address' => 'г. Москва, ул. Тверская, д. 15',
            'latitude' => 55.761665,
            'longitude' => 37.609776,
            'work_time' => '09:00-21:00',
            'phone' => '+7 (495) 123-45-67',
            'description' => 'Вход со двора, 2 этаж',
            'photos' => []
        ];

        return $this->successResponse($pvz);
    }

    /**
     * Сохранение/выбор ПВЗ пользователем
     */
    public function selectPvz(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pvz_id' => 'required|string',
            'pvz_code' => 'required|string',
            'pvz_name' => 'required|string',
            'pvz_address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            DB::transaction(function () use ($request) {
                $userId = auth()->id();

                // Снимаем метку "основной" со всех ПВЗ
                UserPvz::where('user_id', $userId)->update(['last' => false]);

                // Ищем или создаём ПВЗ
                UserPvz::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'pvz' => $request->pvz_code
                    ],
                    [
                        'pvz_name' => $request->pvz_name,
                        'last' => true
                    ]
                );
            });

            $pvzs = UserPvz::where('user_id', auth()->id())
                ->orderBy('last', 'desc')
                ->get();

            return $this->successResponse($pvzs, 'ПВЗ выбран');

        } catch (\Exception $e) {
            return $this->errorResponse('Ошибка при выборе ПВЗ: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Список ПВЗ пользователя
     */
    public function userPvzs()
    {
        $pvzs = UserPvz::where('user_id', auth()->id())
            ->orderBy('last', 'desc')
            ->orderBy('pvz_name')
            ->get();

        return $this->successResponse($pvzs);
    }
}

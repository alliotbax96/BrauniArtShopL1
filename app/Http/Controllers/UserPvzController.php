<?php

namespace App\Http\Controllers;

use App\Models\UserPvz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class UserPvzController extends Controller
{
    public function updateOrCreate(Request $request)
    {
        $validated = $request->validate([
            'client_pvz' => 'required|string|max:255',
            'pvz_name' => 'required|string|max:255',
            'pvz_kode' => 'nullable|string|max:100'
        ]);

        $userId = Auth::id();
        $clientPvz = $validated['client_pvz'];
        $pvzName = $validated['pvz_name'];
        $citikode = $validated['pvz_kode'] ?? null;

        try {
            DB::transaction(function () use ($userId, $clientPvz, $pvzName, $citikode) {
                // Снимаем метку «основной» со всех ПВЗ пользователя
                UserPvz::where('user_id', $userId)
                    ->update(['last' => false]);

                // Ищем существующий ПВЗ
                $existingPvz = UserPvz::where('user_id', $userId)
                    ->where('pvz', $clientPvz)
                    ->first();

                if ($existingPvz) {
                    // Обновляем существующий ПВЗ и делаем его основным
                    $existingPvz->update([
                        'pvz_name' => $pvzName,
                        'citikode' => $citikode,
                        'last' => true
                    ]);
                } else {
                    // Создаём новый ПВЗ как основной
                    UserPvz::create([
                        'user_id' => $userId,
                        'pvz' => $clientPvz,
                        'pvz_name' => $pvzName,
                        'citikode' => $citikode,
                        'last' => true
                    ]);
                }
            });

            // Получаем обновлённый список ПВЗ ПОСЛЕ транзакции
            $pvzs = UserPvz::where('user_id', $userId)
                ->orderBy('last', 'desc')
                ->orderBy('pvz_name')
                ->get();

            // Определяем тип операции для сообщения
            $isUpdate = UserPvz::where('user_id', $userId)
                ->where('pvz', $clientPvz)
                ->where('last', true)
                ->exists();

            return response()->json([
                'success' => true,
                'message' => $isUpdate ? 'ПВЗ обновлён и установлен как основной' : 'Новый ПВЗ добавлен и установлен как основной',
                'pvzs' => $pvzs
            ]);
        } catch (QueryException $e) {
            \Log::error('SQL ошибка при обновлении ПВЗ: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка базы данных при сохранении ПВЗ'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Общая ошибка при обновлении ПВЗ: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении ПВЗ'
            ], 500);
        }
    }

// Метод для получения списка ПВЗ (для AJAX‑запросов)
    public function getList()
    {
        $userId = Auth::id();

        $pvzs = UserPvz::where('user_id', $userId)
            ->orderBy('last', 'desc')
            ->orderBy('pvz_name')
            ->get();

        return response()->json($pvzs);
    }
}

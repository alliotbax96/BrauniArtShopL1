<?php

namespace App\Http\Controllers\Dashboard\Settings;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\LegalEntityDetail;
use App\Models\Seller;
use App\Models\SellerContract;
use App\Models\SellerPvz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends BaseController
{
    public function index(Request $request) {
        if(!Auth::user()->groupInfo()->hasPermission('view_settings')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData(); // вызываем один раз
        $userLegalEnityDetail = LegalEntityDetail::where('user_id', Auth::id())->get();
        $seller = Auth::user()->getFirstSeller();
        $SellerLegalDetail = $seller->legalDetails()->get()->first();
        $contract = $seller->contacts()->where('seller_legal_details_id', $SellerLegalDetail->id)->first();
        return view('dashboard.index',['View' => 'dashboard.settings.index', 'title'=>'Настройки продавца | Единая система BaID', 'PageName'=>'Настройки продавца', 'InPageName'=>'Основные', 'userLegalEnityDetail' => $userLegalEnityDetail, 'SellerLegalDetail' => $SellerLegalDetail, 'seller' => $seller, 'contract' => $contract]);
    }

    public function update(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('edit_settings')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        try {
            $validated = $request->validate([
                'StoreName' => 'required|string|max:255',
                'companyPhone' => 'required|string|max:255',
                'companyEntityDetails' => 'required|integer',
            ], [
                'StoreName.required' => 'Поле "Название кабинета (Магазина)" обязательно для заполнения!',
                'companyPhone.required' => 'Поле "Номер телефона" обязательно для заполнения!',
                'companyEntityDetails.required' => 'Поле "Реквизиты" обязательно для заполнения',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Здесь должна быть логика обновления данных

            $seller = $user->getFirstSeller();
            $seller->name = $validated['StoreName'];
            $seller->phone = $validated['companyPhone'];
            $seller->save();

            $seller->legalDetails()->detach();
            $seller->legalDetails()->sync([$validated['companyEntityDetails']]);

            $contract = SellerContract::where('seller_id', $seller->id)->where('seller_legal_details_id', $validated['companyEntityDetails'])->get()->first();
            if (!$contract) {
                SellerContract::create([
                'seller_id' => $seller->id,
                'status' => null,
                'signed_status' => null,
                'seller_legal_details_id' => $validated['companyEntityDetails']
            ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Данные успешно обновлены',
                'data'=>$contract
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Получаем ошибки валидации
            $errors = $e->errors();

            // Формируем массив с названиями полей, не прошедших валидацию
            $failedFields = array_keys($errors);

            return response()->json([
                'success' => false,
                'errors' => $errors,
                'failed_fields' => $failedFields,
                'message' => 'Ошибка валидации данных'
            ]);
        } catch (\Exception $e) {
            // Общая обработка других возможных исключений
            return response()->json([
                'success' => false,
                'message' => 'Произошла непредвиденная ошибка: ' . $e->getMessage()
            ]);
        }
    }


    public function PvzUpdateOrCreate(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('edit_settings')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $validated = $request->validate([
            'client_pvz' => 'required|string|max:255',
            'pvz_name' => 'required|string|max:255',
            'pvz_kode' => 'nullable|string|max:100'
        ]);

        $sellerId = Auth::user()->getFirstSeller()->id;
        SellerPvz::where('seller_id', $sellerId)->delete();
        $clientPvz = $validated['client_pvz'];
        $pvzName = $validated['pvz_name'];
        $citikode = $validated['pvz_kode'] ?? null;

        try {
            DB::transaction(function () use ($sellerId, $clientPvz, $pvzName, $citikode) {
                // Снимаем метку «основной» со всех ПВЗ пользователя
                // Ищем существующий ПВЗ
                $existingPvz = SellerPvz::where('seller_id', $sellerId)
                    ->where('pvz', $clientPvz)
                    ->first();

                if ($existingPvz) {
                    // Обновляем существующий ПВЗ и делаем его основным
                    $existingPvz->update([
                        'pvz_name' => $pvzName,
                        'citikode' => $citikode,
                        'last' => true
                    ]);
                    $this->insertId = $existingPvz->id;
                } else {
                    // Создаём новый ПВЗ как основной
                    $newPvz = SellerPvz::create([
                        'seller_id' => $sellerId,
                        'pvz' => $clientPvz,
                        'pvz_name' => $pvzName,
                        'citikode' => $citikode,
                        'last' => true
                    ]);
                    $this->insertId = $newPvz->id;
                }
            });

            // Формируем HTML-разметку для ответа
            if ($this->insertId > 0) {
                $data = [
                    'type' => 'Пункт выдачи Яндекс',
                    'seller_id' => $sellerId,
                    'pvz' => $clientPvz,
                    'address' => $pvzName
                ];

                return response()->json([
                    'result' => true,
                    'pvz' => $pvzName,
                ]);
            } else {
                return response()->json([
                    'result' => false,
                    'pvz' => '',
                    'error' => 'Ошибка во время записи данных'
                ]);
            }
        } catch (QueryException $e) {
            \Log::error('SQL ошибка при обновлении ПВЗ: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'pvz' => '',
                'error' => 'SQL ошибка: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            \Log::error('Общая ошибка при обновлении ПВЗ: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'pvz' => '',
                'error' => 'Произошла ошибка при сохранении ПВЗ: ' . $e->getMessage()
            ]);
        }
    }

    public function PvzDelete(int $id)
    {
        if(!Auth::user()->groupInfo()->hasPermission('edit_settings')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        try {
            // Проверяем существование записи перед удалением
            $pvz = SellerPvz::find($id);
            if (!$pvz) {
                return response()->json([
                    'success' => false,
                    'error' => 'Пункт выдачи с указанным ID не найден'
                ], 404);
            }

            // Выполняем удаление
            $deleted = SellerPvz::destroy($id);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Пункт выдачи успешно удалён'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Не удалось удалить пункт выдачи'
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Ошибка базы данных при удалении PvZ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Ошибка базы данных: не удалось удалить пункт выдачи'
            ]);
        } catch (\Exception $e) {
            \Log::error('Неожиданная ошибка при удалении PvZ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Произошла непредвиденная ошибка при удалении'
            ]);
        }
    }
}

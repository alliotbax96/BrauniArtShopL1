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
use Nette\Schema\ValidationException;
class SettingsController extends BaseController
{
    public function index(Request $request) {
        if(!Auth::user()->groupInfo()->hasPermission('view_settings')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $this->shareCommonData(); // вызываем один раз
        $userLegalEnityDetail = LegalEntityDetail::where('user_id', Auth::id())->get();

        $seller = Auth::user()->getFirstSeller();

        if(Auth::user()->isAdmin()){
            $SellerList = Seller::all();
            $seller = isset($_COOKIE['selectedSellerId']) ? Seller::find($_COOKIE['selectedSellerId']) : Auth::user()->getFirstSeller();
        }

        $SelectSellerList = isset($SellerList) ? $SellerList : '';

        $SellerLegalDetail = $seller->legalDetails()->get()->first();
        $contract = $seller->contacts()->where('seller_legal_details_id', $SellerLegalDetail->id)->first();
        return view('dashboard.index',['View' => 'dashboard.settings.index', 'title'=>'Настройки продавца | Единая система BaID', 'PageName'=>'Настройки продавца', 'InPageName'=>'Основные', 'userLegalEnityDetail' => $userLegalEnityDetail, 'SellerLegalDetail' => $SellerLegalDetail, 'Seller' => $seller, 'contract' => $contract, 'SelectSellerList' => $SelectSellerList]);
    }

    public function BecomeASeller() {
        $this->shareCommonData();
        $seller = Auth::user()->getFirstSeller();
        if(isset($seller)) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $userLegalEnityDetail = LegalEntityDetail::where('user_id', Auth::id())->get();
        return view('dashboard.index',['View' => 'dashboard.settings.BecomeASeller', 'title'=>'Стать продавцом | Единая система BaID', 'PageName'=>'Стать продавцом', 'userLegalEnityDetail'=>$userLegalEnityDetail]);
    }

    public function BecomeASeller_process(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('BecomeASeller')->with(['message' => 'Пользователь не авторизован', 'data'=>$request->all()]);
            }

            $seller = $user->getFirstSeller();
            if ($seller) {
                return redirect()->route('BecomeASeller')->with(['message' => 'У вас уже есть аккаунт продавца', 'data'=>$request->all()]);
            }

            $validated = $request->validate([
                'StoreName' => 'required|string|max:255',
                'companyPhone' => 'required|string|max:255',
                'companyEntityDetails' => 'required|integer|exists:legal_entity_details,id',
            ], [
                'StoreName.required' => 'Поле "Название кабинета (Магазина)" обязательно для заполнения!',
                'companyPhone.required' => 'Поле "Номер телефона" обязательно для заполнения!',
                'companyEntityDetails.required' => 'Поле "Реквизиты" обязательно для заполнения',
                'companyEntityDetails.exists' => 'Указанные реквизиты не найдены в системе'
            ]);

            // Проверяем существование реквизитов
            $legalEntityDetail = LegalEntityDetail::find($validated['companyEntityDetails']);
            if (!$legalEntityDetail) {
                return redirect()->route('BecomeASeller')->with(['message' => 'Реквизиты компании не найдены', 'data'=>$request->all()]);
            }

            // Проверяем, связана ли уже эта запись реквизитов с каким‑либо продавцом
            $existingSeller = $legalEntityDetail->sellers()->first();
            if ($existingSeller) {
                return redirect()->route('BecomeASeller')->with([
                    'message' => 'Выбранные реквизиты уже связаны с другим продавцом. Выберите другие реквизиты или обратитесь в поддержку.',
                    'data' => $request->all()
                ]);
            }

            // Создаём продавца в транзакции для целостности данных
            $seller = DB::transaction(function () use ($validated, $legalEntityDetail, $user) {
                $seller = Seller::create([
                    'name' => $validated['StoreName'],
                    'phone' => $validated['companyPhone'],
                    'user_id' => $user->id,
                ]);

                // Связываем с реквизитами
                $seller->legalDetails()->sync([$validated['companyEntityDetails']]);

                // Создаём контракт
                SellerContract::create([
                    'seller_id' => $seller->id,
                    'status' => null,
                    'signed_status' => null,
                    'seller_legal_details_id' => $validated['companyEntityDetails']
                ]);

                $seller->users()->sync([$user->id]);
                $user->groups()->sync([2]);

                return $seller;
            });

            return redirect()->route('seller.settings.index')->with(['message' => 'Вы успешно зарегистрировались в качестве продавца! Осталось настроить логистику и подписать договор!']);

        } catch (ValidationException $e) {
            return redirect()->route('BecomeASeller')->with(['message' => 'Ошибка валидации данных', 'errors' => $e->errors(), 'data'=>$request->all()]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error in BecomeASeller_process: ' . $e->getMessage());
            return redirect()->route('BecomeASeller')->with(['message' => 'Произошла ошибка при сохранении данных. Попробуйте позже.', 'data'=>$request->all()]);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in BecomeASeller_process: ' . $e->getMessage());
            return redirect()->route('BecomeASeller')->with(['message' => 'Произошла непредвиденная ошибка. Обратитесь в поддержку.', 'data'=>$request->all()]);
        }
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

            // Проверяем существование реквизитов
            $legalEntityDetail = LegalEntityDetail::find($validated['companyEntityDetails']);
            if (!$legalEntityDetail) {
                return redirect()->route('BecomeASeller')->with(['message' => 'Реквизиты компании не найдены', 'data'=>$request->all()]);
            }

            // Проверяем, связана ли уже эта запись реквизитов с каким‑либо продавцом
            $existingSeller = $legalEntityDetail->sellers()->first();
            if ($existingSeller && Auth::user()->sellers()->first()->id != $existingSeller->id) {
                return redirect()->route('BecomeASeller')->with([
                    'message' => 'Выбранные реквизиты уже связаны с другим продавцом. Выберите другие реквизиты или обратитесь в поддержку.',
                    'data' => $request->all()
                ]);
            }

            // Здесь должна быть логика обновления данных

            $seller = $user->getFirstSeller();
            if(Auth::user()->isAdmin()){
                $seller = isset($_COOKIE['selectedSellerId']) ? Seller::find($_COOKIE['selectedSellerId']) : Auth::user()->getFirstSeller();
            }
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
        if(Auth::user()->isAdmin()){
            $sellerId = isset($_COOKIE['selectedSellerId']) ? $_COOKIE['selectedSellerId'] : Auth::user()->getFirstSeller();
        }
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

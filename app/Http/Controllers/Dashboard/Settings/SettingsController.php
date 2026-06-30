<?php

namespace App\Http\Controllers\Dashboard\Settings;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\LegalEntityDetail;
use App\Models\SelfEmployedRecord;
use App\Models\Seller;
use App\Models\SellerContract;
use App\Models\SellerPvz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nette\Schema\ValidationException;

class SettingsController extends BaseController
{
    public function index(Request $request)
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_settings')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $this->shareCommonData(); // вызываем один раз
        $userLegalEnityDetail = LegalEntityDetail::where('user_id', Auth::id())->get();

        $seller = Auth::user()->getFirstSeller();

        if (Auth::user()->isAdmin()) {
            $SellerList = Seller::all();
            $seller = isset($_COOKIE['selectedSellerId']) ? Seller::find($_COOKIE['selectedSellerId']) : Auth::user()->getFirstSeller();
        }

        $SelectSellerList = isset($SellerList) ? $SellerList : '';

        $SellerLegalDetail = $seller->legalDetails()->get()->first();
        $contract = isset($SellerLegalDetail) ? $seller->contacts()->where('seller_legal_details_id', $SellerLegalDetail->id)->first() : null;
        $selfEmployed = SelfEmployedRecord::where('user_id', Auth::id())->first();
        return view('dashboard.index', ['View' => 'dashboard.settings.index', 'title' => 'Настройки продавца | Единая система BaID', 'PageName' => 'Настройки продавца', 'InPageName' => 'Основные', 'userLegalEnityDetail' => $userLegalEnityDetail, 'SellerLegalDetail' => $SellerLegalDetail, 'Seller' => $seller, 'contract' => $contract, 'SelectSellerList' => $SelectSellerList, 'selfEmployed' => $selfEmployed]);
    }

    public function BecomeASeller()
    {
        $this->shareCommonData();
        $seller = Auth::user()->getFirstSeller();
        if (isset($seller)) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $userLegalEnityDetail = LegalEntityDetail::where('user_id', Auth::id())->get();
        $selfEmployed = SelfEmployedRecord::where('user_id', Auth::id())->first();
        return view('dashboard.index', ['View' => 'dashboard.settings.BecomeASeller', 'title' => 'Стать продавцом | Единая система BaID', 'PageName' => 'Стать продавцом', 'userLegalEnityDetail' => $userLegalEnityDetail, 'selfEmployed' => $selfEmployed]);
    }

    /**
     * Регистрация продавца
     */
    public function BecomeASeller_process(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Если у пользователя уже есть продавец — запрещаем повторную регистрацию
            $existingSeller = $user->getFirstSeller();
            if ($existingSeller) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас уже есть аккаунт продавца'
                ], 400);
            }

            $validated = $request->validate([
                'StoreName' => 'required|string|max:255',
                'companyPhone' => 'required|string|max:255',
                'companyEntityDetails' => 'required|string',
            ], [
                'StoreName.required' => 'Поле "Название кабинета (Магазина)" обязательно для заполнения!',
                'companyPhone.required' => 'Поле "Номер телефона" обязательно для заполнения!',
                'companyEntityDetails.required' => 'Поле "Реквизиты" обязательно для заполнения',
            ]);

            // Валидируем тип и существование реквизитов, получаем объект
            $legalEntityDetail = $this->resolveLegalEntity($validated['companyEntityDetails'], null);

            // Создаём продавца в транзакции
            $seller = DB::transaction(function () use ($validated, $legalEntityDetail, $user) {
                $seller = Seller::create([
                    'name'      => $validated['StoreName'],
                    'phone'     => $validated['companyPhone'],
                    'user_id'   => $user->id,
                ]);

                // Связываем с реквизитами
                switch ($validated['companyEntityDetails']) {
                    case 'selfpub':
                        // Никаких действий не требуется
                        break;

                    case 'selfEmployed':
                        $seller->selfEmployedRecords()->sync([$legalEntityDetail->id]);
                        break;

                    default:
                        $seller->legalDetails()->sync([$validated['companyEntityDetails']]);
                        SellerContract::create([
                            'seller_id'                => $seller->id,
                            'status'                    => null,
                            'signed_status'             => null,
                            'seller_legal_details_id'   => $validated['companyEntityDetails']
                        ]);
                        break;
                }

                $seller->users()->sync([$user->id]);

                // Осторожно: это меняет группу пользователя. Если это нежелательно — уберите или сделайте условным.
                $user->groups()->sync([2]);

                return $seller;
            });

            return response()->json([
                'success' => true,
                'message' => 'Вы успешно зарегистрировались в качестве продавца! Осталось настроить логистику и подписать договор!',
                'data'    => $seller
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Ошибка валидации данных',
                'errors'        => $e->errors(),
                'failed_fields' => array_keys($e->errors()),
            ], 422);
        } catch (QueryException $e) {
            Log::error('Database error in BecomeASeller_process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении данных. Попробуйте позже.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error in BecomeASeller_process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Произошла непредвиденная ошибка. Обратитесь в поддержку.'
            ], 500);
        }
    }

    /**
     * Обновление данных продавца
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        // Проверка прав (можно вынести в middleware)
        if (!$user->groupInfo()->hasPermission('edit_settings')) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для просмотра данного раздела!'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'StoreName' => 'required|string|max:255',
                'companyPhone' => 'required|string|max:255',
                'companyEntityDetails' => 'required|string',
            ], [
                'StoreName.required' => 'Поле "Название кабинета (Магазина)" обязательно для заполнения!',
                'companyPhone.required' => 'Поле "Номер телефона" обязательно для заполнения!',
                'companyEntityDetails.required' => 'Поле "Реквизиты" обязательно для заполнения',
            ]);

            // Определяем продавца
            $seller = $user->getFirstSeller();

            if ($user->isAdmin()) {
                // Используем сессию вместо кук
                $selectedSellerId = session('selected_seller_id');
                if ($selectedSellerId) {
                    $sellerCandidate = Seller::find($selectedSellerId);
                    if ($sellerCandidate && $sellerCandidate->users()->where('user_id', $user->id)->exists()) {
                        $seller = $sellerCandidate;
                    }
                }
            }

            if (!$seller) {
                return response()->json([
                    'success' => false,
                    'message' => 'Продавец не найден'
                ], 404);
            }

            // Разрешаем использовать те же реквизиты, что уже привязаны к этому продавцу
            $legalEntityDetail = $this->resolveLegalEntity($validated['companyEntityDetails'], $seller->id);

            DB::transaction(function () use ($validated, $legalEntityDetail, $seller) {
                $seller->name = $validated['StoreName'];
                $seller->phone = $validated['companyPhone'];
                $seller->save();

                // Очищаем связи
                $seller->legalDetails()->detach();
                $seller->selfEmployedRecords()->detach();

                switch ($validated['companyEntityDetails']) {
                    case 'selfpub':
                        // Никаких действий не требуется
                        break;

                    case 'selfEmployed':
                        $seller->selfEmployedRecords()->sync([$legalEntityDetail->id]);
                        break;

                    default:
                        $seller->legalDetails()->sync([$validated['companyEntityDetails']]);

                        // Создаём контракт, если его ещё нет
                        $contract = SellerContract::where([
                            'seller_id'               => $seller->id,
                            'seller_legal_details_id' => $validated['companyEntityDetails']
                        ])->first();

                        if (!$contract) {
                            SellerContract::create([
                                'seller_id'                 => $seller->id,
                                'status'                    => null,
                                'signed_status'             => null,
                                'seller_legal_details_id'   => $validated['companyEntityDetails']
                            ]);
                        }
                        break;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Данные успешно обновлены'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Ошибка валидации данных',
                'errors'        => $e->errors(),
                'failed_fields' => array_keys($e->errors()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in SellerController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Произошла непредвиденная ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Разрешает тип и существование реквизитов и проверяет их занятость.
     *
     * @param string $typeOrId Значение из формы (selfpub, selfEmployed, или ID записи)
     * @param int|null $sellerId ID продавца, которому разрешено использовать эти реквизиты (игнорируем проверку занятости для него)
     * @return object Объект реквизитов (LegalEntityDetail или SelfEmployedRecord)
     * @throws \Illuminate\Validation\ValidationException Если данные некорректны
     */
    private function resolveLegalEntity(string $typeOrId, ?int $sellerId): object
    {
        switch ($typeOrId) {
            case 'selfpub':
                // Для selfpub ничего не нужно, но формально возвращаем null или специальный маркер.
                // Так как дальше в коде мы не используем $legalEntityDetail для selfpub, можно выбросить исключение,
                // если логика требует всегда иметь объект. Здесь мы просто возвращаем null и обрабатываем это в switch.
                return (object)['type' => 'selfpub'];

            case 'selfEmployed':
                $record = SelfEmployedRecord::where('user_id', Auth::id())->first();
                if (!$record) {
                    throw ValidationException::withMessages([
                        'companyEntityDetails' => 'Реквизиты самозанятого не найдены'
                    ]);
                }

                // Проверяем занятость (игнорируя текущего продавца)
                $existingSeller = $record->sellers()->first();
                if ($existingSeller && $existingSeller->id !== $sellerId) {
                    throw ValidationException::withMessages([
                        'companyEntityDetails' => 'Выбранные реквизиты уже связаны с другим продавцом. Выберите другие реквизиты или обратитесь в поддержку.'
                    ]);
                }

                return $record;

            default:
                // Это ID записи LegalEntityDetail
                $detail = LegalEntityDetail::find($typeOrId);
                if (!$detail) {
                    throw ValidationException::withMessages([
                        'companyEntityDetails' => 'Указанные реквизиты не найдены в системе'
                    ]);
                }

                // Блокируем строку, чтобы избежать гонки данных
                $detail = LegalEntityDetail::lockForUpdate()->find($typeOrId);
                if (!$detail) {
                    // На случай, если строка была удалена между проверками
                    throw ValidationException::withMessages([
                        'companyEntityDetails' => 'Указанные реквизиты больше не доступны'
                    ]);
                }

                $existingSeller = $detail->sellers()->first();
                if ($existingSeller && $existingSeller->id !== $sellerId) {
                    throw ValidationException::withMessages([
                        'companyEntityDetails' => 'Выбранные реквизиты уже связаны с другим продавцом. Выберите другие реквизиты или обратитесь в поддержку.'
                    ]);
                }

                return $detail;
        }
    }

    public function PvzUpdateOrCreate(Request $request)
    {
        if (!Auth::user()->groupInfo()->hasPermission('edit_settings')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $validated = $request->validate([
            'client_pvz' => 'required|string|max:255',
            'pvz_name' => 'required|string|max:255',
            'pvz_kode' => 'nullable|string|max:100'
        ]);

        $sellerId = Auth::user()->getFirstSeller()->id;
        if (Auth::user()->isAdmin()) {
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
        if (!Auth::user()->groupInfo()->hasPermission('edit_settings')) {
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

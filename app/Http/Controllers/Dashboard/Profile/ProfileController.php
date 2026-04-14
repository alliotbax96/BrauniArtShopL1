<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\UserCard;
use App\Models\UserPvz;
use App\Services\TbankService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SocialAccount;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use App\Models\LegalEntityDetail;
use Illuminate\Database\QueryException;

class ProfileController extends BaseController
{
    public function index()
    {
        $this->shareCommonData(); // вызываем один раз
        $userPvzs = UserPvz::where('user_id', Auth::id())->get();
        $tbankService = new TbankService();
        $TBankResult = $tbankService->GetCards(
            customerKey: Auth::id(),
        );
        $userLegalEnityDetail = LegalEntityDetail::where('user_id', Auth::id())->get();
        $SocialAccounts = SocialAccount::getAllGroupedByProvider(Auth::id());
        return view('dashboard.index', ['View' => 'dashboard.profile.profile', 'title' => 'Учетная запись | Единая система BaID', 'PageName' => 'Профиль', 'userPvzs' => $userPvzs, 'Cards' => $TBankResult, 'SocialAccounts' => $SocialAccounts, 'userLegalEnityDetail' => $userLegalEnityDetail]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . Auth::id()
            ], [
                'name.required' => 'Поле "ФИО" обязательно для заполнения!',
                'email.required' => 'Поле "E-mail" обязательно для заполнения!',
                'email.email' => 'Поле "E-mail" должно содержать корректный адрес электронной почты',
                'email.unique' => 'Пользователь с таким адресом электронной почты уже существует'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            if ($user->update($validated)) {
                session()->put('user', $user->toArray());
                return response()->json(['result' => true]);
            } else {
                return response()->json([
                    'result' => false,
                    'message' => 'Не удалось обновить данные пользователя'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => false,
                'errors' => $e->errors(),
                'message' => 'Ошибка валидации данных'
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'result' => false,
                'message' => 'Произошла непредвиденная ошибка при обновлении профиля'
            ]);
        }
    }

    /**
     * Привязка Яндекс аккаунта к существующему пользователю
     */
    public function attachYandex()
    {
        $driver = Socialite::driver('yandex');
        // Добавляем redirect как параметр запроса
        $redirectUrl = route('profile.attach.yandex.callback');
        return $driver->with([
            'redirect_uri' => $redirectUrl,
        ])->redirect();
    }

    public function handleAttachYandexCallback()
    {
        try {
            // Получаем данные от Яндекс
            $yandexUser = Socialite::driver('yandex')->user();
            $userId = Auth::id();

            if (!$userId) {
                return redirect('/login')->with('error', 'Необходимо войти в систему');
            }

            // Логируем полученные данные
            \Log::info('Yandex callback received:', [
                'user_id' => $userId,
                'yandex_id' => $yandexUser->getId(),
                'email' => $yandexUser->getEmail(),
            ]);

            // Проверяем, не привязан ли уже этот аккаунт Яндекс
            $existing = SocialAccount::where('provider', 'yandex')
                ->where('provider_id', $yandexUser->getId())
                ->first();

            if ($existing) {
                if ($existing->user_id == $userId) {
                    // Аккаунт уже привязан к этому пользователю
                    return redirect('/profile')->with('success', 'Этот аккаунт Яндекс уже привязан к вашему профилю');
                } else {
                    // Аккаунт привязан к другому пользователю
                    return redirect('/profile')->with('error', 'Этот аккаунт Яндекс уже привязан к другому пользователю');
                }
            }

            // Проверяем, не привязан ли другой аккаунт Яндекс к этому пользователю (ограничение — один аккаунт на провайдера)
            $hasOtherYandex = SocialAccount::where('user_id', $userId)
                ->where('provider', 'yandex')
                ->exists();

            if ($hasOtherYandex) {
                return redirect('/profile')->with('error', 'У вас уже привязан аккаунт Яндекс. Сначала отвяжите его');
            }

            // Подготавливаем данные для записи
            $userData = [
                'user_id' => $userId,
                'provider' => 'yandex',
                'provider_id' => $yandexUser->getId(),
                'token' => $yandexUser->token ?? null,
                'refresh_token' => $yandexUser->refreshToken ?? null,
                'expires_in' => $yandexUser->expiresIn ?? null,
                'avatar' => $yandexUser->getAvatar() ?? null,
            ];

            // Дополнительная проверка: убеждаемся, что provider_id не пустой
            if (empty($userData['provider_id'])) {
                \Log::error('Empty provider_id in Yandex callback');
                return redirect('/profile')->with('error', 'Не удалось получить идентификатор аккаунта Яндекс');
            }

            // Создаём новую привязку
            SocialAccount::create($userData);

            return redirect('/profile')->with('success', 'Аккаунт Яндекс успешно привязан');

        } catch (\Exception $e) {
            return redirect('/profile')->with('error', 'Ошибка при привязке Яндекс аккаунта. Код ошибки: ' . $e->getCode());
        }
    }

    public function detachYandex(string $service)
    {
        try {
            // Проверяем, что сервис разрешён для отвязки
            $allowedServices = ['yandex']; // можно расширить список
            if (!in_array($service, $allowedServices)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Недопустимый сервис для отвязки'
                ], 400);
            }

            // Получаем авторизованного пользователя
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Ищем запись для удаления — только для текущего пользователя
            $socialAccount = SocialAccount::where('user_id', $user->id)
                ->where('provider', $service)
                ->first();

            if (!$socialAccount) {
                return response()->json([
                    'result' => true, // считаем успешным — аккаунта нет, значит, он уже отвязан
                    'message' => 'Аккаунт уже отвязан или не существует'
                ]);
            }

            // Удаляем запись
            $deleted = $socialAccount->delete();

            if ($deleted) {

                return response()->json([
                    'result' => true,
                    'message' => 'Аккаунт успешно отвязан'
                ]);
            } else {
                return response()->json([
                    'result' => false,
                    'message' => 'Не удалось отвязать аккаунт. Попробуйте позже'
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {

            return response()->json([
                'result' => false,
                'message' => 'Ошибка базы данных при отвязке аккаунта'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'result' => false,
                'message' => 'Произошла непредвиденная ошибка при отвязке профиля ' . $service
            ]);
        }
    }

    public function PvzUpdateOrCreate(Request $request)
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
                    $this->insertId = $existingPvz->id;
                } else {
                    // Создаём новый ПВЗ как основной
                    $newPvz = UserPvz::create([
                        'user_id' => $userId,
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
                    'user_id' => $userId,
                    'pvz' => $clientPvz,
                    'address' => $pvzName
                ];

                $result = '<div class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1">
                <div class="hstack me-4">
                    <div class="avatar-text">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div class="ms-4">
                        <span class="fw-bold mb-1 text-truncate-1-line">' . htmlspecialchars($data['type']) . '</span>
                        <div class="fs-12 text-muted text-truncate-1-line">' . htmlspecialchars($data['address']) . '</div>
                    </div>
                </div>
                <div class="form-check form-switch form-switch-sm"></div>
                </div>';

                return response()->json([
                    'result' => true,
                    'html' => $result
                ]);
            } else {
                return response()->json([
                    'result' => false,
                    'html' => '',
                    'error' => 'Ошибка во время записи данных'
                ]);
            }
        } catch (QueryException $e) {
            \Log::error('SQL ошибка при обновлении ПВЗ: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'html' => '',
                'error' => 'SQL ошибка: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            \Log::error('Общая ошибка при обновлении ПВЗ: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'html' => '',
                'error' => 'Произошла ошибка при сохранении ПВЗ: ' . $e->getMessage()
            ]);
        }
    }

    public function PvzDelete(int $id)
    {
        try {
            // Проверяем существование записи перед удалением
            $pvz = UserPvz::find($id);
            if (!$pvz) {
                return response()->json([
                    'success' => false,
                    'error' => 'Пункт выдачи с указанным ID не найден'
                ], 404);
            }

            // Выполняем удаление
            $deleted = UserPvz::destroy($id);

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

    public function LegalUpsertOrDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            // Валидация данных
            $validated = $request->validate([
                'id' => 'nullable|integer',
                'user_id' => 'required|integer',
                'legal_name' => 'required|string|max:255',
                'inn' => ['nullable', 'string', 'regex:/^(\d{10}|\d{12})$/'],
                'kpp' => ['nullable', 'string', 'regex:/^\d{9}$/'],
                'ogrn' => ['nullable', 'string', 'regex:/^(\d{13}|\d{15})$/'],
                'bank_name' => 'nullable|string|max:255',
                'bik' => 'nullable|string|size:9',
                'correspondent_account' => 'nullable|string|size:20',
                'account_number' => 'nullable|string|size:20',
                'director_name' => 'nullable|string|max:255',
                'is_vat_payer' => 'boolean',
            ], [
                'user_id.required' => 'ID пользователя обязателен',
                'legal_name.required' => 'Юридическое название обязательно',
                'inn.regex' => 'ИНН должен содержать 10 цифр (для юрлиц) или 12 цифр (для ИП)',
                'kpp.regex' => 'КПП должен содержать только цифры и быть длиной 9 символов',
                'ogrn.regex' => 'ОГРН должен содержать 13 цифр (для юрлиц) или 15 цифр (для ИП — ОГРНИП)',
                'bik.size' => 'БИК должен содержать 9 цифр',
                'correspondent_account.size' => 'Корр. счёт должен содержать 20 цифр',
                'account_number.size' => 'Расчётный счёт должен содержать 20 цифр'
            ]);

            // Определяем, создаём или обновляем запись
            $id = $request->input('id');

            if ($id) {
                // Обновляем существующую запись
                $legalEntityDetail = LegalEntityDetail::findOrFail($id);
                $legalEntityDetail->update($validated);
                $message = 'Данные юридического лица успешно обновлены.';
            } else {
                // Создаём новую запись
                $legalEntityDetail = LegalEntityDetail::create($validated);
                $message = 'Данные юридического лица успешно созданы.';
            }

            // Формируем HTML безопасным способом
            $html = $this->generateLegalEntityHtml($legalEntityDetail, $request->input('id'));

            return response()->json([
                'result' => true,
                'message' => $message,
                'html' => $html,
                'data' => $legalEntityDetail->toArray(),
                'id' => $request->input('id'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Ошибка валидации: проверьте правильность заполнения полей.',
                'errors' => $e->errors()
            ]);
        } catch (QueryException $e) {
            \Log::error('SQL ошибка при upsert LegalEntityDetail: ' . $e->getMessage());
            return response()->json([
                'result' => false,
                'message' => 'Произошла ошибка при сохранении данных. Попробуйте ещё раз.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Неожиданная ошибка при upsert LegalEntityDetail: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString()); // Дополнительная отладка
            return response()->json([
                'result' => false,
                'message' => 'Произошла непредвиденная ошибка. Обратитесь к администратору.'
            ]);
        }
    }

    /**
     * Генерирует HTML для отображения юридического лица
     */
    private function generateLegalEntityHtml(LegalEntityDetail $legalEntityDetail, int $id): string
    {
        $legalName = htmlspecialchars($legalEntityDetail->legal_name ?? '');
        $inn = htmlspecialchars($legalEntityDetail->inn ?? '');
        $ogrn = htmlspecialchars($legalEntityDetail->ogrn ?? '');

        // Формируем дополнительную информацию
        $additionalInfo = [];

        if (!$legalEntityDetail->isIndividualEntrepreneur()) {
            $kpp = htmlspecialchars($legalEntityDetail->kpp ?? '');
            $director = htmlspecialchars($legalEntityDetail->director_name ?? '');

            if ($kpp) {
                $additionalInfo[] = "КПП: $kpp";
            }
            if ($director) {
                $additionalInfo[] = "Генеральный директор: $director";
            }
        }

        if ($legalEntityDetail->isVatPayer()) {
            $additionalInfo[] = "Плательщик НДС";
        }

        $additionalText = !empty($additionalInfo) ? ', ' . implode(', ', $additionalInfo) : '';
        if($id){
            return <<<HTML
                       <span class="fw-bold mb-1 text-truncate-1-line">{$legalName}</span>
                       <div class="fs-12 text-muted text-truncate-1-line">ИНН: {$inn}, ОГРН: {$ogrn}{$additionalText}</div>
                      HTML;
        }
        return <<<HTML
                   <div class="hstack justify-content-between p-4 mb-3 border border-dashed border-gray-3 rounded-1 block-div">
                        <div class="hstack me-4">
                           <div class="avatar-text">
                               <i class="fa fa-building" aria-hidden="true"></i>
                           </div>
                           <div class="ms-4">
                               <span class="fw-bold mb-1 text-truncate-1-line">{$legalName}</span>
                               <div class="fs-12 text-muted text-truncate-1-line">ИНН: {$inn}, ОГРН: {$ogrn}{$additionalText}</div>
                           </div>
                       </div>
                        <div class="form-check form-switch form-switch-sm">
                          <label class="form-check-label fw-500 text-dark c-pointer" for="formSwitch2FA"></label>
                          <a href="#" class="edit_legal" data-id="{$legalEntityDetail->id}" style="text-decoration: none; color: black;">
                           <i class="fa fa-edit" aria-hidden="true"></i>
                          </a>

                          <label class="form-check-label fw-500 text-dark c-pointer" for="formSwitch2FA"></label>
                          <a href="#" class="del_legal" data-id="{$legalEntityDetail->id}" style="text-decoration: none; color: black;">
                           <i class="fa fa-trash" aria-hidden="true"></i>
                          </a>
                      </div>
                   </div>
                  HTML;
    }

    /**
     * Обработать удаление записи
     */
    public function LegalHandleDelete(int $id): \Illuminate\Http\JsonResponse
    {

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Не указан ID записи для удаления.'
            ]);
        }

        try {
            $legalEntityDetail = LegalEntityDetail::findOrFail($id);

            // Проверяем наличие связанных данных
            if ($this->hasRelatedData($legalEntityDetail)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно удалить запись: есть связанные данные.'
                ]);
            }

            $legalEntityDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Данные юридического лица успешно удалены.'
            ]);
        } catch (QueryException $e) {
            \Log::error('SQL ошибка при удалении LegalEntityDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при удалении данных. Попробуйте позже.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Неожиданная ошибка при удалении LegalEntityDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Произошла непредвиденная ошибка при удалении. Обратитесь к администратору.'
            ]);
        }
    }

    public function GetLegal(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $legalEntity = LegalEntityDetail::find($id);

            if (!$legalEntity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Юридическая сущность с указанным ID не найдена',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Данные успешно получены',
                'data' => $legalEntity
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error in GetLegal: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка базы данных',
                'error_code' => $e->getCode(),
                'data' => null
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in GetLegal: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла непредвиденная ошибка',
                'error_code' => $e->getCode(),
                'data' => null
            ]);
        }
    }
    /**
     * Проверить наличие связанных данных, препятствующих удалению
     */
    private function hasRelatedData(LegalEntityDetail $legalEntityDetail): bool
    {
        // Здесь можно добавить проверку на наличие связанных записей
        // Например, если есть связанные документы, транзакции и т. д.
        // В текущем примере просто возвращаем false — удаление разрешено
        return false;
    }
    public function AddPaymentCard() {
       $tbankService = new TbankService();
       $result = $tbankService->init('binding##'.time(), rand(1,10), Auth::id(),'Y', 'https://id.brauniart.shop/profile/pay');
       return redirect($result['PaymentURL']);
    }
    public function DeletePaymentCard(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $tbankService = new TbankService();

            // Обработка ошибки API отдельно
            try {
                $tbankService->RemoveCard($id, Auth::id());
            } catch (\Exception $apiException) {
                \Log::warning('T‑Bank API error (card may still be deleted locally)', [
                    'card_id' => $id,
                    'user_id' => Auth::id(),
                    'message' => $apiException->getMessage(),
                ]);
                // Продолжаем удаление из БД, даже если API дало сбой
            }

            UserCard::where('CardId', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Карта удалена (возможно, с ограничениями во внешней системе)',
            ]);
        } catch (\Exception $e) {
            \Log::error('Critical error during card deletion: ' . $e->getMessage(), [
                'card_id' => $id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Критическая ошибка при удалении карты.',
            ]);
        }
    }
}

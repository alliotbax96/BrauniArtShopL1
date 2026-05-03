<?php

namespace App\Http\Controllers\Dashboard\Quest;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\AdditionalService;
use App\Models\Timeslot;
use Illuminate\Http\Request;
use App\Models\Quest;
use Ramsey\Uuid\Type\Time;
use Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class QuestController extends BaseController
{
    public function index()
    {
        $this->shareCommonData(); // вызываем один раз

        return view('dashboard.index', [
            'View' => 'dashboard.quest.index',
            'title' => 'Управление квестами | Единая система BaID',
            'PageName' => 'Ассортимент',
            'InPageName' => 'Квесты',
            'CreateObject' => '/seller/quests/create'
        ]);
    }

    public function ajax(Request $request)
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_quests')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        try {
            $filters = [
                'search' => $request->input('search'),
            ];

            // Параметры пагинации DataTable
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);

            // Основной запрос для данных
            $query = Quest::query();

            if (!empty($filters['search'])) {
                $query->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            }

            $totalRecords = $query->count();

            // Получаем данные с пагинацией
            $questQuery = clone $query;
            $quests = $questQuery
                ->skip($start)
                ->take($length)
                ->get();

            // Форматируем данные для DataTable
            $formattedQuests = [];
            foreach ($quests as $quest) {
                try {
                    // Формируем HTML для столбца «Наименование»
                    $imagePath = $quest->image_path;
                    $image = null;
                    if (isset($imagePath)) {
                        $image = $this->getFirstImageUrlFromS3('uploads/quests/' . $imagePath);
                    }
                    $hasImage = !empty($imagePath);
                    $imageHtml = $hasImage
                        ? '<img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($quest->title) . '" class="img-fluid" style="width: 40px; height: 40px; object-fit: cover;">'
                        : '<div class="avatar-placeholder bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">Нет</div>';

                    $nameHtml = '
                                 <div class="hstack gap-4">
                                     <div class="avatar-image border-0">' . $imageHtml . '</div>
                                     <div>
                                             <a href="/seller/quests/' . $quest->id . '" class="text-truncate-2-line">' .
                        htmlspecialchars($quest->title ?? 'Не указано') . '</a>
                                             <div class="project-list-action fs-12 d-flex align-items-center gap-3 mt-2">
                                                 <a href="/seller/quests/' . $quest->id . '">Изменить</a>
                                             <span class="vr text-muted"></span>
                                             <a href="javascript:void(0);" class="text-danger delete_quest" data-id="' . $quest->id . '">Удалить</a>
                                         </div>
                                     </div>
                                 </div>';

                    $formattedQuests[] = [
                        'id' => $quest->id,
                        'name' => $nameHtml,
                        'type' => htmlspecialchars($quest->type ?? 'Не указан'),
                        'price' => ($quest->base_price ?? 0) . ' руб.',
                        'difficulty' => htmlspecialchars($quest->difficulty ?? 'Не указана'),
                        'duration' => ($quest->duration ?? 0) . ' мин.',
                        'min_age' => ($quest->min_age ?? 0) . '+',
                        'players' => ($quest->min_players ?? 0) . '-' . ($quest->max_players ?? 0),
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error formatting quest data: ' . $e->getMessage());
                    // Заглушка при ошибке
                    $formattedQuests[] = [
                        'id' => $quest->id,
                        'name' => 'Ошибка данных',
                        'type' => 'Не указан',
                        'price' => '0 руб.',
                        'difficulty' => 'Не указана',
                        'duration' => '0 мин.',
                        'min_age' => '0 лет',
                        'players' => '0-0',
                    ];
                }
            }

            // Типы квестов для фильтров
            $types = Quest::select('type')
                ->distinct()
                ->orderBy('type')
                ->pluck('type')
                ->toArray();

            // Уровни сложности для фильтров
            $difficulties = Quest::select('difficulty')
                ->distinct()
                ->orderBy('difficulty')
                ->pluck('difficulty')
                ->toArray();

            return response()->json([
                'draw' => (int)$draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$totalRecords,
                'data' => $formattedQuests,
                'filters' => $filters,
                'types' => $types,
                'difficulties' => $difficulties,
            ]);

        } catch (\Exception $e) {
            \Log::error('DataTable error: ' . $e->getMessage());

            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Произошла ошибка при загрузке данных'
            ], 500);
        }
    }

    private function getFirstImageUrlFromS3(?string $folderPath): ?string
    {
        if (empty($folderPath)) {
            return null;
        }

        // Убедимся, что путь заканчивается на `/`
        if (substr($folderPath, -1) !== '/') {
            $folderPath .= '/';
        }

        try {
            // Получаем список файлов в папке
            $files = Storage::disk('s3')->files($folderPath);

            // Фильтруем только изображения
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $images = array_filter($files, function ($file) use ($imageExtensions) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                return in_array($extension, $imageExtensions);
            });

            if (!empty($images)) {
                // Берём первый файл из списка
                $firstImagePath = reset($images);
                // Генерируем публичный URL
                return Storage::disk('s3')->url($firstImagePath);
            }

            return null; // Папка пуста или нет изображений
        } catch (\Exception $e) {
            \Log::error('S3 image fetch error: ' . $e->getMessage());
            return null;
        }
    }

    public function create()
    {
        $this->shareCommonData();
        return view('dashboard.index', [
            'View' => 'dashboard.quest.create',
            'title' => 'Создание квеста | Единая система BaID',
            'PageName' => 'Ассортимент',
            'InPageName' => 'Создание квеста',
        ]);
    }

    public function edit(int $id)
    {
        $this->shareCommonData();
        $quest = Quest::where('id', $id)->firstOrFail();
        return view('dashboard.index', [
            'View' => 'dashboard.quest.edit',
            'quest' => $quest,
            'title' => 'Редактирование ' . $quest->title . ' | Единая система BaID',
            'PageName' => 'Ассортимент',
            'InPageName' => $quest->title,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'images' => 'nullable|array',
                'images.*' => 'string',
                'title' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'station' => 'required|string|max:255',
                'type' => 'required|in:quest,performance',
                'difficulty' => 'required|integer|between:1,3',
                'fear_level' => 'required|integer|between:1,3',
                'duration' => 'required|integer|min:1',
                'min_age' => 'required|integer|min:0',
                'base_price' => 'required|numeric|min:0',
                'base_player_count' => 'required|integer|min:0',
                'additional_player_price' => 'required|numeric|min:0',
                'min_players' => 'required|integer|min:1',
                'max_players' => 'required|integer|gt:min_players',
                'description' => 'required|string',
                'features' => 'required|array',
                'timeslots' => 'required|array|min:1',
                'timeslots.*.id' => 'integer',
                'timeslots.*.day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'timeslots.*.start_time' => 'required|date_format:H:i',
                'timeslots.*.price' => 'required|numeric|min:0',
                'additional_services' => 'nullable|array',
                'additional_services.*.id' => 'integer',
                'additional_services.*.name' => 'required_with:additional_services|string|max:255',
                'additional_services.*.price' => 'required_with:additional_services|numeric|min:0',
            ], [
                // Общие сообщения для массивов
                'images.array' => 'Поле "Изображения" должно быть массивом',
                'images.*.string' => 'Каждый элемент массива "Изображения" должен быть строкой',

                // Сообщения для обязательных полей
                'title.required' => 'Название является обязательным полем',
                'address.required' => 'Адрес является обязательным полем',
                'station.required' => 'Станция метро является обязательным полем, если метро рядом нет впишите слов "нет"',
                'type.required' => 'Тип квеста является обязательным полем',
                'difficulty.required' => 'Уровень сложности является обязательным полем',
                'fear_level.required' => 'Уровень страха является обязательным полем',
                'duration.required' => 'Длительность является обязательным полем',
                'min_age.required' => 'Минимальный возраст является обязательным полем',
                'base_price.required' => 'Базовая цена является обязательным полем',
                'base_player_count.required' => 'Количество игроков включенных в базовую цену является обязательным полем',
                'additional_player_price.required' => 'Стоимость за дополнительного игрока является обязательным полем',
                'min_players.required' => 'Минимальное количество игроков является обязательным полем',
                'max_players.required' => 'Максимальное количество игроков является обязательным полем',
                'description.required' => 'Описание является обязательным полем',
                'features.required' => 'Особенности являются обязательным полем',
                'timeslots.required' => 'Временные слоты являются обязательным полем',

                // Сообщения для типов данных
                'title.string' => 'Название должно быть текстовым значением',
                'type.in' => 'Недопустимый тип квеста. Допустимые значения: quest, performance',
                'difficulty.integer' => 'Уровень сложности должен быть целым числом',
                'fear_level.integer' => 'Уровень страха должен быть целым числом',
                'duration.integer' => 'Длительность должна быть целым числом',
                'min_age.integer' => 'Минимальный возраст должен быть целым числом',
                'base_price.numeric' => 'Базовая цена должна быть числовым значением',
                'base_player_count.integer' => 'Количество игроков включенных в базовую цену должно быть целым числом',
                'additional_player_price.integer' => 'Стоимость за дополнительного игрока должна быть целым числом',
                'min_players.integer' => 'Минимальное количество игроков должно быть целым числом',
                'max_players.integer' => 'Максимальное количество игроков должно быть целым числом',
                'description.string' => 'Описание должно быть текстовым значением',
                'features.array' => 'Особенности должны быть представлены в виде массива',
                'timeslots.array' => 'Должен быть хотя бы один временной слот',


                // Ограничения по значениям
                'title.max' => 'Название не должно превышать 255 символов',
                'difficulty.between' => 'Уровень сложности должен быть от 1 до 3',
                'fear_level.between' => 'Уровень страха должен быть от 1 до 3',
                'duration.min' => 'Длительность должна быть не менее 1 минуты',
                'min_age.min' => 'Минимальный возраст не может быть отрицательным',
                'base_price.min' => 'Цена не может быть отрицательной',
                'min_players.min' => 'Минимальное количество игроков должно быть не менее 1',
                'max_players.gt' => 'Максимальное количество игроков должно быть больше минимального количества',
                'timeslots.min' => 'Должен быть указан хотя бы один временной слот',

                // Валидация временных слотов
                'timeslots.*.day_of_week.required' => 'День недели является обязательным для каждого временного слота',
                'timeslots.*.day_of_week.in' => 'Недопустимое значение дня недели. Допустимые значения: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday',
                'timeslots.*.start_time.required' => 'Время начала является обязательным для каждого временного слота',
                'timeslots.*.start_time.date_format' => 'Время должно быть в формате ЧЧ:ММ (например, 14:30)',
                'timeslots.*.price.required' => 'Цена является обязательной для каждого временного слота',
                'timeslots.*.price.numeric' => 'Цена временного слота должна быть числовым значением',
                'timeslots.*.price.min' => 'Цена временного слота не может быть отрицательной',

                // Дополнительные услуги
                'additional_services.array' => 'Дополнительные услуги должны быть представлены в виде массива',
                'additional_services.*.name.required_with' => 'Название дополнительной услуги является обязательным, если указаны дополнительные услуги',
                'additional_services.*.name.string' => 'Название дополнительной услуги должно быть текстовым значением',
                'additional_services.*.name.max' => 'Название дополнительной услуги не должно превышать 255 символов',
                'additional_services.*.price.required_with' => 'Цена дополнительной услуги является обязательной, если указаны дополнительные услуги',
                'additional_services.*.price.numeric' => 'Цена дополнительной услуги должна быть числовым значением',
                'additional_services.*.price.min' => 'Цена дополнительной услуги не может быть отрицательной',
            ]);


            // Начинаем транзакцию для согласованности данных
            DB::transaction(function () use ($validated, $request) {
                $sellerId = Auth::user()->getFirstSeller()->id;
                $quest = Quest::create([
                    'seller_id' => $sellerId,
                    'title' => $validated['title'],
                    'address' => 'required|string|max:255',
                    'station' => 'required|string|max:255',
                    'type' => $validated['type'],
                    'difficulty' => $validated['difficulty'],
                    'fear_level' => $validated['fear_level'],
                    'duration' => $validated['duration'],
                    'min_age' => $validated['min_age'],
                    'base_price' => $validated['base_price'],
                    'base_player_count' => $validated['base_player_count'],
                    'additional_player_price' => $validated['additional_player_price'],
                    'min_players' => $validated['min_players'],
                    'max_players' => $validated['max_players'],
                    'description' => $validated['description'],
                    'features' => $validated['features'],
                ]);

                $quest->update(['image_path' => $quest->id]);

                // Обрабатываем изображения, если они есть
                if (!empty($validated['images'])) {
                    $this->updateQuestImages($quest->id, $validated['images']);
                }

                // Создаём таймслоты
                foreach ($validated['timeslots'] as $timeslot) {
                    Timeslot::create([
                        'quest_id' => $quest->id,
                        'day_of_week' => $timeslot['day_of_week'],
                        'start_time' => $timeslot['start_time'],
                        'price' => $timeslot['price'],
                    ]);
                }

                // Создаём дополнительные услуги, если есть
                if (!empty($validated['additional_services'])) {
                    foreach ($validated['additional_services'] as $service) {
                        AdditionalService::create([
                            'quest_id' => $quest->id,
                            'name' => $service['name'],
                            'price' => $service['price'],
                        ]);
                    }
                }

                session('id', $quest->id);
            });

            return response()->json([
                'success' => true,
                'message' => 'Квест успешно создан',
                'data' => ['quest_id' => session('id')]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Quest creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании квеста. Пожалуйста, попробуйте ещё раз или обратитесь в поддержку.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(int $id, Request $request)
    {
        $quest = Quest::where('id', $id)->firstOrFail();
        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Квест не найден',
                'errors' => ''
            ], 404);
        }

        $validated = $request->validate([
            'images' => 'nullable|array',
            'images.*' => 'string',
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'station' => 'required|string|max:255',
            'type' => 'required|in:quest,performance',
            'difficulty' => 'required|integer|between:1,3',
            'fear_level' => 'required|integer|between:1,3',
            'duration' => 'required|integer|min:1',
            'min_age' => 'required|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'base_player_count' => 'required|integer|min:0',
            'additional_player_price' => 'required|numeric|min:0',
            'min_players' => 'required|integer|min:1',
            'max_players' => 'required|integer|gt:min_players',
            'description' => 'required|string',
            'features' => 'required|array',
            'timeslots' => 'required|array|min:1',
            'timeslots.*.id' => 'integer',
            'timeslots.*.day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'timeslots.*.start_time' => 'required|date_format:H:i',
            'timeslots.*.price' => 'required|numeric|min:0',
            'additional_services' => 'nullable|array',
            'additional_services.*.id' => 'integer',
            'additional_services.*.name' => 'required_with:additional_services|string|max:255',
            'additional_services.*.price' => 'required_with:additional_services|numeric|min:0',
        ], [
            // Общие сообщения для массивов
            'images.array' => 'Поле "Изображения" должно быть массивом',
            'images.*.string' => 'Каждый элемент массива "Изображения" должен быть строкой',

            // Сообщения для обязательных полей
            'title.required' => 'Название является обязательным полем',
            'address.required' => 'Адрес является обязательным полем',
            'station.required' => 'Станция метро является обязательным полем, если метро рядом нет впишите слов "нет"',
            'type.required' => 'Тип квеста является обязательным полем',
            'difficulty.required' => 'Уровень сложности является обязательным полем',
            'fear_level.required' => 'Уровень страха является обязательным полем',
            'duration.required' => 'Длительность является обязательным полем',
            'min_age.required' => 'Минимальный возраст является обязательным полем',
            'base_price.required' => 'Базовая цена является обязательным полем',
            'base_player_count.required' => 'Количество игроков включенных в базовую цену является обязательным полем',
            'additional_player_price.required' => 'Стоимость за дополнительного игрока является обязательным полем',
            'min_players.required' => 'Минимальное количество игроков является обязательным полем',
            'max_players.required' => 'Максимальное количество игроков является обязательным полем',
            'description.required' => 'Описание является обязательным полем',
            'features.required' => 'Особенности являются обязательным полем',
            'timeslots.required' => 'Временные слоты являются обязательным полем',

            // Сообщения для типов данных
            'title.string' => 'Название должно быть текстовым значением',
            'type.in' => 'Недопустимый тип квеста. Допустимые значения: quest, performance',
            'difficulty.integer' => 'Уровень сложности должен быть целым числом',
            'fear_level.integer' => 'Уровень страха должен быть целым числом',
            'duration.integer' => 'Длительность должна быть целым числом',
            'min_age.integer' => 'Минимальный возраст должен быть целым числом',
            'base_price.numeric' => 'Базовая цена должна быть числовым значением',
            'base_player_count.integer' => 'Количество игроков включенных в базовую цену должно быть целым числом',
            'additional_player_price.integer' => 'Стоимость за дополнительного игрока должна быть целым числом',
            'min_players.integer' => 'Минимальное количество игроков должно быть целым числом',
            'max_players.integer' => 'Максимальное количество игроков должно быть целым числом',
            'description.string' => 'Описание должно быть текстовым значением',
            'features.array' => 'Особенности должны быть представлены в виде массива',
            'timeslots.array' => 'Должен быть хотя бы один временной слот',


            // Ограничения по значениям
            'title.max' => 'Название не должно превышать 255 символов',
            'difficulty.between' => 'Уровень сложности должен быть от 1 до 3',
            'fear_level.between' => 'Уровень страха должен быть от 1 до 3',
            'duration.min' => 'Длительность должна быть не менее 1 минуты',
            'min_age.min' => 'Минимальный возраст не может быть отрицательным',
            'base_price.min' => 'Цена не может быть отрицательной',
            'min_players.min' => 'Минимальное количество игроков должно быть не менее 1',
            'max_players.gt' => 'Максимальное количество игроков должно быть больше минимального количества',
            'timeslots.min' => 'Должен быть указан хотя бы один временной слот',

            // Валидация временных слотов
            'timeslots.*.day_of_week.required' => 'День недели является обязательным для каждого временного слота',
            'timeslots.*.day_of_week.in' => 'Недопустимое значение дня недели. Допустимые значения: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday',
            'timeslots.*.start_time.required' => 'Время начала является обязательным для каждого временного слота',
            'timeslots.*.start_time.date_format' => 'Время должно быть в формате ЧЧ:ММ (например, 14:30)',
            'timeslots.*.price.required' => 'Цена является обязательной для каждого временного слота',
            'timeslots.*.price.numeric' => 'Цена временного слота должна быть числовым значением',
            'timeslots.*.price.min' => 'Цена временного слота не может быть отрицательной',

            // Дополнительные услуги
            'additional_services.array' => 'Дополнительные услуги должны быть представлены в виде массива',
            'additional_services.*.name.required_with' => 'Название дополнительной услуги является обязательным, если указаны дополнительные услуги',
            'additional_services.*.name.string' => 'Название дополнительной услуги должно быть текстовым значением',
            'additional_services.*.name.max' => 'Название дополнительной услуги не должно превышать 255 символов',
            'additional_services.*.price.required_with' => 'Цена дополнительной услуги является обязательной, если указаны дополнительные услуги',
            'additional_services.*.price.numeric' => 'Цена дополнительной услуги должна быть числовым значением',
            'additional_services.*.price.min' => 'Цена дополнительной услуги не может быть отрицательной',
        ]);


        // Начинаем транзакцию для согласованности данных
        DB::transaction(function () use ($validated, $request, $quest) {
            $sellerId = Auth::user()->getFirstSeller()->id;
            $quest->update([
                'seller_id' => $sellerId,
                'title' => $validated['title'],
                'address' => $validated['address'],
                'station' => $validated['station'],
                'type' => $validated['type'],
                'difficulty' => $validated['difficulty'],
                'fear_level' => $validated['fear_level'],
                'duration' => $validated['duration'],
                'min_age' => $validated['min_age'],
                'base_price' => $validated['base_price'],
                'base_player_count' => $validated['base_player_count'],
                'additional_player_price' => $validated['additional_player_price'],
                'min_players' => $validated['min_players'],
                'max_players' => $validated['max_players'],
                'description' => $validated['description'],
                'features' => $validated['features'],
            ]);

            // Обрабатываем изображения, если они есть
            if (!empty($validated['images'])) {
                $this->updateQuestImages($quest->id, $validated['images']);
            }

            // Собираем ID из входящих данных
            $inputIds = array_column($validated['timeslots'], 'id');

            // Обновляем или создаём таймслоты
            foreach ($validated['timeslots'] as $timeslot) {
                $timeslotId = $timeslot['id'] ?? null;
                Timeslot::updateOrCreate(
                    [
                        'id' => $timeslotId,
                        'quest_id' => $quest->id,
                    ],
                    [
                        'day_of_week' => $timeslot['day_of_week'],
                        'start_time' => $timeslot['start_time'],
                        'price' => $timeslot['price'],
                    ]
                );
            }

            // Удаляем все таймслоты квеста, которых нет во входящих данных
            Timeslot::where('quest_id', $quest->id)
                ->whereNotIn('id', $inputIds)
                ->delete();

            if (!empty($validated['additional_services'])) {
                // Получаем все текущие дополнительные услуги для квеста
                $existingServiceIds = AdditionalService::where('quest_id', $quest->id)
                    ->pluck('id')
                    ->toArray();

                $processedIds = [];

                foreach ($validated['additional_services'] as $service) {

                    $serviceId = $service['id'] ?? null;
                    $as = AdditionalService::where('id', $serviceId)->first();

                    if ($as) {
                        // Обновляем существующую услугу
                        $as->update([
                            'name' => $service['name'],
                            'price' => $service['price'],
                        ]);
                        $processedIds[] = $service['id'];
                    } else {
                        // Создаём новую услугу
                        $newAs = AdditionalService::create([
                            'quest_id' => $quest->id,
                            'name' => $service['name'],
                            'price' => $service['price'],
                        ]);
                        $processedIds[] = $newAs->id;
                    }
                }

                // Удаляем услуги, которых нет в обновлённых данных
                $idsToDelete = array_diff($existingServiceIds, $processedIds);
                if (!empty($idsToDelete)) {
                    AdditionalService::whereIn('id', $idsToDelete)->delete();
                }
            } else {
                // Если массив пуст — удаляем все дополнительные услуги для этого квеста
                AdditionalService::where('quest_id', $quest->id)->delete();
            }

            session('id', $quest->id);
        });

        return response()->json([
            'success' => true,
            'message' => 'Квест успешно создан',
            'data' => ['quest_id' => session('id')]
        ], 201);
    }

    private function updateQuestImages(int $questId, array $newImagePaths): string
    {
        // Формируем путь к папке квеста
        $questFolder = "uploads/quests/{$questId}";

        try {
            // Проверяем, существует ли папка для квеста в S3. Если нет — создаём
            if (!Storage::disk('s3')->exists($questFolder)) {
                Storage::disk('s3')->makeDirectory($questFolder);
            }

            // Получаем все текущие изображения в папке квеста (исключая возможные подпапки)
            $currentImages = Storage::disk('s3')
                ->files($questFolder); // files() возвращает только файлы в указанной директории

            // Удаляем старые изображения, которых нет в новом списке
            foreach ($currentImages as $currentImagePath) {
                $shouldDelete = true;
                foreach ($newImagePaths as $newImagePath) {
                    // Сравниваем только имена файлов, а не полные пути
                    if (basename($currentImagePath) === basename($newImagePath)) {
                        $shouldDelete = false;
                        break;
                    }
                }
                if ($shouldDelete) {
//                    Storage::disk('s3')->delete($currentImagePath);
                }
            }

            // Добавляем новые изображения в папку квеста
//            foreach ($newImagePaths as $index => $tempPath) {
//                try {
//                    // Получаем расширение файла
//                    $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
//                    // Генерируем новое имя файла с уникальным идентификатором в папке квеста
//                    $newFilename = "{$questFolder}/" . Str::random(12) . ".{$extension}";
//
//                    // Перемещаем файл из временной папки в папку квеста
//                    if (Storage::disk('s3')->exists($tempPath)) {
//                        Storage::disk('s3')->move($tempPath, $newFilename);
//                    } else {
//                        throw new \Exception("Файл не найден в S3: {$tempPath}");
//                    }
//                } catch (\Exception $e) {
//                    \Log::error("Image move error for quest {$questId}: " . $e->getMessage());
//                    throw $e;
//                }
//            }

            return $questFolder;
        } catch (\Exception $e) {
            \Log::error("Quest images update failed for quest {$questId}: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteQuest(int $questId): void
    {
        $questFolder = "uploads/quests/{$questId}";
    }

}

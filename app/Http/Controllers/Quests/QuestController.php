<?php

namespace App\Http\Controllers\Quests;

use App\Http\Controllers\BaseController;
use App\Models\AdditionalService;
use App\Models\Quest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

class QuestController extends BaseController
{
    public function index(Request $request)
    {
        $this->shareCommonData(); // вызываем один раз
        Cookie::queue('ShopMode', 4);

        $filters = [
            'type' => $request->input('type'),
            'difficulty' => $request->input('difficulty'),
            'fear_level' => $request->input('fear_level'),
            'min_players' => $request->input('min_players'),
            'max_players' => $request->input('max_players'),
            'min_age' => $request->input('min_age'),
            'duration' => $request->input('duration'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'search' => $request->input('search'),
        ];

        // Валидация цен
        $minPrice = is_numeric($filters['min_price']) ? (float)$filters['min_price'] : null;
        $maxPrice = is_numeric($filters['max_price']) ? (float)$filters['max_price'] : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $filters['min_price'] = $minPrice;
        $filters['max_price'] = $maxPrice;

        // Валидация количества игроков
        $minPlayers = is_numeric($filters['min_players']) ? (int)$filters['min_players'] : null;
        $maxPlayers = is_numeric($filters['max_players']) ? (int)$filters['max_players'] : null;

        if ($minPlayers !== null && $maxPlayers !== null && $minPlayers > $maxPlayers) {
            [$minPlayers, $maxPlayers] = [$maxPlayers, $minPlayers];
        }

        $filters['min_players'] = $minPlayers;
        $filters['max_players'] = $maxPlayers;

        // Количество квестов на странице
        $perPage = $request->input('perPage', 12);
        $validPerPage = in_array($perPage, [8, 12, 16, 20, 24]) ? $perPage : 12;

        // Применяем фильтр условий
        $quests = Quest::query()
            ->with(['seller', 'timeslots'])
            ->filter($filters)
            ->paginate($validPerPage);

        // Данные для фильтров
        $types = Quest::select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        $difficulties = Quest::select('difficulty')
            ->distinct()
            ->orderBy('difficulty')
            ->pluck('difficulty');

        $fearLevels = Quest::select('fear_level')
            ->distinct()
            ->orderBy('fear_level')
            ->pluck('fear_level');

        $durations = Quest::select('duration')
            ->distinct()
            ->orderBy('duration')
            ->pluck('duration');

        $ageGroups = [
            '12-99' => '12+',
            '16-99' => '16+',
            '18-99' => '18+'
        ];

        // --- НАЧАЛО ДОБАВЛЕННОГО КОДА ДЛЯ SEO ---
        // Формируем title с учётом фильтров
        $titleParts = ['Квесты'];

        if (!empty($filters['search'])) {
            $titleParts[] = 'по запросу «' . $filters['search'] . '»';
        }

        if (!empty($filters['type'])) {
            $typeNames = [
                'quest' => 'квесты',
                'performance' => 'перфомансы'
            ];
            $typeName = $typeNames[$filters['type']] ?? $filters['type'];
            $titleParts[] = 'типа «' . $typeName . '»';
        }

        if (!empty($filters['difficulty'])) {
            $difficultyNames = [
                'easy' => 'лёгкие',
                'medium' => 'средней сложности',
                'hard' => 'сложные'
            ];
            $difficultyName = $difficultyNames[$filters['difficulty']] ?? $filters['difficulty'];
            $titleParts[] = $difficultyName;
        }

        if (!empty($filters['fear_level'])) {
            $fearNames = [
                'low' => 'с низким уровнем страха',
                'medium' => 'со средним уровнем страха',
                'high' => 'с высоким уровнем страха'
            ];
            $fearName = $fearNames[$filters['fear_level']] ?? $filters['fear_level'];
            $titleParts[] = $fearName;
        }

        $title = implode(' ', $titleParts) . ' | Брауни Арт — квесты и перформансы в России';

        // Формируем description с учётом фильтров
        $descriptionParts = ['Большой выбор квестов и перформансов'];

        if (!empty($filters['search'])) {
            $descriptionParts[] = 'по запросу «' . $filters['search'] . '»';
        }

        if (!empty($filters['type'])) {
            $typeDesc = $filters['type'] === 'quest' ? 'квестов' : 'перформансов';
            $descriptionParts[] = 'разных ' . $typeDesc;
        }

        if (!empty($filters['difficulty'])) {
            $descriptionParts[] = 'разной сложности';
        }

        if (!empty($filters['fear_level'])) {
            $descriptionParts[] = 'с разным уровнем страха';
        }

        if (!empty($filters['min_age'])) {
            $descriptionParts[] = 'для возраста от ' . $filters['min_age'] . ' лет';
        }

        $descriptionParts[] = 'Проверенные организаторы. Гарантия бронирования. Отзывы реальных игроков.';

        $description = implode(' ', $descriptionParts);

        // --- КОНЕЦ ДОБАВЛЕННОГО КОДА ДЛЯ SEO ---


        return view('index', [
            'view' => 'quests.index',
            'shopMode' => 4,
            'quests' => compact('quests', 'filters', 'types', 'difficulties', 'fearLevels', 'durations', 'ageGroups', 'perPage'),
            'title' => $title,
            'meta_description' => $description
        ]);
    }


    public function show($id)
    {
        $this->shareCommonData();
        Cookie::queue('ShopMode', 4);
        $shopMode = 4;
        $quest = Quest::find($id);

        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = Carbon::now()->addDays($i);
        }

        // Получаем все таймслоты для квеста
        $allTimeSlots = $quest->timeslots;

        // Собираем бронирования по датам и слотам — с преобразованием даты в строку
        $bookings = [];
        foreach ($quest->bookings as $booking) {
            // Преобразуем дату бронирования в формат Y-m-d
            $bookingDate = Carbon::parse($booking->date)->format('Y-m-d');
            $bookings[$bookingDate][$booking->timeslot_id] = $booking;
        }

        // Подготовка слотов по датам с определением статусов
        $timeSlotsByDate = [];

        foreach ($dates as $date) {
            $dateString = $date->format('Y-m-d');
            $dayOfWeek = $date->format('l'); // 1 (Mon) — 7 (Sun)

            // Фильтруем слоты только для текущего дня недели
            $relevantSlots = $allTimeSlots->filter(function ($slot) use ($dayOfWeek) {
                return $slot->day_of_week === $dayOfWeek;
            });

            $timeSlotsByDate[$dateString] = $relevantSlots->map(function ($slot) use ($dateString, $bookings, $date) {
                // Определяем, забронирован ли слот на эту дату
                $isBooked = isset($bookings[$dateString][$slot->id]);

                // Создаём полную дату и время для проверки
                $slotDateTime = Carbon::parse($dateString . ' ' . $slot->start_time);
                $isPast = $slotDateTime->lt(Carbon::now());

                // Проверяем, есть ли доплата для этого слота
                $isExtraPrice = $this->checkExtraPrice($slot, $date);

                // Добавляем атрибуты статуса в слот
                $slot->isBooked = $isBooked;
                $slot->isPast = $isPast;
                $slot->isExtraPrice = $isExtraPrice;
                return $slot;
            })->values();
        }

        $view = 'quests.show';
        $modal = 'elements.modal.quest';
        return view('index', compact('dates', 'timeSlotsByDate', 'bookings', 'quest', 'view', 'modal', 'shopMode'));
    }

// Вспомогательный метод для проверки доплаты
    private function checkExtraPrice($slot, $date)
    {
        // Например, доплата в выходные:
        $dayOfWeek = $date->format('l');
        if ($dayOfWeek == 6 || $dayOfWeek == 7) { // суббота или воскресенье
            return true;
        }
        // Или доплата для конкретного слота (по ID или времени)
        if ($slot->price > 0) { // пример условия
            return true;
        }
        return false;
    }


    public function api_services(int $questId) {
     $services = AdditionalService::where('quest_id', $questId)->get();
     return response()->json(['success' => true, 'services' => $services]);
    }
}

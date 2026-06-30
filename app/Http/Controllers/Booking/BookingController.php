<?php

namespace App\Http\Controllers\Booking;

use App\Models\Booking;
use App\Models\Quest;
use App\Models\Timeslot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;

class BookingController extends BaseController
{
    public function index(Request $request){
        $this->shareCommonData($request);
        $phone = Auth::user()?->phone; // Получаем номер телефона авторизованного пользователя

        $bookings = Booking::where('user_id', Auth::id())
            ->orWhere(function ($query) use ($phone) {
                $query->whereNull('user_id')
                    ->where('customer_phone', $phone);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('index',
            [
                'view'=>'pages.bookings.index',
                'title'=>'Бронирования | Брауни Арт — маркетплейс качественных товаров с доставкой по России',
                'bookings' => $bookings,
            ]
        );
    }
    public function store(Request $request)
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'timeslot_id' => 'required|exists:timeslots,id',
            'date' => 'required|date',
            'total_price' => 'required|numeric|min:0',
            'player_count' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'selected_services' => 'nullable|array',
        ], [
            'timeslot_id.required' => 'Необходимо выбрать временной слот для бронирования',
            'timeslot_id.exists' => 'Выбранный временной слот недоступен',
            'date.required' => 'Дата бронирования обязательна для заполнения',
            'date.date' => 'Некорректный формат даты',
            'total_price.required' => 'Цена бронирования обязательна',
            'total_price.numeric' => 'Цена должна быть числом',
            'total_price.min' => 'Цена не может быть отрицательной',
            'player_count.required' => 'Количество игроков обязательно для указания',
            'player_count.integer' => 'Количество игроков должно быть целым числом',
            'player_count.min' => 'Количество игроков не может быть меньше 1',
            'customer_name.required' => 'Имя клиента обязательно для заполнения',
            'customer_name.string' => 'Имя должно быть текстовым значением',
            'customer_name.max' => 'Имя не может превышать 255 символов',
            'customer_phone.required' => 'Телефон обязателен для связи',
            'customer_phone.string' => 'Номер телефона должен быть текстовым значением',
            'customer_phone.max' => 'Номер телефона не может превышать 20 символов',
            'selected_services.array' => 'Дополнительные услуги должны быть представлены в виде списка'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

         // Получаем таймслот
        $timeslot = Timeslot::find($request->timeslot_id);
        if (!$timeslot) {
            return response()->json([
                'success' => false,
                'message' => 'Выбранный временной слот не существует'
            ], 404);
        }

         // ПРОВЕРКА: существует ли уже бронирование на этот квест, дату и timeslot_id
        $existingBooking = Booking::where('quest_id', $timeslot->quest_id)
            ->where('date', $request->date)
            ->where('timeslot_id', $request->timeslot_id)
            ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'На выбранную дату и время уже есть бронирование'
            ], 400);
        }

        // Получаем квест для получения quest_id
        $quest = Quest::whereHas('timeslots', function ($query) use ($request) {
            $query->where('id', $request->timeslot_id);
        })->first();

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Квест не найден'
            ], 404);
        }

        // Создаём бронирование
        $booking = new Booking();
        $booking->quest_id = $quest->id;
        $booking->timeslot_id = $request->timeslot_id;
        $booking->date = $request->date;
        $booking->total_price = $request->total_price;
        $booking->player_count = $request->player_count;
        $booking->customer_name = $request->customer_name;
        $booking->customer_phone = $request->customer_phone;

        // Обрабатываем дополнительные услуги
        if ($request->has('selected_services')) {
            $booking->selected_services = json_encode($request->selected_services);
        } else {
            $booking->selected_services = json_encode([]);
        }

        // Если пользователь авторизован, сохраняем его ID
        if (Auth::check()) {
            $booking->user_id = Auth::id();
        }

        try {
            $booking->save();

            return response()->json([
                'success' => true,
                'message' => 'Бронирование успешно создано',
                'booking_id' => $booking->id
            ], 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании бронирования',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

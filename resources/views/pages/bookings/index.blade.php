<!-- main-area -->
<main>

    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Бронирования</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Бронирования</h1>
    </div>
    <!-- wishlist-area -->
    <section class="wishlist-area py-5" style="padding-top: 0 !important;">
        <div class="custom-container-two">
            <div class="row">
                <div class="col-12">

                    <div class="bookings-list">
                        @foreach($bookings as $booking)
                            <div class="booking-card">
                                <div class="booking-header">
                                    <div class="booking-id">#{{$booking->id}}</div>
                                    <span class="payment-badge">Оплата на месте</span>
                                </div>

                                <div class="booking-content">
                                    <!-- Изображение и информация о квесте -->
                                    <div class="quest-info">
                                        <a href="/quests/{{$booking->quest->id}}" class="quest-link">
                                            <div class="quest-image-container">
                                                <img
                                                    src="{{$booking->quest->getMainImage()}}"
                                                    alt="{{$booking->quest->title}}"
                                                    class="quest-image"
                                                >
                                            </div>
                                            <div class="quest-details">
                                                <h3 class="quest-title">{{$booking->quest->title}}</h3>
                                                <p class="seller-name">{{$booking->quest->seller->name}}</p>
                                                <p class="base-price">{{$booking->quest->getProductPrice()}} руб.</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Детали бронирования -->
                                    <div class="booking-details">
                                        <div class="detail-item">
                                            <i class="fas fa-users detail-icon"></i>
                                            <span>{{$booking->player_count}} чел.</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-coins detail-icon"></i>
                                            <span>+{{$booking->timeslot->price}} руб.</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="far fa-calendar detail-icon"></i>
                                            <span>{{date('d.m.Y в H:i', strtotime($booking->date." ".$booking->timeslot->start_time))}}</span>
                                        </div>
                                    </div>

                                    <!-- Дополнительные услуги -->
                                    <div class="additional-services">
                                        <h4 class="services-title">Дополнительные услуги:</h4>
                                        @if($booking->selected_services->isNotEmpty())
                                            <ul class="services-list">
                                                @foreach($booking->selected_services as $service)
                                                    <li class="service-item">
                                                        {{$service->name}} <span class="service-price">+{{ $service->price }} руб.</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="no-services">Услуги не выбраны</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Итоговая информация -->
                                <div class="booking-footer">
                                    <div class="total-price-container">
                                        <span class="label">Итоговая стоимость:</span>
                                        <span class="total-amount">{{$booking->total_price}} руб.</span>
                                    </div>
{{--                                    <div class="action-buttons">--}}
{{--                                        <button class="btn-view">Посмотреть детали</button>--}}
{{--                                        <button class="btn-cancel">Отменить бронь</button>--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- wishlist-area-end -->

</main>
<!-- main-area-end -->

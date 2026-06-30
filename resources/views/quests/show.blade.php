<main>
    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/quests">Квесты</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$quest->getProductName()}}</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">
            {{$quest->getProductName()}}
        </h1>
    </div>
    <!-- breadcrumb-area-end -->
    @include('elements.svg')
    <!-- shop-details-area -->
    <section class="shop-details-area pb-100">
        <div>
            <div class="quest-page mb-95">
                <div class="background-image" style="background-image: url('{{$quest->getMainImage()}}')">
                    <div class="overlay"></div>
                    <!-- Полупрозрачный слой (rgba(0,0,0,0.5)) -->
                    <div class="content-container">
                        <h1 class="quest-title">{{ $quest->getProductName() }}</h1>
                        <div class="brand-info">Бренд: {{ $quest->getSeller()->name }}</div>
                        <div class="characteristics-grid">
                            <div class="char-item players">
                             <span class="feature-text">
                                 Игроки: от {{$quest->min_players}} до {{$quest->max_players}}
                             </span>
                            </div>
                            <div class="char-item time">Время: {{$quest->duration}} мин</div>
                            <div class="char-item price">
                                {{ $quest->getProductPrice() }} руб.
                            </div>
                            <div class="char-item difficulty">
                                <span class="feature-text">Сложность:</span>
                                <div class="icons">
                                    @php $difficulty = $quest->difficulty; @endphp
                                    @for($i = 1; $i <= $difficulty; $i++)
                                        <svg class="icon key" width="20" height="20">
                                            <use xlink:href="#icon-key"></use>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <div class="char-item fear">
                                <span class="feature-text">Страх:</span>
                                <div class="icons">
                                    @php $fear = $quest->fear_level; @endphp
                                    @for($i = 1; $i <= $fear; $i++)
                                        <svg class="icon key" width="20" height="20">
                                            <use xlink:href="#icon-skull"></use>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <div class="char-item age">
                                <span class="feature-text">{{$quest->min_age}}+</span>
                            </div>
                        </div>
                        <!-- Рейтинг и отзывы -->
                        <div class="review-block">
                            <div class="rating">
                                @php
                                    $rating = $quest->getProductEstimation();
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($hasHalfStar && $i == $fullStars + 1)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="fas fa-star text-muted"></i>
                                    @endif
                                @endfor
                            </div>
                            <span>
                @php
                    $count = count($quest->getProductReviews());
                    $word = match (true) {
                        $count % 10 == 1 && $count % 100 != 11 => 'отзыв',
                        in_array($count % 10, [2, 3, 4]) && !in_array($count % 100, [12, 13, 14]) => 'отзыва',
                        default => 'отзывов'
                    };
                @endphp
                - {{ $count }} {{ $word }}
               </span>
                        </div>
                        <div class="stock-info">Открыт</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="custom-container-two">
            <!-- Описание квеста -->
            <div class="quest-description-section mb-60">
                <h4 class="section-title mb-2" style="color: white;">Описание квеста</h4>
                <div class="product-desc-content">
                    <div class="description-text" style="color: rgba(255,255,255,0.9);">
                        {!!$quest->description!!}
                    </div>
                </div>
            </div>

            <!-- Особенности квеста -->
            <div class="quest-features-section mb-60">
                <h4 class="section-title mb-2" style="color: white;">Особенности квеста</h4>
                <ul class="features-list">
                    @foreach($quest->getFeatures() as $feature)
                    <li class="feature-item">
                        <span style="color: rgba(255,255,255,0.9);">
                          {{$feature}}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Блок бронирования -->
            <div class="booking-section mb-60">
                <h4 class="section-title mb-2" style="color: white;">Забронировать игру</h4>

                <div class="price-legend">
                    <span class="legend-text">Стоимость игры:</span>
                    <button class="price-button blue">{{$quest->getProductPrice()}} р</button>
                    <button class="price-button orange">{{$quest->getProductPrice()}} р + доплата</button>
                    @php
                        $count = $quest->base_player_count;

                        // Функция для преобразования числа в слово в родительном падеже
                        function numberToWordInGenitive($num) {
                            $units = [
                                1 => 'одного',
                                2 => 'двух',
                                3 => 'трёх',
                                4 => 'четырёх',
                                5 => 'пяти',
                                6 => 'шести',
                                7 => 'семи',
                                8 => 'восьми',
                                9 => 'девяти',
                                10 => 'десяти',
                            ];

                            if ($num >= 1 && $num <= 10) {
                                return $units[$num];
                            }

                            return $num; // Для чисел больше 10 возвращаем число без преобразования
                        }

                        // Определяем правильное окончание слова «игрок»
                        $word = match (true) {
                            $count % 10 == 1 && $count % 100 != 11 => 'игрока',
                            in_array($count % 10, [2, 3, 4]) && !in_array($count % 100, [12, 13, 14]) => 'игроков',
                            default => 'игроков'
                        };

                        $numberWord = numberToWordInGenitive($count);
                    @endphp
                    <span class="legend-note">(Цена для {{ $numberWord }} {{ $word }}, точная стоимость — в форме бронирования)</span>
                </div>

                <!-- Календарь слотов -->
                <div class="timeslot-calendar">
                    @foreach ($dates as $date)
                        <div class="timeslot-row">
                            <div class="timeslot-date">
                                {{ $date->format('d.m.Y') }}
                            </div>
                            @foreach ($timeSlotsByDate[$date->format('Y-m-d')] as $slot)
                                <div class="timeslot-cell {{
                                    $slot->isPast ? 'timeslot-past' :
                                    ($slot->isBooked ? 'timeslot-booked' : ($slot->isExtraPrice ? 'timeslot-extra' : 'timeslot-available'))
                                     }} @if(!$slot->isPast && !$slot->isBooked) timeslot-active @endif"
                                     data-timeslot-id="{{ $slot->id }}"
                                     data-date="{{ $date->format('Y-m-d') }}"
                                     data-price="{{ $slot->price + $quest->base_price }}"
                                     data-quest-id="{{$quest->id}}"
                                     data-base-player-count="{{$quest->base_player_count}}"
                                     data-additional-player-price="{{$quest->additional_player_price}}"
                                >
                                    {{ $slot->start_time }}
                                    @if ($slot->isBooked)
                                        <span class="booking-indicator">Занят</span>
                                    @elseif ($slot->isPast)
                                        <span class="past-indicator">Прошёл</span>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Если слотов нет для этой даты -->
                            @if (empty($timeSlotsByDate[$date->format('Y-m-d')]))
                                <div class="timeslot-cell timeslot-empty">
                                    Слотов нет
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Отзывы -->
            <div class="product-reviews-wrap">
                <div class="deal-day-top">
                    <div class="deal-day-title">
                        <h4 class="title" style="color: white;">Отзывы</h4>
                    </div>
                </div>
                <div class="reviews-count-title">
                    @php
                        $count = count($quest->getProductReviews());
                        $word = match (true) {
                            $count % 10 == 1 && $count % 100 != 11 => 'отзыв',
                            in_array($count % 10, [2, 3, 4]) && !in_array($count % 100, [12, 13, 14]) => 'отзыва',
                            default => 'отзывов'
                        };
                    @endphp
                    <h5 style="color: rgba(255,255,255,0.8);">Всего {{ $count }} {{ $word }}</h5>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="product-review-list blog-comment">
                            <ul>
                                @foreach($quest->getProductReviews() as $review)
                                    <li>
                                        <div class="single-comment">
                                            <div class="comment-text">
                                                <div class="comment-avatar-info">
                                                    <h5 style="color: white;">
                                                         {{$review->user->name}}
                                                        <span class="comment-date" style="color: rgba(255,255,255,0.6);">
                                                         {{$review->created_at->format('d.m.Y H:i')}}
                                                        </span>
                                                    </h5>
                                                    <div class="rating">
                                                        @php
                                                            $rating = $review->estimation;
                                                            $fullStars = floor($rating);
                                                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                                        @endphp
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $fullStars)
                                                                <i class="fas fa-star text-warning"></i>
                                                            @elseif($hasHalfStar && $i == $fullStars + 1)
                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                            @else
                                                                <i class="fas fa-star text-muted"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p style="color: rgba(255,255,255,0.9);">{{$review->comment}}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="product-review-form">
                            @if(isset($currentUser->id))
                                <div class="rising-star mb-40">
                                    <h5 style="color: white;">Оставить отзыв</h5>
                                    <div class="rising-rating"></div>
                                </div>

                                <form id="form_review" method="POST" action="/quests/{{$quest->id}}/reviews">
                                    <input type="hidden" id="form_estimation" name="estimation" value="">
                                    @csrf
                                    <div class="form-grp">
                                        <label for="message" style="color: white;">Отзыв *</label>
                                        <textarea class="" name="comment" id="message" minlength="50" maxlength="1000"
                                                  style="background: rgba(255,255,255,0.1); color: white;"></textarea>
                                    </div>
                                    <button class="btn">Отправить</button>
                                </form>
                            @else
                                <h4 style="color: white;">Авторизуйтесь, чтобы оставлять отзывы</h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- shop-details-area-end -->
</main>
<!-- main-area-end -->

@push('scripts')
    <script type="module">
        window.authUserData = {
            isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
            name: @json(auth()->check() ? auth()->user()->name : ''),
            phone: @json(auth()->check() ? auth()->user()->phone : '')
        };
    </script>
    @vite('resources/js/Pages/quests.js')
@endpush


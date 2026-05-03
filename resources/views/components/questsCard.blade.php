@props([
    'quest' => null,
])

<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
    <div class="quest-card card-item shadow-sm">
        <div class="quest-card-thumb">
            <a href="/quests/{{ $quest->id }}">
                <img
                    src="{{ $quest->getMainImage() ?? '/images/quest-placeholder.jpg' }}"
                    alt="{{ $quest->title }}"
                    style="object-fit: cover; height: 200px; width: 100%;">
            </a>
            <div class="quest-badge">
                @if($quest->type == 'performance')
                    <span class="badge bg-danger">Перформанс</span>
                @else
                    <span class="badge bg-primary">Квест</span>
                @endif
            </div>
        </div>
        <div class="quest-card-content p-3">
            <h5 class="quest-card-title mb-2">
                <a href="/quests/{{ $quest->id }}">
                    {{ Str::limit($quest->title, 50) }}
                </a>
            </h5>

            <div class="quest-meta mb-3">
                <!-- Сложность -->
                <div class="mb-2">
                    <i class="fas fa-bolt text-warning mr-1"></i>
                    @php
                        $difficultyNames = [
                    '1' => 'Лёгкий',
                    '2' => 'Средний',
                    '3' => 'Сложный'
                ];
                $difficultyName = $difficultyNames[$quest->difficulty] ?? $quest->difficulty;
                    @endphp
                    <span>{{ $difficultyName }} уровень сложности</span>
                </div>

                <!-- Уровень страха -->
                <div class="mb-2">
                    <i class="fas fa-ghost text-danger mr-1"></i>
                    @php
                        $fearNames = [
                        '1' => 'Низкий',
                        '2' => 'Средний',
                        '3' => 'Высокий'
                    ];
                    $fearName = $fearNames[$quest->fear_level] ?? $quest->fear_level;
                    @endphp
                    <span>{{ $fearName }} уровень страха</span>
                </div>

                <!-- Количество игроков -->
                <div class="mb-2">
                    <i class="fas fa-users mr-1"></i>
                    <span>{{ $quest->min_players }}–{{ $quest->max_players }} игроков</span>
                </div>

                <!-- Минимальный возраст -->
                <div class="mb-2">
                    <i class="fas fa-child mr-1"></i>
                    <span>от {{ $quest->min_age }} лет</span>
                </div>

                <!-- Продолжительность -->
                <div class="mb-2">
                    <i class="far fa-clock mr-1"></i>
                    <span>{{ $quest->duration }} минут</span>
                </div>
            </div>

            <div class="quest-price-rating d-flex justify-content-between align-items-center">
                <!-- Цена -->
                <div class="price">
                    <span class="h5">{{ $quest->getProductPrice() }} руб.</span>
                </div>
                <!-- Рейтинг -->
                <div class="rating">
                    @php
                        $rating = $quest->getProductEstimation();
                        $fullStars = floor($rating);
                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                        $count = count($quest->getProductReviews());
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

            <div class="mt-3">
                <a href="/quests/{{ $quest->id }}" class="btn btn-primary w-100">
                    Подробнее
                </a>
            </div>
        </div>
    </div>
</div>

@props([
    'product' => null,
])

<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 grid-item grid-sizer cat-two cat-three">
    <div class="exclusive-item exclusive-item-two mb-40">
        <div class="exclusive-item-thumb">
            <a href="/quests/{{$product->getProductId()}}">
                <img src="{{$product->getMainImage()}}" alt="{{ $product->getProductName() }}" style="object-fit: cover; height: 200px; width: 100%;">
                <img class="overlay-product-thumb" src="{{$product->getMainImage()}}" alt="{{ $product->getProductName() }}" style="object-fit: cover; height: 200px; width: 100%;">
            </a>
            <span class="sd-meta">
                @if($product->type == 'performance')
                    Перформанс
                @else
                    Квест
                @endif
            </span>
            @if($product->isNew())
                <span class="discount">Новинка</span>
            @endif
            <x-add-to-cart-button
                :product-id="$product->getProductId()"
                :seller-id="$product->getProductSellerId()"
                mode="homeQuest"
                quantity="1"
                button-text="В корзину"
                link-class="AdToCartLink"
                :options="[]"
            />
        </div>

        <div class="exclusive-item-content p-3">
            <!-- Название квеста -->
            <h5 class="quest-card-title mb-2">
                <a href="/quests/{{$product->getProductId()}}">
                    {{ Str::limit($product->getProductName(), 50) }}
                </a>
            </h5>

            <!-- Характеристики квеста -->
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
                $difficultyName = $difficultyNames[$product->difficulty] ?? $product->difficulty;
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
                $fearName = $fearNames[$product->fear_level] ?? $product->fear_level;
                    @endphp
                    <span>{{ $fearName }} уровень страха</span>
                </div>

                <!-- Количество игроков -->
                <div class="mb-2">
                    <i class="fas fa-users mr-1"></i>
                    <span>{{ $product->min_players }}–{{ $product->max_players }} игроков</span>
                </div>

                <!-- Минимальный возраст -->
                <div class="mb-2">
                    <i class="fas fa-child mr-1"></i>
                    <span>от {{ $product->min_age }} лет</span>
                </div>

                <!-- Продолжительность -->
                <div class="mb-2">
                    <i class="far fa-clock mr-1"></i>
                    <span>{{ $product->duration }} минут</span>
                </div>
            </div>

            <!-- Цена и рейтинг -->
            <div class="quest-price-rating d-flex justify-content-between align-items-center">
                <!-- Цена -->
                <div class="price">
                    <span class="h5">{{ $product->getProductPrice() }} руб.</span>
                </div>
                <!-- Рейтинг -->
                <div class="rating">
                    @php
                        $rating = $product->getProductEstimation();
                $fullStars = floor($rating);
                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                $count = count($product->getProductReviews());
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
        </div>
    </div>
</div>

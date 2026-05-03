@props([
    'product' => null,
])

<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
    <div class="quest-card card-item shadow-sm">
        <div class="quest-card-thumb">
            <a href="/product/{{ $product->getProductId()}}">
                <img
                    src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{ $product->getMainImage() }}"
                    alt="{{ $product->getProductName() }}"
                    style="object-fit: contain; height: 200px; margin-left: 20px;">
            </a>
        </div>
        <div class="quest-card-content p-3">
            <h5 class="quest-card-title mb-2">
                <a href="/products/{{ $product->getProductId() }}" class="text-dark">
                    {{ Str::limit($product->getProductName(), 50) }}
                </a>
            </h5>
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

            <div class="mt-3">
                <x-add-to-cart-button
                    :product-id="$product->getProductId()"
                    :seller-id="$product->getProductSellerId()"
                    mode="catalog"
                    quantity="1"
                    button-text="В корзину"
                    link-class="btn btn-primary w-100 AdToCartLink"
                    :options="[]"
                />
            </div>
        </div>
    </div>
</div>

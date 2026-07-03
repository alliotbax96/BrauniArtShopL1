<main>
    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/books">Книги</a></li>
                @if($book->genre)
                    @if($book->getProductGroup()->parent)
                        <li class="breadcrumb-item"><a href="/products?category={{$book->getProductGroup()->parent->id}}">{{$book->getProductGroup()->parent->name}}</a></li>
                    @endif
                    <li class="breadcrumb-item"><a href="/products?category={{$book->getProductGroup()->id}}">{{$book->getProductGroup()->name}}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{$book->getProductName()}}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-4">{{$book->getProductName()}}</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- shop-details-area -->
    <section class="shop-details-area pb-100">
        <div class="custom-container-two">
            <div class="row mb-95">
                <!-- Левая колонка - Обложка и галерея -->
                <div class="col-xl-5 col-lg-6">
                    <div class="book-details-img-wrap">
                        <div class="shop-details-active">
                            @if($book->getMainImage())
                                <div class="shop-details-img">
                                    <a href="{{ $book->getMainImage() }}" class="popup-image">
                                        <img src="{{ $book->getMainImage() }}"
                                             alt="{{ $book->name }}"
                                             style="max-height: 800px; object-fit: contain;">
                                    </a>
                                </div>
                            @else
                                <div class="shop-details-img">
                                    <div class="d-flex align-items-center justify-content-center w-100"
                                         style="height: 800px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px;">
                                        <div class="text-white text-center">
                                            <i class="fas fa-book-open mb-3" style="font-size: 4rem; opacity: 0.8;"></i>
                                            <h3 class="text-white">{{ $book->name }}</h3>
                                            <p class="text-white-50">{{ $book->author ?? 'Автор не указан' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Правая колонка - Информация о книге -->
                <div class="col-xl-7 col-lg-6">
                    <div class="shop-details-content">
                        <div class="mb-3">
                            @if($book->isComplete())
                                <span class="badge badge-success p-2">
                                    <i class="fas fa-check-circle"></i> Полная версия
                                </span>
                            @else
                                <span class="badge badge-warning p-2">
                                    <i class="fas fa-clock"></i> Черновик
                                </span>
                            @endif
                            <span class="badge {{ $book->getBookTypeBadgeClass() }} p-2">
                                <i class="fas {{ $book->isEbook() ? 'fa-book-open' : 'fa-headphones' }} me-1"></i>
                                {{ $book->getBookTypeLabel() }}
                            </span>
                            @if($book->isNew())
                                <span class="badge bg-danger p-2">Новинка</span>
                            @endif
                        </div>

                        <h2>{{ $book->getProductName() }}</h2>

                        <div class="shop-details-review">
                            <div class="rating">
                                @php
                                    $rating = $book->getProductEstimation();
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
                                    $count = count($book->getProductReviews());
                                    $word = match (true) {
                                        $count % 10 == 1 && $count % 100 != 11 => 'отзыв',
                                        in_array($count % 10, [2, 3, 4]) && !in_array($count % 100, [12, 13, 14]) => 'отзыва',
                                        default => 'отзывов'
                                    };
                                @endphp
                                - {{ $count }} {{ $word }}
                            </span>
                        </div>

                        <div class="shop-details-price">
                            <h2>
                                @if (is_numeric($book->getProductPrice(false)))
                                    {{ number_format($book->getProductPrice(false), 2, ',', ' ') }} руб.
                                @else
                                    {{$book->getProductPrice(false)}}
                                @endif
                            </h2>
                            @if($book->isAudiobook() && $book->total_duration)
                                <small class="text-muted d-block">
                                    <i class="far fa-clock me-1"></i> {{ $book->formatted_duration }}
                                </small>
                            @endif
                        </div>

                        <div class="book-info-details mt-4 mb-4">
                            <div class="row g-3">
                                @if($book->author)
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Автор</small>
                                            <span class="fw-semibold">{{ $book->author }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->getGenreName() !== 'Не указан')
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Жанр</small>
                                            <span class="fw-semibold">{{ $book->getGenreName() }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->publication_year)
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Год выпуска</small>
                                            <span class="fw-semibold">{{ $book->getFormattedPublicationYear() }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->publisher)
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Издатель</small>
                                            <span class="fw-semibold">{{ $book->publisher }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->isEbook() && $book->getReadableSize())
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Объём</small>
                                            <span class="fw-semibold">{{ $book->getReadableSize() }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->isAudiobook() && $book->getTotalDuration())
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Длительность</small>
                                            <span class="fw-semibold">{{ $book->getFormattedTotalDuration() }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->language)
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Язык</small>
                                            <span class="fw-semibold">{{ strtoupper($book->language) }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->isAudiobook() && $book->narrator)
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">Озвучивает</small>
                                            <span class="fw-semibold">{{ $book->narrator }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->getChapterCount() > 0)
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">{{ trans_choice('глава|главы|глав', $book->getChapterCount()) }}</small>
                                            <span class="fw-semibold">{{ $book->getChapterCount() }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->isEbook() && $book->pages_count)
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">{{ trans_choice('страница|страницы|страниц', $book->pages_count) }}</small>
                                            <span class="fw-semibold">{{ number_format($book->pages_count, 0, '', ' ') }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($book->isbn)
                                    <div class="col-12">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted mb-1">ISBN</small>
                                            <span class="fw-semibold font-monospace">{{ $book->isbn }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
{{--                        @if($book->annotation)--}}
{{--                            <div class="book-annotation mt-3 mb-3">--}}
{{--                                <small class="text-muted d-block mb-1">Аннотация</small>--}}
{{--                                <p class="small">{{ Str::limit($book->annotation, 200) }}</p>--}}
{{--                            </div>--}}
{{--                        @endif--}}
                        <div class="row">
                            <div class="col-12">
                                <div class="product-desc-wrap ">
                                    <ul class="nav nav-tabs mb-25" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab"
                                                    data-bs-target="#details" type="button" role="tab"
                                                    aria-controls="details" aria-selected="true">
                                                Описание
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="chapters-tab" data-bs-toggle="tab"
                                                    data-bs-target="#chapters" type="button" role="tab"
                                                    aria-controls="chapters" aria-selected="false">
                                                Содержание
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                                    data-bs-target="#reviews" type="button" role="tab"
                                                    aria-controls="reviews" aria-selected="false">
                                                Отзывы
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <!-- Вкладка "Описание" -->
                                        <div class="tab-pane fade show active" id="details" role="tabpanel"
                                             aria-labelledby="details-tab">
                                            <div class="product-desc-content">
                                                <div class="book-full-description">
                                                    @if($book->annotation)
                                                        <p>{{ $book->annotation }}</p>
                                                    @else
                                                        <p class="text-muted">Описание отсутствует</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Вкладка "Содержание" -->
                                        <div class="tab-pane fade" id="chapters" role="tabpanel"
                                             aria-labelledby="chapters-tab">
                                            <div class="product-desc-content">
                                                @if($book->getChapterCount() > 0)
                                                    <div class="chapters-list">
                                                        <div class="list-group">
                                                            @foreach($book->publishedChapters()->get() as $chapter)
                                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <strong>{{ $chapter->order }}.</strong>
                                                                        {{ $chapter->title }}
                                                                    </div>
                                                                    @if($chapter->is_free_preview)
                                                                        <span class="badge bg-success">Бесплатно</span>
                                                                    @endif
                                                                    @if($chapter->duration)
                                                                        <small class="text-muted">
                                                                            {{$book->isEbook() ? $chapter->duration : '<i class="far fa-clock me-1"></i>'.gmdate('H:i:s', $chapter->duration) }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="mt-3">
                                                            <small class="text-muted">
                                                                Всего глав: {{ $book->getChapterCount() }}
                                                                @if($book->isAudiobook() && $book->total_duration)
                                                                    , общая длительность: {{ $book->formatted_duration }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-muted">Содержание пока не добавлено</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Вкладка "Отзывы" -->
                                        <div class="tab-pane fade" id="reviews" role="tabpanel"
                                             aria-labelledby="reviews-tab">
                                            <div class="product-reviews-wrap">
                                                <div class="reviews-count-title">
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="product-review-list blog-comment">
                                                            <ul>
                                                                @foreach($book->getProductReviews() as $review)
                                                                    <li>
                                                                        <div class="single-comment">
                                                                            <div class="comment-text">
                                                                                <div class="comment-avatar-info">
                                                                                    <h5>{{ $review->user->name ?? 'Пользователь' }}
                                                                                        <span class="comment-date">{{ $review->created_at->format('d.m.Y') }}</span>
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
                                                                                <p>{{ $review->comment }}</p>
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
                                                                    <h5>Оцените книгу</h5>
                                                                    <div class="rising-rating"></div>
                                                                </div>

                                                                <form id="form_review" method="POST" action="/products/{{ $book->id }}/reviews">
                                                                    <input type="hidden" id="form_estimation" name="estimation" value="">
                                                                    @csrf
                                                                    <div class="form-grp">
                                                                        <label for="message">Ваш отзыв *</label>
                                                                        <textarea class="form-control" name="comment" id="message"
                                                                                  minlength="50" maxlength="1000" rows="5"></textarea>
                                                                    </div>
                                                                    <button class="btn btn-primary mt-3">Отправить отзыв</button>
                                                                </form>
                                                            @else
                                                                <div class="alert alert-info">
                                                                    <h4 class="h6">Авторизуйтесь, чтобы оставлять отзывы</h4>
                                                                    <a href="/auth" class="btn btn-primary btn-sm">Войти</a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p>
                            <i class="fas fa-store me-1"></i> Продавец:
                            <a href="/sellers/{{ $book->getSeller()->id ?? 0 }}">{{ $book->getSeller()->name ?? 'Не указан' }}</a>
                        </p>
                        @if ($book->isPurchased())
                            @if($book->isEbook())
                                <a href="/reader/{{$book->id}}" class="btn btn-success btn-sm">Читать</a>
                            @endif
                        @else
                        <div class="perched-info">
                            <div class="cart-plus">
                                <form id="adtocart"></form>
                            </div>
                            <input form="adtocart" type="hidden" name="product_id" value="{{ $book->getProductId() }}">
                            <x-add-to-cart-button
                                :product-id="$book->getProductId()"
                                :seller-id="$book->getProductSellerId()"
                                productType='App\\Models\\Book'
                                quantity="1"
                                button-text="В корзину"
                                link-class="btn-primary AdToCartLink"
                                :options="[]"
                            />
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@push('scripts')
    @vite('resources/js/Pages/product.js')
@endpush

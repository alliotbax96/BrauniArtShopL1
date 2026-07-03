@props([
    'book' => null,
])

<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 mb-4">
    <div class="quest-card card-item shadow-sm">
        <div class="quest-card-thumb position-relative" style="padding-bottom: 150%;">
            <a href="/books/{{ $book->id }}" class="position-absolute top-0 start-0 w-100 h-100">
                @if($book->getMainImage())
                    <img
                        src="{{ $book->getMainImage() }}"
                        alt="{{ $book->name ?? 'Наименование книги' }}"
                        class="w-100 h-100"
                        style="object-fit: cover;">
                @else
                    <div
                        class="w-100 h-100 d-flex flex-column align-items-center justify-content-center"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-book-open text-white mb-2" style="font-size: 2rem; opacity: 0.8;"></i>
                        <div class="text-white text-center px-2 small fw-bold">
                            {{ Str::limit($book->name ?? 'Название книги', 35) }}
                        </div>
                        <div class="text-white text-center px-2 small mt-1 opacity-75">
                            {{ $book->author ?? 'Автор' }}
                        </div>
                    </div>
                @endif
            </a>

            <div class="quest-badge">
                <span class="badge {{ $book->getBookTypeBadgeClass() }}">
                    {{ $book->getBookTypeLabel() }}
                </span>
                @if($book->isNew())
                    <span class="badge bg-danger">Новинка</span>
                @endif
            </div>
        </div>

        <div class="quest-card-content p-2 p-md-3">
            <h6 class="quest-card-title mb-1">
                <a href="/books/{{ $book->id }}" class="text-decoration-none">
                    {{ Str::limit($book->name, 40) }}
                </a>
            </h6>

            @if($book->author)
                <small class="text-muted d-block mb-2">
                    <i class="fas fa-user me-1"></i> {{ Str::limit($book->author, 25) }}
                </small>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div class="price">
                    <span class="h6 text-primary mb-0">
                        @if (is_numeric($book->getProductPrice(false)))
                            {{ number_format($book->getProductPrice(false), 2, ',', ' ') }} руб.
                        @else
                            {{$book->getProductPrice(false)}}
                        @endif
                    </span>
                </div>
                <div class="rating small">
                    @php
                        $rating = $book->getProductEstimation();
                        $fullStars = floor($rating);
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $fullStars ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

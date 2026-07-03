<!-- main-area -->
<main>
    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Каталог книг</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Каталог книг</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- shop-area -->
    <div class="shop-area gray-bg pb-100">
        <div class="custom-container-two">
            <div class="shop-top-meta">
                <p class="show-result">
                    @if($books['books']->total())
                        Найдено: {{ $books['books']->total() }} {{ trans_choice('книга|книги|книг', $books['books']->total()) }}
                    @else
                        Ничего не найдено
                    @endif
                </p>
                <div class="shop-meta-right">
                    <form id="filters" action="/books" method="get">
                        <select class="custom-select perPageSelect" name="perPage">
                            <option value="9" @if($books['perPage'] == 9) selected @endif>9</option>
                            <option value="12" @if($books['perPage'] == 12) selected @endif>12</option>
                            <option value="18" @if($books['perPage'] == 18) selected @endif>18</option>
                            <option value="21" @if($books['perPage'] == 21) selected @endif>21</option>
                            <option value="24" @if($books['perPage'] == 24) selected @endif>24</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="row justify-content-center">
                <!-- Сайдбар с фильтрами -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 order-2 order-lg-0">
                    <aside class="shop-sidebar">

                        <!-- Тип книги: ebook / audiobook -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Формат</h6>
                            </div>
                            <ul class="shop-cat-list">
                                <li>
                                    <label>
                                        <input form="filters" type="radio" name="type" value=""
                                               @if(empty($books['filters']['type'])) checked @endif>
                                        <span>Все</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input form="filters" type="radio" name="type" value="ebook"
                                               @if($books['filters']['type'] == 'ebook') checked @endif>
                                        <span>Электронная книга</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input form="filters" type="radio" name="type" value="audiobook"
                                               @if($books['filters']['type'] == 'audiobook') checked @endif>
                                        <span>Аудиокнига</span>
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <!-- Автор -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Автор</h6>
                            </div>
                            <input form="filters" type="text" name="author" class="form-control"
                                   placeholder="Имя автора"
                                   value="{{ e($books['filters']['author'] ?? '') }}">
                        </div>

                        <!-- Цена -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Цена</h6>
                            </div>
                            <div class="price_filter">
                                <div id="slider-range"></div>
                                <div class="price_slider_amount">
                                    <span>Цена:</span>
                                    <input type="text" id="amount" placeholder="Диапазон цен"/>
                                    <input form="filters" type="hidden" name="min_price"
                                           value="{{ $books['filters']['min_price'] ?? '' }}">
                                    <input form="filters" type="hidden" name="max_price"
                                           value="{{ $books['filters']['max_price'] ?? '' }}">
                                </div>
                            </div>
                            <br>
                            <input form="filters" type="submit" class="btn" value="Показать">
                        </div>
                    </aside>
                </div>

                <!-- Список книг -->
                <div class="col-xl-9 col-lg-8">
                    <div class="row list">
                        @foreach($books['books'] as $book)
                            <x-homeBookCard :book="$book" />
                        @endforeach
                    </div>
                    <div class="pagination-wrap">
                        {{ $books['books']->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- shop-area-end -->
</main>
<!-- main-area-end -->
@push('scripts')
{{--    @vite('resources/js/Pages/books.js')--}}
@endpush

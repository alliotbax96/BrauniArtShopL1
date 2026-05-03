<!-- main-area -->
<main>
    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Квесты</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Квесты и перформансы</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- shop-area -->
    <div class="shop-area pb-100">
        <div class="custom-container-two">
            <div class="shop-top-meta">
                <p class="show-result">Найдено <strong>{{ $quests['quests']->total() }}</strong> квестов</p>
                <div class="shop-meta-right">
                    <form id="filters" action="/quests" method="get">
                        <select class="custom-select perPageSelect" name="perPage">
                            <option value="8" @if($quests['perPage'] == 8) selected @endif>8 квестов на странице
                            </option>
                            <option value="12" @if($quests['perPage'] == 12) selected @endif>12 квестов на странице
                            </option>
                            <option value="16" @if($quests['perPage'] == 16) selected @endif>16 квестов на странице
                            </option>
                            <option value="20" @if($quests['perPage'] == 20) selected @endif>20 квестов на странице
                            </option>
                            <option value="24" @if($quests['perPage'] == 24) selected @endif>24 квестов на странице
                            </option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 order-2 order-lg-0">
                    <aside class="shop-sidebar">
                        <!-- Фильтр по типу квеста -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Тип квеста</h6>
                            </div>
                            <ul class="treeview">
                                @foreach($quests['types'] as $type)
                                    @php
                                        $typeNames = [
                                            'quest' => 'Квесты',
                                            'performance' => 'Перформансы'
                                        ];
                                        $typeName = $typeNames[$type] ?? $type;
                                    @endphp
                                    <li>
                                        <label>
                                            <input form="filters" type="radio" name="type"
                                                   value="{{ $type }}"
                                                   @if(isset($quests['filters']['type']) && $quests['filters']['type'] == $type) checked @endif>
                                            <span class="category-name">{{ $typeName }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Фильтр по сложности -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Сложность</h6>
                            </div>
                            <ul class="treeview">
                                @foreach($quests['difficulties'] as $difficulty)
                                    @php
                                        $difficultyNames = [
                                        '1' => 'Лёгкие',
                                        '2' => 'Средней сложности',
                                        '3' => 'Сложные'
                                        ];
                                        $difficultyName = $difficultyNames[$difficulty] ?? $difficulty;
                                    @endphp
                                    <li>
                                        <label>
                                            <input form="filters" type="radio" name="difficulty"
                                                   value="{{ $difficulty }}"
                                                   @if(isset($quests['filters']['difficulty']) && $quests['filters']['difficulty'] == $difficulty) checked @endif>
                                            <span class="category-name">{{ $difficultyName }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Фильтр по уровню страха -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Уровень страха</h6>
                            </div>
                            <ul class="treeview">
                                @foreach($quests['fearLevels'] as $fearLevel)
                                    @php
                                        $fearNames = [
                                        '1' => 'Низкий',
                                        '2' => 'Средний',
                                        '3' => 'Высокий'
                                        ];
                                        $fearName = $fearNames[$fearLevel] ?? $fearLevel;
                                    @endphp
                                    <li>
                                        <label>
                                            <input form="filters" type="radio" name="fear_level"
                                                   value="{{ $fearLevel }}"
                                                   @if(isset($quests['filters']['fear_level']) && $quests['filters']['fear_level'] == $fearLevel) checked @endif>
                                            <span class="category-name">{{ $fearName }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Фильтр по возрасту -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Минимальный возраст</h6>
                            </div>
                            <select form="filters" name="min_age" class="custom-select form-control">
                                <option value="">Любой возраст</option>
                                @foreach($quests['ageGroups'] as $key => $ageGroup)
                                    <option value="{{ $key }}"
                                            @if(isset($quests['filters']['min_age']) && $quests['filters']['min_age'] == $key) selected @endif>{{ $ageGroup }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Фильтр по продолжительности -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Продолжительность</h6>
                            </div>
                            <ul class="treeview">
                                @foreach($quests['durations'] as $duration)
                                    <li>
                                        <label>
                                            <input form="filters" type="radio" name="duration"
                                                   value="{{ $duration }}"
                                                   @if(isset($quests['filters']['duration']) && $quests['filters']['duration'] == $duration) checked @endif>
                                            <span class="category-name">{{ $duration }} минут</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Фильтр по цене -->
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Цена</h6>
                            </div>
                            <div class="price_filter">
                                <div id="slider-range"></div>
                                <div class="price_slider_amount">
                                    <span>Цена: от</span>
                                    <input type="text" id="min_price" name="min_price" placeholder="мин. цена"
                                           value="{{ $quests['filters']['min_price'] ?? '' }}">
                                    <span>до</span>
                                    <input type="text" id="max_price" name="max_price" placeholder="макс. цена"
                                           value="{{ $quests['filters']['max_price'] ?? '' }}">
                                </div>
                            </div>
                            <br>
                            <input form="filters" type="submit" class="btn w-100" value="Применить фильтры">
                        </div>
                    </aside>
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div class="row list">
                        @foreach($quests['quests'] as $quest)
                            <x-questsCard
                                :quest="$quest"
                            />
                        @endforeach
                    </div>
                </div>
                <!-- Пагинация -->
                <div class="pagination-wrap mt-4">
                    {{ $quests['quests']->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    <!-- shop-area-end -->
</main>
<!-- main-area-end -->
@push('scripts')
    @vite('resources/js/Pages/quests.js')
@endpush


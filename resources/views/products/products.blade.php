<!-- main-area -->
<main>
    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Каталог</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Каталог</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- shop-area -->
    <div class="shop-area gray-bg pb-100">
        <div class="custom-container-two">
            <div class="shop-top-meta">
                <p class="show-result"></p>
                <div class="shop-meta-right">
                    <form id="filters" action="/products" method="get">
                        <select class="custom-select perPageSelect" name="perPage">
                            <option value="8" @if($products['perPage'] == 9) selected @endif>9</option>
                            <option value="12" @if($products['perPage'] == 12) selected @endif>12</option>
                            <option value="16" @if($products['perPage'] == 18) selected @endif>18</option>
                            <option value="20" @if($products['perPage'] == 21) selected @endif>21</option>
                            <option value="24" @if($products['perPage'] == 24) selected @endif>24</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 order-2 order-lg-0">
                    <aside class="shop-sidebar">
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Категории</h6>
                            </div>

                            <div class="shop-cat-list">
                                <ul class="treeview">
                                    @foreach($ProductGroups as $item)
                                        @if($item->parent_id == 0)
                                            <li class="has-children @if(isset($FilterGroup) && !empty($FilterGroup->parent_id) && $item->id == $FilterGroup->parent_id) expanded @endif">
                                                <label class="category-toggle">
                                                    <input form="filters" type="radio" name="rootCategory"
                                                           value="{{ $item->id }}"
                                                           @if(isset($FilterGroup) && !empty($FilterGroup->parent_id) && $item->id == $FilterGroup->parent_id) checked @endif>
                                                    <span class="category-name">{{$item->name}}</span>
                                                    <span class="toggle-icon">@if ($item->children->isNotEmpty())
                                                            ▶
                                                        @endif</span>
                                                </label>
                                                <!-- Вложенные категории -->
                                                @if ($item->children->isNotEmpty())
                                                    <ul class="children">
                                                        @foreach($item->children as $child)
                                                            <li>
                                                                <label>
                                                                    <input form="filters" type="radio" name="category"
                                                                           value="{{ $child->id }}"
                                                                           @if(isset($FilterGroup) && !empty($FilterGroup->parent_id) && $child->id == $FilterGroup->id) checked @endif>
                                                                    <span class="category-name">{{ $child->name }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>


                        </div>
                        <div class="widget shop-widget mb-30">
                            <div class="shop-widget-title">
                                <h6 class="title">Цена</h6>
                            </div>
                            <div class="price_filter">
                                <div id="slider-range"></div>
                                <div class="price_slider_amount">
                                    <span>Цена :</span>
                                    <input type="text" id="amount" name="price" placeholder="Add Your Price"/>
                                    <input form="filters" type="hidden" name="min_price"
                                           value="{{$products['filters']['min_price']}}">
                                    <input form="filters" type="hidden" name="max_price"
                                           value="{{$products['filters']['max_price']}}">
                                </div>
                            </div>
                            <br>
                            <input form="filters" type="submit" class="btn" value="Показать">
                        </div>
                    </aside>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="row list">
                        @foreach($products['products'] as $product)
                          <x-productsCard
                            :product="$product"
                          />
                        @endforeach
                    </div>
                    <div class="pagination-wrap">{{ $products['products']->withQueryString()->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- shop-area-end -->
</main>
<!-- main-area-end -->
@push('scripts')
    @vite('resources/js/Pages/products.js')
@endpush

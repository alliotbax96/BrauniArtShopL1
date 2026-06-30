<!-- main-area -->
<main>

    <!-- slider-area -->
    {{--        <section>--}}
    {{--            <div class="container-fluid">--}}
    {{--                <div class="row align-items-start justify-content-between">--}}
    {{--                    <div class="col mini_banner d-none d-lg-flex"><img src="/assets/img/images/right_banner.png" alt=""></div>--}}
    {{--                    <div class="col mini_banner-center"><img src="/assets/img/images/center_banner.png" alt=""></div>--}}
    {{--                    <div class="col mini_banner d-none d-lg-flex"><img src="/assets/img/images/left_banner.png" alt=""></div>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </section>--}}
    <!-- slider-area-end -->

    <!-- exclusive-collection-area -->
    <section class="exclusive-collection pt-10 pb-10">
        <div class="custom-container-two">
            <div class="row justify-content-center"></div>
            <div id="showmore-list">
                <div>
                    <div class="row prod-list">
                        @foreach($products['products'] as $product)
                            @switch(Cookie::get('ShopMode'))
                                @case(1)
                                    <x-homeProductCard
                                        :product="$product"
                                    />
                                @break
                                @case(4)
                                    <x-homeQuestCard
                                        :product="$product"
                                    />
                                @break
                                @case(8)
                                    <x-homeBookCard
                                        :book="$product"
                                    />
                                @break
                            @endswitch
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @if(count($products['products'])>8)
            <div id="showmore-triger" data-page="1" data-max="{$amt}">
                <img src="https://snipp.ru/demo/693/ajax-loader.gif" alt="">
            </div>
        @else
            <input type="hidden" id="showmore-triger" value="none">
        @endif
    </section>
    <!-- exclusive-collection-area-end -->
</main>
<!-- main-area-end -->

@push('scripts')
    @vite('resources/js/Pages/home.js')
@endpush

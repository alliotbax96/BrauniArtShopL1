@props([
    'shopMode'=>1
])


<div>
    @switch($shopMode)
        @case(1)
            <form id="search_form" action="/products" method="get"></form>
            @break
        @case(4)
            <form id="search_form" action="/quests" method="get"></form>
            @break
    @endswitch
{{--    <a href="#" class="btn-catalog cat-toggle">КАТАЛОГ</a>--}}
    <input form="search_form" id="search-input" list="search" name="search" type="text"
           placeholder="Введите ваш поисковый запрос.....">
    <button form="search_form" type="submit">
        <i class="flaticon-magnifying-glass-1"></i>
    </button>
</div>

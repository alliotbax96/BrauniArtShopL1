@props([
    'ProductGroups' => null,
    'shopMode'=>1
])

@switch($shopMode)
    @case(1)
        <a href="#" class="btn-catalog cat-toggle">КАТАЛОГ</a>
        <ul class="category-menu" style="display: none;">
            @foreach($ProductGroups as $item)
                @if($item->parent_id == 0)
                    <li class="has-dropdown">
                        <a href="/products?category={{$item->id}}">
                            <div class="cat-menu-img">
                                <img style="width: 38px; heght: 38px;" src="https://id.brauniart.shop/{{$item->image}}"
                                     alt="">
                            </div>
                            {{$item->name}}
                        </a>
                        @if ($item->children->isNotEmpty())
                            <ul class="mega-menu">
                                <li>
                                    <ul>
                                        <li class="dropdown-title">{{$item->name}}</li>
                                        @foreach($item->children as $childItem)
                                            @if($childItem->parent_id == $item->id)
                                                <li>
                                                    <a href="/products?category={{$childItem->id}}">{{$childItem->name}}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
        @break
    @case(4)
        <a href="/quests" class="btn-catalog">ВСЕ КВЕСТЫ</a>
        @break
@endswitch

@props([
    'ProductGroups' => null,
    'shopMode' => 1
])

@switch($shopMode)
    @case(4)
        <a href="/quests" class="btn-catalog">ВСЕ КВЕСТЫ</a>
    @break

    @default
        <a href="#" class="btn-catalog cat-toggle">
            {{ $shopMode == 1 ? 'КАТАЛОГ' : 'ЖАНРЫ' }}
        </a>

        <ul class="category-menu" style="display: none;">
            @foreach($ProductGroups->where('parent_id', 0)->where('ShopMode', $shopMode) as $item)
                <li class="has-dropdown">
                    <a href="/products?category={{ $item->id }}">
                        <div class="cat-menu-img">
                            <img style="width: 38px; height: 38px;"
                                 src="https://id.brauniart.shop/{{ $item->image }}"
                                 alt="{{ $item->name }}">
                        </div>
                        {{ $item->name }}
                    </a>

                    @if($item->children->isNotEmpty())
                        <ul class="mega-menu">
                            <li>
                                <ul>
                                    <li class="dropdown-title">{{ $item->name }}</li>
                                    @foreach($item->children->where('parent_id', $item->id) as $childItem)
                                        <li>
                                            <a href="/products?category={{ $childItem->id }}">{{ $childItem->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
        @break
@endswitch

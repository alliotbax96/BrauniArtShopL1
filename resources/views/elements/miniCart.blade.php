<ul class="minicart show" id="minicart" style="max-height: 600px; overflow: scroll;">
    @if($cart && $cart->items->isNotEmpty())
        @foreach($cart->items as $key => $item)
            <li class="d-flex align-items-start">
                <div class="cart-img">
                    <a href="{{ route('products.show', $item->getProduct()->id) }}">
                        <img
                            src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{ $item->getProduct()->getMainImage() }}"
                            alt="{{ $item->getProduct()->name }}">
                    </a>
                </div>
                <div class="cart-content">
                    <h4>
                        <a href="{{ route('products.show', $item->getProduct()->id) }}">
                            {{ $item->getProduct()->getProductName() }} (x{{ $item->quantity }})
                        </a>
                        <p></p>
                    </h4>
                    <div class="cart-price">
                        <span class="new">
                            {{ number_format($item->getProduct()->getProductPrice() * $item->quantity, 2, ',', ' ') }} руб.
                            <br>
                            ({{ number_format($item->getProduct()->getProductPrice(), 2, ',', ' ') }} руб./шт)
                        </span>
                    </div>
                </div>
                <div class="del-icon">
                    <form action="{{ route('cart.remove', $item->id) }}"
                          method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit"
                                style="background: none; border: none; cursor: pointer;">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </li>
        @endforeach
    @else
        <li class="empty-cart-message">
            <p>Корзина пуста</p>
        </li>
    @endif

    <li>
        <div class="total-price">
            <span class="f-left">Итого:</span>
            <span class="f-right" contenteditable>{{$cartTotalPrice}}</span>
        </div>
    </li>
    <li>
        <div class="checkout-link">
            <a href="/cart">Корзина</a>
        </div>
    </li>
</ul>

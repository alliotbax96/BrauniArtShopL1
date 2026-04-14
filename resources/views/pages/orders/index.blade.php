<!-- main-area -->
<main>

    <div class="custom-container-two" >
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Заказы</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Заказы</h1>
    </div>
    <!-- wishlist-area -->
    <section class="wishlist-area py-5" style="padding-top: 0 !important;">
        <div class="custom-container-two">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" class="rounded-start"></th>
                                <th scope="col">Номер заказа</th>
                                <th scope="col">Состав</th>
                                <th scope="col">Сумма</th>
                                <th scope="col">Статус оплаты</th>
                                <th scope="col">Дата</th>
                                <th scope="col" class="rounded-end">Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td></td>
                                    <td><h3><a href="/orders/{{$order->id}}">{{$order->id}}</a></h3></td>
                                    <td>
                                        @foreach($order->items as $OrderItem)
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <a href="/products/{{$OrderItem->product_id}}" class="flex-shrink-0 mr-3">
                                                    <img
                                                        src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{ $OrderItem->product->getMainImage() }}"
                                                        alt="{{ $OrderItem->product->getProductName() }}"
                                                        class="img-thumbnail"
                                                        style="width: 103px; height: 129px; object-fit: contain;"
                                                    >
                                                </a>
                                                <div class="flex-grow-1">
                                                    <h4 class="h6 mb-1">
                                                        <a href="/products/{{ $OrderItem->product->id }}" class="text-decoration-none text-dark">
                                                            {{ $OrderItem->product->getProductName() }}
                                                        </a>
                                                    </h4>
                                                    <p class="text-muted mb-1 small">{{ $OrderItem->product->seller->name }}</p>
                                                    <p class="mb-0 fw-bold">{{ $OrderItem->product->getProductPrice() }} руб.</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>

                                    <td><span class="fw-medium">{{$order->amount}} руб.</span></td>

                                    <td>
                                        @if($order->status != 'pending')
                                        <span class="badge bg-success p-3 text-white">Оплачен</span>
                                        @else
                                        <span class="badge bg-warning text-dark">Не оплачен</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column flex-sm-row gap-2">
                                            <span class="text-muted small">Оформлен {{date('d.m.Y в H:m:s', strtotime($order->created_at))}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->status != 'pending')
                                            <div class="badge bg-{{$order->statusInfo->color}} p-3">{{$order->statusInfo->name}}</div>
                                        @else
                                            <a href="/orders/" class="btn btn-success btn-sm">Оплатить</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- wishlist-area-end -->

</main>
<!-- main-area-end -->

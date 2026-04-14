<!-- main-area -->
<main>

    <!-- breadcrumb-area -->
    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="/orders">Заказы</a></li>
                <li class="breadcrumb-item active">Заказ № {{$order->id}}</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Заказ № {{$order->id}}</h1>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- order-area -->
    <section class="order-area pb-100">
        <div class="custom-container-two">
            <div class="order-content">
                <p>Статус оплаты: @if($order->status != 'pending') <span class="badge bg-success p-2 text-dark">Оплачен @else <span class="badge bg-danger p-2 text-dark">Не оплачен @endif</span></p>
                <p>Статус заказа: <span class="badge bg-{{$order->statusInfo->color}} p-2 text-dark">{{$order->statusInfo->name}}</span></p>
                <p>Доставка: Пункт выдачи Яндекс: {{$order->PickUpPointInfo->pvz_name}}</p>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                    <th scope="col">Наименование</th>
                    <th scope="col">Количество</th>
                    <th scope="col">Цена</th>
                    <th scope="col">Сумма</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Действия</th>
                    </thead>
                    <tbody>
                     @foreach($order->items as $orderItem)
                         <td><img class="order-item-img" src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{{$orderItem->product->getMainImage()}}" alt="">{{$orderItem->product->getProductName()}}</td>
                         <td>{{$orderItem->quantity}} шт.</td>
                         <td>{{$orderItem->product->getProductPrice()}} руб.</td>
                         <td>{{$orderItem->quantity*$orderItem->product->getProductPrice()}} руб.</td>
                         @php
                             $sellerId = $orderItem->seller_id;
                             $statusInfo = $order->getSellerStatus($sellerId)->statusInfo;
                         @endphp
                         <td><span class="badge bg-{{$statusInfo->color}} p-2">{{$statusInfo->name}}</span></td>
                         <td><a href="/orders/sharing/{{$orderItem->id}}" target="_blank">ОТСЛЕДИТЬ</a></td>
                     @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-sm-right">
                <div class="row">
                    <div class="col-6"></div>
                    <div class="col"><h2>Сумма: {{$order->amount}} руб.</h2></div>
                    @if($order->status == 'pending') <div class="col"><a href="#" class="btn btn-primary">Оплатить заказ</a></div> @endif
                </div>
            </div>
        </div>
    </section>
    <!-- order-area-end -->


</main>
<!-- main-area-end -->

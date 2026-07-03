<!-- main-area -->
<main>

    <div class="custom-container-two">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb breadcrumb-new" class="mb-1 mt-2">
            <ol class="breadcrumb breadcrumb-new-ol">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Мои книги</li>
            </ol>
        </nav>
        <!-- Заголовок -->
        <h1 class="h3 mb-4">Мои книги</h1>
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
                                <th scope="col">Дата</th>
                                <th scope="col">Книга</th>
                                <th scope="col">Статус оплаты</th>
                                <th scope="col" class="rounded-end">Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td></td>
                                    <td>
                                        <h3>
                                            <a href="/books/{{$order->book->id}}">{{$order->id}}</a>
                                        </h3>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column flex-sm-row gap-2">
                                            <span
                                                class="text-muted small">Оформлен {{date('d.m.Y в H:m:s', strtotime($order->created_at))}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <a href="/books/{{$order->book->id}}" class="flex-shrink-0 mr-3">
                                                @if($order->book->getMainImage())
                                                <img src="{{$order->book->getMainImage()}}"
                                                    alt="{{$order->book->getProductName()}}"
                                                    style="width: 113px; height: 159px; object-fit: contain;">
                                                @else
                                                    <div class="shop-details-img">
                                                        <div class="d-flex align-items-center justify-content-center"
                                                             style="width: 113px; height: 159px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px;">
                                                            <div class="text-white text-center">
                                                                <i class="fas fa-book-open mb-3" style="font-size: 2rem; opacity: 0.8;"></i>
                                                                <h5 class="text-white">{{ $order->book->name }}</h5>
                                                                <p class="text-white-50">{{ $order->book->author ?? 'Автор не указан' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </a>
                                            <div class="flex-grow-1">
                                                <h4 class="h6 mb-1">
                                                    <a href="/books/{{$order->book->id}}" class="text-decoration-none text-dark">
                                                        {{$order->book->getProductName()}}
                                                    </a>
                                                </h4>
                                                <p class="text-muted mb-1 small"></p>
                                                <p class="mb-0 fw-bold">{{$order->book->getProductPrice()}} руб.</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($order->status != 'pending')
                                            <span class="badge bg-success p-3 text-white">Оплачен</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Не оплачен</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($order->status == 'pending')
                                            <a href="/orders/" class="btn btn-success btn-sm">Оплатить</a>
                                        @else
                                            @if($order->book->isEbook())
                                                <a href="/reader/{{$order->book->id}}" class="btn btn-success btn-sm">Читать</a>
                                            @endif
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


@push('scripts')

@endpush

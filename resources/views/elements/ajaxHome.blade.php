@if(count($products['products']) < 1)

@else
    @foreach($products['products'] as $product)
        <x-homeProductCard
            :product="$product"
        />
    @endforeach
@endif

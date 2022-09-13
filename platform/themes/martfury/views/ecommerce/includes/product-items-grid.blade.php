<div class="loading">
    <div class="half-circle-spinner">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
    </div>
</div>
<!--products list-->
<input type="hidden" name="page" data-value="{{ $products->currentPage() }}">
<input type="hidden" name="q" value="{{ request()->input('q') }}">

<div class="ps-shopping-product">
    <div class="row">
        @forelse ($products as $product)
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
                <div class="ps-product">
                    {!! Theme::partial('product-item', compact('product')) !!}
                </div>
            </div>
        @empty
            <div class="alert alert-warning mt-4 w-100" role="alert">
                {{ __(':total Product found', ['total' => 0]) }}
            </div>
        @endforelse
    </div>
</div>
<div class="ps-pagination">
    {!! $products->withQueryString()->links() !!}
</div>


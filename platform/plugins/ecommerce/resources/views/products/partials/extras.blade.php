<div id="product-extras" class="widget meta-boxes">
    <div class="widget-title">
        <h4><span>{{ trans('plugins/ecommerce::products.related_products') }}</span></h4>
    </div>
    <div class="widget-body">
        <div class="form-group mb-3">
            <label class="control-label">{{ trans('plugins/ecommerce::products.related_products') }}</label>
            <input type="hidden" name="related_products"
                value="@if ($product) {{ implode(',',$product->products()->allRelatedIds()->toArray()) }} @endif" />
            <div class="box-search-advance product">
                <div>
                    <input type="text" class="next-input textbox-advancesearch"
                        placeholder="{{ trans('plugins/ecommerce::products.search_products') }}"
                        data-target="{{ $dataUrl }}">
                </div>
                <div class="panel panel-default">

                </div>
            </div>
            @include('plugins/ecommerce::products.partials.selected-products-list', [
                'products' => $product ? $product->products : collect([]),
                'includeVariation' => false,
            ])
        </div>
        <hr>
        <div class="form-group mb-3">
            <label class="control-label">{{ trans('plugins/ecommerce::products.cross_selling_products') }}</label>
            <input type="hidden" name="cross_sale_products"
                value="@if ($product) {{ implode(',',$product->crossSales()->allRelatedIds()->toArray()) }} @endif" />
            <div class="box-search-advance product">
                <div>
                    <input type="text" class="next-input textbox-advancesearch"
                        placeholder="{{ trans('plugins/ecommerce::products.search_products') }}"
                        data-target="{{ $dataUrl }}">
                </div>
                <div class="panel panel-default">

                </div>
            </div>
            @include('plugins/ecommerce::products.partials.selected-products-list', [
                'products' => $product ? $product->crossSales : collect([]),
                'includeVariation' => false,
            ])
        </div>

        @if (false)
            <hr>
            <div class="form-group mb-3">
                <label class="control-label">{{ trans('plugins/ecommerce::products.up_selling_products') }}</label>
                <input type="hidden" name="up_sale_products"
                    value="@if ($product) {{ implode(',',$product->upSales()->allRelatedIds()->toArray()) }} @endif" />
                <div class="box-search-advance product">
                    <div>
                        <input type="text" class="next-input textbox-advancesearch"
                            placeholder="{{ trans('plugins/ecommerce::products.search_products') }}"
                            data-target="{{ $dataUrl }}">
                    </div>
                    <div class="panel panel-default">

                    </div>
                </div>
                @include('plugins/ecommerce::products.partials.selected-products-list', [
                    'products' => $product ? $product->upSales : collect([]),
                    'includeVariation' => false,
                ])
            </div>

            <hr>
            <div class="form-group mb-3">
                <label class="control-label">{{ trans('plugins/ecommerce::products.grouped_products') }}</label>
                <input type="hidden" name="grouped_products"
                    value="@if ($product) {{ implode(',',$product->groupedItems()->pluck('product_id')->all()) }} @endif" />
                <div class="box-search-advance product">
                    <div>
                        <input type="text" class="next-input textbox-advancesearch"
                            placeholder="{{ trans('plugins/ecommerce::products.search_products') }}"
                            data-target="{{ $dataUrl }}">
                    </div>
                    <div class="panel panel-default">

                    </div>
                </div>
                @include('plugins/ecommerce::products.partials.selected-products-list', [
                    'products' => $product ? $product->groupedProduct : collect([]),
                    'includeVariation' => false,
                ])
            </div>
        @endif
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">EAN CODE CHECK</h5>
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> --}}
            </div>
            <form id="ean_code_check" action="{{ route('products.ean_check') }}">
                <div class="modal-body">
                    <form action="" class="form-group">
                        <label for="">Enter Prodcut EAN Code: {{url()->current()}}</label>
                        <input type="text" class="form-control" name="ean_code_check">
                    </form>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                    <button type="button" onclick="$('#ean_code_check').submit();"
                        class="btn btn-primary">Continue</button>
                </div>
            </form>

        </div>
    </div>
</div>





{{-- Start Ean Code Script --}}
<script>
    $('#exampleModal').modal('show');
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false,
        mouse: false
    });
    $('#ean_code_check').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var is_vendor = "{{ auth('customer')->check() }}";
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            url: is_vendor ? "product-ean-check-vendor" : "{{ route('products.ean_check') }}",
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status && response.is_unique) {
                    $('input[name="ean_code"]').val(response.ean_code);
                    $('input[name="ean_code"]').attr('readonly', 'readonly');
                    $('#exampleModal').modal('hide');
                } else {
                    toastr.success('Created Successfully');
                    console.log(response);
                    location.href = response.route;
                }
            },
            error: function() {
                toastr.error('EAN CODE Required');
                form.reset();
            },
        });
    });
</script>
{{-- End Ean Code Script --}}


<script id="selected_product_list_template" type="text/x-custom-template">
    <tr>
        <td class="width-60-px min-width-60-px">
            <div class="wrap-img vertical-align-m-i">
                <img class="thumb-image" src="__image__" alt="__name__" title="__name__">
            </div>
        </td>
        <td class="pl5 p-r5 min-width-200-px">
            <a class="hover-underline pre-line" href="__url__">__name__</a>
            <p class="type-subdued">__attributes__</p>
        </td>
        <td class="pl5 p-r5 text-end width-20-px min-width-20-px">
            <a href="#" class="btn-trigger-remove-selected-product" title="{{ trans('plugins/ecommerce::products.delete') }}" data-id="__id__">
                <i class="fa fa-times"></i>
            </a>
        </td>
    </tr>
</script>

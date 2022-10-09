@extends(MarketplaceHelper::viewPath('dashboard.layouts.master'))
@section('content')
    <div class="container page-content" style="background: none; max-width: none">
        <div class="table-wrapper">
            @if ($table->isHasFilter())
                <div class="table-configuration-wrap" @if (request()->has('filter_table_id')) style="display: block;" @endif>
                    <span class="configuration-close-btn btn-show-table-options"><i class="fa fa-times"></i></span>
                    {!! $table->renderFilter() !!}
                </div>
            @endif
            <div class="portlet light bordered portlet-no-padding">
                <div class="portlet-title">
                    <div class="caption">
                        <div class="wrapper-action">
                            @if ($actions)
                                <div class="btn-group">
                                    <a class="btn btn-secondary dropdown-toggle" href="#"
                                        data-bs-toggle="dropdown">{{ trans('core/table::table.bulk_actions') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        @foreach ($actions as $action)
                                            <li>
                                                {!! $action !!}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($table->isHasFilter())
                                <button
                                    class="btn btn-primary btn-show-table-options">{{ trans('core/table::table.filters') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-responsive @if ($actions) table-has-actions @endif @if ($table->isHasFilter()) table-has-filter @endif"
                        style="overflow-x: inherit">
                    @section('main-table')
                        {!! $dataTable->table(compact('id', 'class'), false) !!}
                    @show
                </div>
            </div>
        </div>
    </div>
</div>
@include('core/table::modal')
@stop
@push('scripts')
{!! $dataTable->scripts() !!}
<script>
    /**
     * Edit Quantity
     */
    var src = null , quantity = null , product_id = null, route = null , input_quantity = null;
    $(document).on('click', '.editQuantity', event => {
        event.preventDefault();
        src = $(event.currentTarget);
        quantity = src.data('quantity');
        product_id = src.data('id');
        route = src.data('href');
        $('.modal-quantity-change').modal('show');
        input_quantity = $('.modal-quantity-change').find('input');
        input_quantity.val(quantity);
    });

    $(document).on('click', '.change-quantity-button', event => {
        $.ajax({
            url: route,
            type: 'POST',
            data: {
                quantity: input_quantity.val(),
                id: product_id
            },
            success: function(response) {
                if (response.status) {
                    Botble.showSuccess(response.message);
                } else {
                    Botble.showError(response.message);
                }
                $('.buttons-reload').click();
                $('.modal-quantity-change').modal('hide');
            },
            error: function(response) {
                Botble.handleError(response);
                src.removeClass('button-loading');
                $('.buttons-reload').click();
                $('.modal-quantity-change').modal('hide');
            }
        });
    });
</script>
@endpush

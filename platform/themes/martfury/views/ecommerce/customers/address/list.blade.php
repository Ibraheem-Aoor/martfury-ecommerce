@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')

@section('content')
    <div class="ps-section__header">
        <h3></h3>
        <div class="float-left">
            <h3>{{ SeoHelper::getTitle() }}</h3>
        </div>
        <div class="float-right">
            <a class="add-address ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.address.create') }}">
                <span>{{ __('Add a new address') }}</span>
            </a>
        </div>
    </div>
    <div class="ps-section__content">
        <div class="table-responsive">
            <table class="table ps-table--wishlist">
                <thead>
                <tr>
                    <th>{{ __('Address') }}</th>
                    <th>{{ __('Is default?') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @if (count($addresses) > 0)
                    @foreach($addresses as $address)
                        <tr class="dashboard-address-item">
                            <td style="white-space: inherit;">
                                <p>{{ $address->name }}, {{ $address->address }}, {{ $address->city }}, {{ $address->state }}@if (count(EcommerceHelper::getAvailableCountries()) > 1), {{ $address->country_name }} @endif @if (EcommerceHelper::isZipCodeEnabled()), {{ $address->zip_code }} @endif - {{ $address->phone }}</p>
                            </td>
                            <td style="width: 120px;">
                                @if ($address->is_default) {{ __('Yes') }} @else {{ __('No') }} @endif
                            </td>
                            <td style="width: 140px;">
                                <a class="ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.address.edit', $address->id) }}">{{ __('Edit') }}</a>
                                <a class="ps-btn ps-btn--sm ps-btn--small btn-trigger-delete-address"
                                   href="#" data-url="{{ route('customer.address.destroy', $address->id) }}">{{ __('Remove') }}</a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">{{ __('No address!') }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 justify-content-center pagination_style1">
            {!! $addresses->links() !!}
        </div>
    </div>

    <div class="modal fade" id="confirm-delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>{{ __('Confirm delete') }}</strong></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Do you really want to delete this address?') }}</p>
                </div>
                <div class="modal-footer">
                    <button class="ps-btn ps-btn--sm ps-btn--gray" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button class="ps-btn ps-btn--sm avatar-save btn-confirm-delete" type="submit">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
@endsection

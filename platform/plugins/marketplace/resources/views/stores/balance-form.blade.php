{!! Form::open(['url' => route('marketplace.store.revenue.create', $store->id)]) !!}
    <div class="form-group mb-3">
        <label class="control-label required">{{ trans('plugins/marketplace::revenue.forms.amount') . ' (' . get_application_currency()->symbol . ')' }}</label>
        <input type="number" class="form-control" name="amount" placeholder="{{ trans('plugins/marketplace::revenue.forms.amount_placeholder') }}">
    </div>
    <div class="form-group mb-3">
        <label class="control-label required">{{ trans('plugins/marketplace::revenue.forms.type') }}</label>
        {!! Form::customSelect('type', Botble\Marketplace\Enums\RevenueTypeEnum::labels()) !!}
    </div>
    <div class="form-group mb-3">
        <label class="control-label">{{ trans('core/base::forms.description') }}</label>
        <textarea class="form-control" name="description" placeholder="{{ trans('plugins/marketplace::revenue.forms.description_placeholder') }}" rows="5"></textarea>
    </div>
{!! Form::close() !!}

@extends('plugins/marketplace::themes.dashboard.layouts.master')
@section('content')
    <div class="container">
        <form id="step_2_form" action="{{ route('marketplace.vendor.products.post_create_step_2') }}">
            @csrf
            <div class="row mb-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="text-title-field  required">
                            {{ __('Content') }}
                            &nbsp;
                            <i class="fa fa-question" style="display:inline !important;"
                                title="{{ trans('plugins/ecommerce::products.form.content_placeholder') }}"></i>

                        </label>
                    </div>
                    <textarea class="form-control " rows="4" cols="5" name="content" with-short-code="true"
                        aria-required="true"></textarea>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="text-title-field  required">
                            {{ __('Deliverables') }}
                            &nbsp;
                            <i class="fa fa-question" style="display:inline !important;"
                                title="{{ trans('plugins/ecommerce::products.form.deleviralbes_placeholder') }}"></i>

                        </label>
                    </div>
                    <textarea class="form-control " rows="4" cols="5" name="deliverables" with-short-code="true"
                        aria-required="true"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="text-title-field  required">
                            {{ __('Price') }}
                        </label>
                        <div class="next-input--stylized">
                            <span
                                class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                            <input name="price" class="next-input input-mask-number regular-price next-input--invisible"
                                step="any" type="text">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label
                            class="text-title-field">{{ trans('plugins/ecommerce::products.form.storehouse.quantity') }}</label>
                        <input type="number" class="form-control" name="quantity" min="1" step="1">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.brand') }}</label>
                        <select name="brand_id" class="form-control">
                            @foreach ($brands as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Delivery time') }}</label>
                        <select type="number" name="delivery_time" class="form-control text-center">
                            <option value="">--SELECT--</option>
                            <option value="24 Hour" @if (old('delivery_time') == '24 Hour' || (isset($product) && $product->delivery_time == '24 Hour')) selected @endif>
                                {{ trans('plugins/ecommerce::products.form.24 Hour') }}</option>
                            <option value="2 Days" @if (old('delivery_time') == '2 Days' || (isset($product) && $product->delivery_time == '2 Days')) selected @endif>
                                {{ trans('plugins/ecommerce::products.form.2 Days') }}</option>
                            <option value="5 Days" @if (old('delivery_time') == '5 Days' || (isset($product) && $product->delivery_time == '5 Days')) selected @endif>
                                {{ trans('plugins/ecommerce::products.form.5 Days') }}</option>
                            <option value="1 Week" @if (old('delivery_time') == '1 Week' || (isset($product) && $product->delivery_time == '1 Week')) selected @endif>
                                {{ trans('plugins/ecommerce::products.form.1 Week') }}</option>
                            <option value="2 Weeks" @if (old('delivery_time') == '2 Weeks' || (isset($product) && $product->delivery_time == '2 Weeks')) selected @endif>
                                {{ trans('plugins/ecommerce::products.form.2 Weeks') }}</option>
                        </select>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-outline-success">{{ __('Next') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection



@push('scripts')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_2.js') }}"></script>
@endpush

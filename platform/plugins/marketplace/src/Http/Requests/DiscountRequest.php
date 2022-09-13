<?php

namespace Botble\Marketplace\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use MarketplaceHelper;

class DiscountRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'       => 'nullable|max:255',
            'code'        => 'required|max:20|unique:ec_discounts,code',
            'value'       => 'required|numeric|min:0',
            'type_option' => 'required|' . Rule::in(array_keys(MarketplaceHelper::discountTypes())),
            'quantity'    => 'required_without:is_unlimited|numeric|min:1',
            'start_date'  => 'nullable|date|date_format:d-m-Y',
            'end_date'    => 'nullable|date|date_format:d-m-Y|after:start_date',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'title.required_if' => trans('plugins/ecommerce::discount.create_discount_validate_title_required_if'),
            'value.required'    => trans('plugins/ecommerce::discount.create_discount_validate_value_required'),
            'target.required'   => trans('plugins/ecommerce::discount.create_discount_validate_target_required'),
        ];
    }
}

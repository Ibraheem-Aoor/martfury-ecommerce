<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ProductRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => 'required|max:120',
            'price'      => 'numeric|nullable|min:0|max:100000000',
            'start_date' => 'date|nullable|required_if:sale_type,1',
            'end_date'   => 'date|nullable|after:' . ($this->input('start_date') ?? now()->toDateTimeString()),
            'wide'       => 'numeric|nullable|min:0|max:100000000',
            'height'     => 'numeric|nullable|min:0|max:100000000',
            'weight'     => 'numeric|nullable|min:0|max:100000000',
            'length'     => 'numeric|nullable|min:0|max:100000000',
            'status'     => Rule::in(BaseStatusEnum::values()),
            'quantity'   => 'numeric|nullable|min:0|max:100000000',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'          => trans('plugins/ecommerce::products.product_create_validate_name_required'),
            'sale_price.max'         => trans('plugins/ecommerce::products.product_create_validate_sale_price_max'),
            'sale_price.required_if' => trans('plugins/ecommerce::products.product_create_validate_sale_price_required_if'),
            'end_date.after'         => trans('plugins/ecommerce::products.product_create_validate_end_date_after'),
            'start_date.required_if' => trans('plugins/ecommerce::products.product_create_validate_start_date_required_if'),
            'sale_price'             => trans('plugins/ecommerce::products.product_create_validate_sale_price'),
        ];
    }
}

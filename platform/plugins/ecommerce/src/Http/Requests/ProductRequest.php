<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Contracts\Validation\Rule as ValidationRule;
use Illuminate\Support\Facades\Route;
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
        $basic_rules = [
            'name'       => 'required|max:120',
            // 'price'      => 'numeric|required|min:1|max:100000000',
            'start_date' => 'date|nullable|required_if:sale_type,1',
            'end_date'   => 'date|nullable|after:' . ($this->input('start_date') ?? now()->toDateTimeString()),
            'wide'       => 'numeric|sometimes|min:0|max:100000000',
            'height'     => 'numeric|sometimes|min:0|max:100000000',
            'weight'     => 'numeric|sometimes|min:0|max:100000000',
            'length'     => 'numeric|sometimes|min:0|max:100000000',
            'status'     => Rule::in(BaseStatusEnum::values()),
            'quantity'   => 'numeric|sometimes|min:1|max:100000000',
            'ean_code' => (Route::currentRouteName() != 'products.edit.update' &&  Route::currentRouteName() != 'marketplace.vendor.products.edit.update' ) ? 'required|digits:13' : 'nullable',
            'description' => 'nullable',
            'content' => 'required',
            'deliverables' => 'nullable',
            'images' => 'required',
            'added_attributes.*' => 'required',
        ];
        $vendor_rules = [
            'is_guaranteed' => 'required' ,
            'guarantee' => 'sometimes' ,
            'attr_weight' => 'required|numeric',
            'attr_height' => 'required|numeric',
            'attr_width' => 'required|numeric',
            'attr_length' => 'required|numeric',
            'product_country' => 'required',
            'packaging_language' => 'required',
            'product_meterial' => 'required',
            'peice_count' => 'required',
            'delivery_time' => 'required' ,
            'added_attributes' => 'required',
        ];
        return auth('customer')->check() ?  array_merge($basic_rules , $vendor_rules) : $basic_rules;
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
            'is_refunded.required' => trans('plugins/ecommerce::products.is_refunded'),
            'added_attributes.*'=> trans('plugins/ecommerce::products.form.no_attributes_selected'),
        ];
    }
}

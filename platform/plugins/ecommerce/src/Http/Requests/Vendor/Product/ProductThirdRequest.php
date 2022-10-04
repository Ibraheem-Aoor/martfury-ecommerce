<?php

namespace Botble\Ecommerce\Http\Requests\Vendor\Product;

use Botble\Support\Http\Requests\Request;

class ProductThirdRequest extends Request
{
    public function rules()
    {
        return [
            'attr_weight' => 'required|numeric',
            'attr_height' => 'required|numeric',
            'attr_width' => 'required|numeric',
            'attr_length' => 'required|numeric',
            'product_country' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'attr_weight.required' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'attr_height.required' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'attr_width.required' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'attr_length.required' =>  trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'product_country.required' => trans('plugins/ecommerce::products.product_create_validate_product_country_required'),
        ];
    }
}

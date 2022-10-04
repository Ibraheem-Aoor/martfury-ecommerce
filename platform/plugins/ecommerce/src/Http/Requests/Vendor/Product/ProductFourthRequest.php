<?php

namespace Botble\Ecommerce\Http\Requests\Vendor\Product;

use Botble\Support\Http\Requests\Request;

class ProductFourthRequest extends Request
{
    public function rules()
    {
        return [
            'wide'       => 'numeric|required|min:0|max:100000000',
            'height'     => 'numeric|required|min:0|max:100000000',
            'weight'     => 'numeric|required|min:0|max:100000000',
            'length'     => 'numeric|required|min:0|max:100000000',
            'is_guaranteed' => 'required' ,
            'guarantee' => 'sometimes' ,
        ];
    }

    public function messages()
    {
        return [
            'weight.required' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'height.required' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'width.required' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'length.required' =>  trans('plugins/ecommerce::products.product_create_validate_attr_wright_required'),
            'weight.numeric' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_number'),
            'height.numeric' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_number'),
            'width.numeric' => trans('plugins/ecommerce::products.product_create_validate_attr_wright_number'),
            'length.numeric' =>  trans('plugins/ecommerce::products.product_create_validate_attr_wright_number'),
        ];
    }
}

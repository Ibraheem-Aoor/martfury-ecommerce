<?php

namespace Botble\Ecommerce\Http\Requests\Vendor\Product;

use Botble\Support\Http\Requests\Request;

class ProductFiffthRequest extends Request
{
    public function rules()
    {
        return [
            'images' => 'required',
            'added_attributes.*' => 'required',
            'added_attributes' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'added_attributes.*'=> trans('plugins/ecommerce::products.form.no_attributes_selected'),
            'added_attributes'=> trans('plugins/ecommerce::products.form.no_attributes_selected'),
        ];
    }
}

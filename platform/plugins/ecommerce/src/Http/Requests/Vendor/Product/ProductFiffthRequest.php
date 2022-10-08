<?php

namespace Botble\Ecommerce\Http\Requests\Vendor\Product;

use Botble\Support\Http\Requests\Request;

class ProductFiffthRequest extends Request
{
    public function rules()
    {
        return [
            'images' => 'required',
            'image_input' => 'required',
            'added_attributes.*' => 'required',
            'added_attributes' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'added_attributes.*'=> trans('plugins/ecommerce::products.form.no_attributes_selected'),
            'added_attributes'=> trans('plugins/ecommerce::products.form.no_attributes_selected'),
            'image_input.required'=> trans('plugins/ecommerce::products.featured_image_required'),
        ];
    }
}

<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class BrandUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation_admin = [
            'name'   => 'required|unique:ec_brands,name,'.$this->segment(5),
            'slug'   => 'required',
            'order'  => 'required|integer|min:0|max:10000',
            'status' => Rule::in(BaseStatusEnum::values()),
            'website' => 'required|url',
        ];

        if(auth('customer')->check()){
            $validation_vendor = [
                'logo_input' => 'required',
            ];
        }

        return array_merge($validation_admin , $validation_vendor ?? []);
    }


    public function messages()
    {
        return [
            'logo_input.required' => 'Brand Image Required',
        ];
    }
}

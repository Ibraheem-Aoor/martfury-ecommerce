<?php

namespace Botble\Marketplace\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class StoreRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                  => 'required',
            'customer_id'           => 'required',
            'description'           => 'max:400',
            'status'                => Rule::in(BaseStatusEnum::values()),
            'bank_info.name'        => 'max:120',
            'bank_info.number'      => 'max:60',
            'bank_info.full_name'   => 'max:120',
            'bank_info.description' => 'max:500',
        ];
    }
}

<?php

namespace Botble\Marketplace\Http\Requests;

use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class WithdrawalRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images'      => 'nullable|array',
            'status'      => Rule::in(WithdrawalStatusEnum::values()),
            'description' => 'nullable|max:400',
        ];
    }
}

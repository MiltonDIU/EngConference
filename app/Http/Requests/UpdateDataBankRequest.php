<?php

namespace App\Http\Requests;

use App\Models\DataBank;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDataBankRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('data_bank_edit');
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'unique:data_banks,email,' . request()->route('data_bank')->id,
            ],
            'is_subscribe' => [
                'required',
            ],
            'name' => [
                'string',
                'nullable',
            ],
            'unsubscribe_link' => [
                'string',
                'nullable',
            ],
            'data_bank_categories.*' => [
                'integer',
            ],
            'data_bank_categories' => [
                'required',
                'array',
            ],
        ];
    }
}

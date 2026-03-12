<?php

namespace App\Http\Requests;

use App\Models\DataBankCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDataBankCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('data_bank_category_create');
    }

    public function rules()
    {
        return [
            'title_of_data_bank' => [
                'string',
                'required',
            ],
            'is_active' => [
                'required',
            ],
        ];
    }
}

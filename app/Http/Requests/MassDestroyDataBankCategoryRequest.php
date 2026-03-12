<?php

namespace App\Http\Requests;

use App\Models\DataBankCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDataBankCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('data_bank_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:data_bank_categories,id',
        ];
    }
}

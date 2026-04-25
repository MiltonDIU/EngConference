<?php

namespace App\Http\Requests;

use App\Models\UploadMedium;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreUploadMediumRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('upload_medium_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'nullable',
            ],
            'file_name' => [
                'array',
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\UploadMedium;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUploadMediumRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('upload_medium_edit');
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

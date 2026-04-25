<?php

namespace App\Http\Requests;

use App\Models\UploadMedium;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyUploadMediumRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('upload_medium_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:upload_media,id',
        ];
    }
}

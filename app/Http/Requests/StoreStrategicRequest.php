<?php

namespace App\Http\Requests;

use App\Models\StrategicPartner;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreStrategicRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('strategic_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
            ],
        ];
    }
}

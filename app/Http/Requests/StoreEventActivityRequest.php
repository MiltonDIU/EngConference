<?php

namespace App\Http\Requests;

use App\Models\EventActivity;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreEventActivityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('event_activity_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'nullable',
            ],
            'link' => [
                'string',
                'nullable',
            ],
            'is_active' => [
                'required',
            ],
        ];
    }
}

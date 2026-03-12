<?php

namespace App\Http\Requests;

use App\Models\EventActivity;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyEventActivityRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('event_activity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:event_activities,id',
        ];
    }
}

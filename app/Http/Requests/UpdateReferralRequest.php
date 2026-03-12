<?php

namespace App\Http\Requests;

use App\Models\Referral;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateReferralRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('referral_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'string',
                'required',
                'unique:referrals,email,' . request()->route('referral')->id,
            ],
            'coupon_id' => [
                'nullable',
                'numeric',
                Rule::unique('referrals', 'coupon_id')->ignore(request()->route('referral')->id)->where(function ($query) {
                    $query->whereNotNull('coupon_id');
                }),
            ],
            'is_active' => [
                'required',
            ],
        ];
    }
}

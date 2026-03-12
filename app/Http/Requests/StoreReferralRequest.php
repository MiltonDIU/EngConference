<?php

namespace App\Http\Requests;

use App\Models\Referral;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
class StoreReferralRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('referral_create');
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
                'unique:referrals',
            ],
            'coupon_id' => [
                'nullable',
                'numeric',
                Rule::unique('referrals')->where(function ($query) {
                    $query->whereNotNull('coupon_id');
                }),
            ],
            'is_active' => [
                'required',
            ],
        ];
    }
}

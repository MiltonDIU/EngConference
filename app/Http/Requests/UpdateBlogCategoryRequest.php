<?php

namespace App\Http\Requests;

use App\Models\BlogCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBlogCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('blog_category_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'min:2',
                'max:100',
                'required',
                'unique:blog_categories,title,' . request()->route('blog_category')->id,
            ],
            'slug' => [
                'string',
                'min:2',
                'max:100',
                'nullable',
            ],
            'is_active' => [
                'required',
            ],
        ];
    }
}

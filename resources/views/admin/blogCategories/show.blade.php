@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.blogCategory.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.blog-categories.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.id') }}
                        </th>
                        <td>
                            {{ $blogCategory->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.title') }}
                        </th>
                        <td>
                            {{ $blogCategory->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.slug') }}
                        </th>
                        <td>
                            {{ $blogCategory->slug }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.feature_image') }}
                        </th>
                        <td>
                            @if($blogCategory->feature_image)
                                <a href="{{ $blogCategory->feature_image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $blogCategory->feature_image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.is_active') }}
                        </th>
                        <td>
                            {{ App\Models\BlogCategory::IS_ACTIVE_SELECT[$blogCategory->is_active] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.summary') }}
                        </th>
                        <td>
                            {{ $blogCategory->summary }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.details') }}
                        </th>
                        <td>
                            {!! $blogCategory->details !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.blogCategory.fields.parent') }}
                        </th>
                        <td>
                            {{ $blogCategory->parent->title ?? '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.blog-categories.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection

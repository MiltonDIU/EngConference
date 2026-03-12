@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.post.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.posts.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.id') }}
                        </th>
                        <td>
                            {{ $post->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.title') }}
                        </th>
                        <td>
                            {{ $post->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.slug') }}
                        </th>
                        <td>
                            {{ $post->slug }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.feature_image') }}
                        </th>
                        <td>
                            @if($post->feature_image)
                                <a href="{{ $post->feature_image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $post->feature_image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.summary') }}
                        </th>
                        <td>
                            {{ $post->summary }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.details') }}
                        </th>
                        <td>
                            {!! $post->details !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.user') }}
                        </th>
                        <td>
                            {{ $post->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.blog_category') }}
                        </th>
                        <td>
                            {{ $post->blog_category->title ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.tag') }}
                        </th>
                        <td>
                            @foreach($post->tags as $key => $tag)
                                <span class="label label-info">{{ $tag->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.post.fields.is_active') }}
                        </th>
                        <td>
                            {{ App\Models\Post::IS_ACTIVE_SELECT[$post->is_active] ?? '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.posts.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('global.relatedData') }}
        </div>
        <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
            <li class="nav-item">
                <a class="nav-link" href="#post_comments" role="tab" data-toggle="tab">
                    {{ trans('cruds.comment.title') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="post_comments">
                @includeIf('admin.posts.relationships.postComments', ['comments' => $post->postComments])
            </div>
        </div>
    </div>

@endsection

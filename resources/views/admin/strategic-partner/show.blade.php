@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.strategic.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.strategic.fields.id') }}
                        </th>
                        <td>
                            {{ $strategic->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.strategic.fields.name') }}
                        </th>
                        <td>
                            {{ $strategic->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.strategic.fields.logo') }}
                        </th>
                        <td>
                            @if($strategic->logo)
                                <a href="{{ $strategic->logo->getUrl() }}" target="_blank">
                                    <img src="{{ $strategic->logo->getUrl('thumb') }}" width="50px" height="50px">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.strategic.fields.link') }}
                        </th>
                        <td>
                            {{ $strategic->link }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>


    </div>
</div>
@endsection

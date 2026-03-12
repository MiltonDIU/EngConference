@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.dataBank.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.data-banks.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBank.fields.id') }}
                        </th>
                        <td>
                            {{ $dataBank->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBank.fields.email') }}
                        </th>
                        <td>
                            {{ $dataBank->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBank.fields.is_subscribe') }}
                        </th>
                        <td>
                            {{ App\Models\DataBank::IS_SUBSCRIBE_SELECT[$dataBank->is_subscribe] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBank.fields.name') }}
                        </th>
                        <td>
                            {{ $dataBank->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBank.fields.unsubscribe_link') }}
                        </th>
                        <td>
                            {{ $dataBank->unsubscribe_link }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBank.fields.data_bank_category') }}
                        </th>
                        <td>
                            @foreach($dataBank->data_bank_categories as $key => $data_bank_category)
                                <span class="label label-info">{{ $data_bank_category->title_of_data_bank }}</span>
                            @endforeach
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.data-banks.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection

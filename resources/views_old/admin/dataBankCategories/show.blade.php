@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.dataBankCategory.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.data-bank-categories.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBankCategory.fields.id') }}
                        </th>
                        <td>
                            {{ $dataBankCategory->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBankCategory.fields.title_of_data_bank') }}
                        </th>
                        <td>
                            {{ $dataBankCategory->title_of_data_bank }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.dataBankCategory.fields.is_active') }}
                        </th>
                        <td>
                            {{ App\Models\DataBankCategory::IS_ACTIVE_SELECT[$dataBankCategory->is_active] ?? '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.data-bank-categories.index') }}">
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
                <a class="nav-link" href="#data_bank_category_data_banks" role="tab" data-toggle="tab">
                    {{ trans('cruds.dataBank.title') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="data_bank_category_data_banks">
                @includeIf('admin.dataBankCategories.relationships.dataBankCategoryDataBanks', ['dataBanks' => $dataBankCategory->dataBankCategoryDataBanks])
            </div>
        </div>
    </div>

@endsection

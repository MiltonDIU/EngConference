@extends('layouts.admin')
@section('content')
    <div class="card">

            <div class="card-body">
                <form action="{{ route('data-banks.dataBankSendEmail') }}" method="POST">
                    @csrf
                    <div class="form-group {{ $errors->has('email_body') ? 'has-error' : ''}}">
                        <div class="row">
                            <label for="name" class="col-md-3">Select Message*</label>
                            <div class="col-md-9">
                                <select name="email_id" class="form-control">
                                    @foreach($emails as $email)
                                        <option value="{{ $email->id }}">{{ $email->subject }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="form-group {{ $errors->has('permissions') ? 'has-error' : '' }}">
                        <div class="row">
                            <label for="name" class="col-md-3">Select Group*</label>
                            <div class="col-md-9">
                                <label for="permissions">
                                    <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                                    <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span>
                                </label>
                                <select name="data_bank_categories[]" id="permissions" class="form-control select2" multiple="multiple" required>
                                    @foreach($dataBankCategories as $dataBankCategory)
                                        <option value="{{ $dataBankCategory->id??'' }}" > {{ $dataBankCategory->title_of_data_bank??'' }}</option>
                                    @endforeach


                                </select>
                                @if($errors->has('permissions'))
                                    <p class="help-block">
                                        {{ $errors->first('permissions') }}
                                    </p>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.role.fields.permissions_helper') }}
                                </p>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-1 offset-3 form-group">
                        <input type="submit" name="submit" value="Send" class="btn btn-success">
                    </div>
                </form>
            </div>

    </div>
@endsection

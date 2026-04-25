@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edit Committee Type
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.committee-types.update', [$committeeType->id]) }}">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">Name</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $committeeType->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                @endif
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

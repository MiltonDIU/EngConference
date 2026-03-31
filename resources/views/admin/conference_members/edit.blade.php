@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edit Committee  Member
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.conference-members.update', [$conferenceMember->id]) }}">
            @method('PUT')
            @csrf

            <div class="form-group">
                <label class="required" for="name">Name</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $conferenceMember->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="designation">Designation</label>
                <input class="form-control {{ $errors->has('designation') ? 'is-invalid' : '' }}" type="text" name="designation" id="designation" value="{{ old('designation', $conferenceMember->designation) }}">
                @if($errors->has('designation'))
                    <div class="invalid-feedback">{{ $errors->first('designation') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="institution">Institution</label>
                <input class="form-control {{ $errors->has('institution') ? 'is-invalid' : '' }}" type="text" name="institution" id="institution" value="{{ old('institution', $conferenceMember->institution) }}">
                @if($errors->has('institution'))
                    <div class="invalid-feedback">{{ $errors->first('institution') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', $conferenceMember->email) }}">
                @if($errors->has('email'))
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="profile_url">Profile URL</label>
                <input class="form-control {{ $errors->has('profile_url') ? 'is-invalid' : '' }}" type="text" name="profile_url" id="profile_url" value="{{ old('profile_url', $conferenceMember->profile_url) }}">
                @if($errors->has('profile_url'))
                    <div class="invalid-feedback">{{ $errors->first('profile_url') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="is_active">Active</label>
                <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active">
                    <option value="1" {{ old('is_active', $conferenceMember->is_active) == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('is_active', $conferenceMember->is_active) == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <hr>
            <h4>Associate Committees</h4>
            <div class="table-responsive">
                <table class="table table-bordered" id="committees-table">
                    <thead>
                        <tr>
                            <th width="40%">Committee</th>
                            <th>Role</th>
                            <th>Level</th>
                            <th>Sort Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $committee_index = 0; @endphp
                        @if(old('committees'))
                            @foreach(old('committees') as $index => $oldCommittee)
                                <tr data-index="{{ $index }}">
                                    <td>
                                        <select name="committees[{{ $index }}][id]" class="form-control select2" required>
                                            <option value="">-- Select Committee (Parent > Sub) --</option>
                                            @foreach($committees as $id => $name)
                                                <option value="{{ $id }}" {{ $oldCommittee['id'] == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="committees[{{ $index }}][role]" class="form-control" value="{{ $oldCommittee['role'] }}"></td>
                                    <td><input type="text" name="committees[{{ $index }}][level]" class="form-control" value="{{ $oldCommittee['level'] }}"></td>
                                    <td><input type="number" name="committees[{{ $index }}][sort_order]" class="form-control" value="{{ $oldCommittee['sort_order'] ?? 0 }}"></td>
                                    <td><button type="button" class="btn btn-danger remove-committee">Remove</button></td>
                                </tr>
                                @php $committee_index = $index + 1; @endphp
                            @endforeach
                        @else
                            @foreach($conferenceMember->committees as $index => $associatedCommittee)
                                <tr data-index="{{ $index }}">
                                    <td>
                                        <select name="committees[{{ $index }}][id]" class="form-control select2" required>
                                            <option value="">-- Select Committee --</option>
                                            @foreach($committees as $groupName => $options)
                                                <optgroup label="{{ $groupName }}">
                                                    @foreach($options as $id => $name)
                                                        <option value="{{ $id }}" {{ (isset($oldCommittee['id']) ? $oldCommittee['id'] : $associatedCommittee->id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="committees[{{ $index }}][role]" class="form-control" value="{{ $associatedCommittee->pivot->role }}"></td>
                                    <td><input type="text" name="committees[{{ $index }}][level]" class="form-control" value="{{ $associatedCommittee->pivot->level }}"></td>
                                    <td><input type="number" name="committees[{{ $index }}][sort_order]" class="form-control" value="{{ $associatedCommittee->pivot->sort_order ?? 0 }}"></td>
                                    <td><button type="button" class="btn btn-danger remove-committee">Remove</button></td>
                                </tr>
                                @php $committee_index = $index + 1; @endphp
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary" id="add-committee">Add Committee</button>
            </div>

            <div class="form-group mt-4">
                <button class="btn btn-danger" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let committeeIndex = {{ $committee_index }};

        $('#add-committee').click(function() {
            let row = `
                <tr data-index="${committeeIndex}">
                    <td>
                        <select name="committees[${committeeIndex}][id]" class="form-control select2-dynamic" required>
                            <option value="">-- Select Committee --</option>
                            @foreach($committees as $groupName => $options)
                                <optgroup label="{{ $groupName }}">
                                    @foreach($options as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="committees[${committeeIndex}][role]" class="form-control"></td>
                    <td><input type="text" name="committees[${committeeIndex}][level]" class="form-control"></td>
                    <td><input type="number" name="committees[${committeeIndex}][sort_order]" class="form-control" value="0"></td>
                    <td><button type="button" class="btn btn-danger remove-committee">Remove</button></td>
                </tr>
            `;
            $('#committees-table tbody').append(row);
            $('.select2-dynamic').select2();
            committeeIndex++;
        });

        $(document).on('click', '.remove-committee', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection

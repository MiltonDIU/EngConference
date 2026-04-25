@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Create Committee
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.committees.store') }}">
            @csrf

            <div class="form-group">
                <label class="required" for="committee_type_id">Committee Type</label>
                <select class="form-control select2 {{ $errors->has('committee_type_id') ? 'is-invalid' : '' }}" name="committee_type_id" id="committee_type_id" required>
                    <option value="">-- Select Type --</option>
                    @foreach($committeeTypes as $id => $name)
                        <option value="{{ $id }}" {{ old('committee_type_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @if($errors->has('committee_type_id'))
                    <div class="invalid-feedback">{{ $errors->first('committee_type_id') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="parent_id">Parent Committee</label>
                <select class="form-control select2 {{ $errors->has('parent_id') ? 'is-invalid' : '' }}" name="parent_id" id="parent_id">
                    <option value="0">-- Root Committee --</option>
                    @foreach($parentCommittees as $groupName => $options)
                        <optgroup label="{{ $groupName }}">
                            @foreach($options as $id => $name)
                                <option value="{{ $id }}" {{ (old('parent_id') ? old('parent_id') : (isset($committee) ? $committee->parent_id : '')) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @if($errors->has('parent_id'))
                    <div class="invalid-feedback">{{ $errors->first('parent_id') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">Name</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="section">Section</label>
                <input class="form-control {{ $errors->has('section') ? 'is-invalid' : '' }}" type="text" name="section" id="section" value="{{ old('section', '') }}">
                @if($errors->has('section'))
                    <div class="invalid-feedback">{{ $errors->first('section') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', '') }}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input class="form-control {{ $errors->has('sort_order') ? 'is-invalid' : '' }}" type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}">
                @if($errors->has('sort_order'))
                    <div class="invalid-feedback">{{ $errors->first('sort_order') }}</div>
                @endif
            </div>

            <hr>
            <h4>Associate Members</h4>
            <div class="table-responsive">
                <table class="table table-bordered" id="members-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Role</th>
                            <th>Level</th>
                            <th>Sort Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $member_index = 0; @endphp
                        @if(old('members'))
                            @foreach(old('members') as $index => $oldMember)
                                <tr data-index="{{ $index }}">
                                    <td>
                                        <select name="members[{{ $index }}][id]" class="form-control select2" required>
                                            <option value="">-- Select Member --</option>
                                            @foreach($members as $member)
                                                <option value="{{ $member->id }}" {{ $oldMember['id'] == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="members[{{ $index }}][role]" class="form-control" value="{{ $oldMember['role'] }}"></td>
                                    <td><input type="text" name="members[{{ $index }}][level]" class="form-control" value="{{ $oldMember['level'] }}"></td>
                                    <td><input type="number" name="members[{{ $index }}][sort_order]" class="form-control" value="{{ $oldMember['sort_order'] ?? 0 }}"></td>
                                    <td><button type="button" class="btn btn-danger remove-member">Remove</button></td>
                                </tr>
                                @php $member_index = $index + 1; @endphp
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary" id="add-member">Add Member</button>
            </div>

            <div class="form-group mt-4">
                <button class="btn btn-danger" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let memberIndex = {{ $member_index }};

        $('#add-member').click(function() {
            let row = `
                <tr data-index="${memberIndex}">
                    <td>
                        <select name="members[${memberIndex}][id]" class="form-control select2-dynamic" required>
                            <option value="">-- Select Member --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="members[${memberIndex}][role]" class="form-control"></td>
                    <td><input type="text" name="members[${memberIndex}][level]" class="form-control"></td>
                    <td><input type="number" name="members[${memberIndex}][sort_order]" class="form-control" value="0"></td>
                    <td><button type="button" class="btn btn-danger remove-member">Remove</button></td>
                </tr>
            `;
            $('#members-table tbody').append(row);
            $('.select2-dynamic').select2();
            memberIndex++;
        });

        $(document).on('click', '.remove-member', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection

@extends('layouts.admin')
@section('content')
    @can('events_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("admin.domain.create") }}">
                    Add Domain
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            Concern Domain
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-events">
                    <thead>
                    <tr>
                        <th>

                        </th>
                        <th>
                            Concern Name
                        </th>
                        <th>
                            Domain Name
                        </th>
                        <th>
                            User
                        </th>
                        <th>
                            Total Registration
                        </th>
                        <th>
                            Paid Registration
                        </th>
                        <th>
                            Use Status
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($domains as $key => $domain)
                        <tr data-entry-id="{{ $domain->id }}">
                            <td>
                                {{ $domain->id ?? '' }}
                            </td>
                            <td>
                                {{ $domain->concern_name ?? '' }}
                            </td>
                            <td>
                                {{ $domain->domain_name ?? '' }}
                            </td>
                            <td>
                                {{ $domain->user->name ?? '' }}
                            </td>
                            <td>
                                {{ \App\Models\Domain::countProfile($domain->domain_name) }}
                            </td>

                            <td>
                                {{ \App\Models\Domain::countProfilePaid($domain->domain_name) }}
                            </td>
                            <td>
                                {{ $domain->status == 1 ?'Publish':'UnPublish' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.domain.edit',$domain->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="{{ route('admin.domain.destroy',$domain->id) }}" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

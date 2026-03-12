@extends('layouts.admin')
@section('content')
    @can('custom_email_access')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("admin.custom-mail.create") }}">
                    Add Custom Email Template
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            Coupons
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-events">
                    <thead>
                    <tr>
                        <th>

                        </th>
                        <th>
                            Subject
                        </th>
                        <th>
                            Email body
                        </th>

                        <th>
                            User
                        </th>
                        <th>
                            Publication Status
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i = 1 @endphp
                    @foreach($mails as $key => $mail)
                        <tr data-entry-id="{{ $mail->id }}">

                            <td>
                                {{ $i++ }}
                            </td>
                            <td>
                                {{ $mail->subject ?? '' }}
                            </td>
                            <td>
                                {!! $mail->mail_body ?? '' !!}
                            </td>

                            <td>
                                {{ $mail->user->name ?? '' }}
                            </td>
                            <td>
                                {{ $mail->publication_status == 1 ?'Publish':'UnPublish' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.custom-mail.edit',$mail->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="{{ route('admin.custom-mail.destroy',$mail->id) }}" class="btn btn-danger btn-sm">Delete</a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

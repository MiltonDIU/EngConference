@extends('layouts.admin')
@section('content')
    @can('upload_medium_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.upload-media.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.uploadMedium.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.uploadMedium.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-UploadMedium">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.uploadMedium.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.uploadMedium.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.uploadMedium.fields.file_name') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($uploadMedia as $key => $uploadMedium)
                        <tr data-entry-id="{{ $uploadMedium->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $uploadMedium->id ?? '' }}
                            </td>
                            <td>
                                {{ $uploadMedium->title ?? '' }}
                            </td>
                            <td>
                                @foreach($uploadMedium->file_name as $key => $media)
                                    @php
                                        $url = $media->getUrl();
                                        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                    @endphp
                                    <div>
                                        @if(in_array($extension, $imageExtensions))
                                            <a href="{{ $media->getUrl() }}">
                                            <img src="{{ $media->getUrl('preview') }}" />
                                            </a>
{{--                                            <p>{{ $media->getUrl() }}</p>--}}
                                            <button onclick="copyToClipboard(this, '{{ $media->getUrl('preview') }}','Preview Pic')">Preview Pic</button>
                                            <button onclick="copyToClipboard(this, '{{ $media->getUrl() }}','Original Pic')">Original Pic</button>
                                        @else
                                            <a href="{{ $media->getUrl() }}">
                                               Download File
                                            </a>
                                            <button onclick="copyToClipboard(this, '{{ $media->getUrl() }}','Copy Link')"> Copy Link</button>
                                        @endif
                                    </div>

                                @endforeach

                            </td>
                            <td>
                                @can('upload_medium_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.upload-media.show', $uploadMedium->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('upload_medium_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.upload-media.edit', $uploadMedium->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('upload_medium_delete')
                                    <form action="{{ route('admin.upload-media.destroy', $uploadMedium->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('upload_medium_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.upload-media.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: { ids: ids, _method: 'DELETE' }})
                            .done(function () { location.reload() })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[ 1, 'desc' ]],
                pageLength: 100,
            });
            let table = $('.datatable-UploadMedium:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

        function copyToClipboard(button, url,text) {
            // Create a temporary input element to copy the text
            var input = document.createElement('input');
            input.style.opacity = 0;
            input.style.position = 'absolute';
            input.value = url;
            document.body.appendChild(input);
            // Select and copy the URL
            input.select();
            document.execCommand('copy');
            // Clean up
            document.body.removeChild(input);
            // Change the button text to indicate success
            button.innerText = 'Link Copied!';
            setTimeout(function () {
                button.innerText = text
            }, 2000); // Reset button text after 2 seconds
        }


    </script>
@endsection

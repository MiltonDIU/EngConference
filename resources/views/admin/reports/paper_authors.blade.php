@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="font-weight-bold text-dark">
                <i class="fas fa-users mr-2 text-primary"></i> Paid Paper Authors
                <small class="text-muted font-weight-normal" style="font-size: 0.9rem;">{{ $rows->count() }} paid paper(s)</small>
            </h3>
            <a href="{{ route('admin.paper-authors-report.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel mr-1"></i> Download Excel
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover table-bordered mb-0 align-middle w-100 small" id="paper-authors-report-table">
                    <thead>
                        <tr>
                            @foreach($headers as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                @foreach(\App\Services\PaperAuthorsReportService::cells($row, $coAuthorColumns) as $i => $value)
                                    <td @if($i === 0) class="text-nowrap font-weight-bold" @elseif($i === 1) style="min-width: 320px;" @endif>{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table td, .table th { vertical-align: middle; }
    /* Same navy header as the Excel template */
    #paper-authors-report-table thead th { background: #182C45; color: #fff; white-space: nowrap; text-align: center; }
    .card { border-radius: 12px; overflow: hidden; }
    .dataTables_wrapper .dataTables_filter { margin-bottom: 20px; }
</style>
@endsection

@section('scripts')
<script>
$(function () {
    $('#paper-authors-report-table').DataTable({
        retrieve: true,
        aaSorting: [],
        dom: 'lfrtip',
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        scrollX: true
    });
});
</script>
@endsection

@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h3 class="font-weight-bold text-dark">
                <i class="fas fa-chart-pie mr-2 text-primary"></i> Track & Sub-track Analytics
            </h3>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-end">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-road mr-1 text-primary"></i> Filter by Track
                    </label>
                    <select id="filter_track" class="form-control select2">
                        <option value="">All Tracks</option>
                        @foreach($tracks as $track)
                            <option value="{{ $track->name }}">{{ $track->name }} ({{ $track->papers_count }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="button" id="reset_filters" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-undo mr-1"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover mb-0 align-middle datatable-TracksReport w-100" id="tracks-report-table">
                    <thead class="bg-light text-muted text-uppercase small font-weight-bold">
                        <tr>
                            <th>Track Name</th>
                            <th>Sub-track Name</th>
                            <th class="text-center">Total Submissions</th>
                            <th class="text-center">Total Authors</th>
                            <th class="text-center">Submitting Users</th>
                            <th class="text-center">Approved & Paid</th>
                            <th class="text-center">Approved & Unpaid</th>
                            <th class="text-center">Pending Review</th>
                            {{-- <th class="text-right">Total Paid Amount</th> --}}
                            {{-- @foreach($currencies as $currency)
                                <th class="text-right">Paid ({{ $currency }})</th>
                            @endforeach --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $row)
                            <tr>
                                <td class="font-weight-bold text-dark">{{ $row->track_name }}</td>
                                <td class="text-muted">{{ $row->sub_track_name ?: 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light border font-weight-bold px-3 py-2 rounded-pill">{{ $row->total_submissions }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border font-weight-bold px-3 py-2 rounded-pill">{{ $row->total_authors }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border font-weight-bold px-3 py-2 rounded-pill">{{ $row->unique_submitters }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-3 py-2 rounded-pill font-weight-bold">{{ $row->paid_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning px-3 py-2 rounded-pill font-weight-bold">{{ $row->unpaid_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info px-3 py-2 rounded-pill font-weight-bold">{{ $row->pending_count }}</span>
                                </td>
                                {{-- <td class="text-right font-weight-bold text-success" style="font-size: 0.95rem;">
                                    {{ $row->paid_amount }}
                                </td> --}}
                                {{-- @foreach($currencies as $currency)
                                    <td class="text-right font-weight-bold text-success" style="font-size: 0.95rem;">
                                        {{ number_format($row->currency_amounts[$currency] ?? 0, 0) }}
                                    </td>
                                @endforeach --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-white { background-color: #fff; border-color: #dee2e6; }
    .btn-white:hover { background-color: #f8f9fa; }
    .table td, .table th { vertical-align: middle; }
    .card { border-radius: 12px; overflow: hidden; }
    .dataTables_wrapper .dataTables_filter { margin-bottom: 20px; }
    .dt-buttons { margin-bottom: 15px; }
    .dt-button {
        padding: 5px 15px !important;
        border-radius: 4px !important;
        font-size: 13px !important;
        border: 1px solid #dee2e6 !important;
        background: #fff !important;
        color: #495057 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
    }
    .dt-button:hover { background: #f8f9fa !important; }
</style>
@endsection

@section('scripts')
<script>
$(function () {
    let dtButtons = [
        {
            extend: 'excel',
            className: 'btn-default',
            text: '<i class="fas fa-file-excel mr-1 text-success"></i> Excel',
            titleAttr: 'Export to Excel',
            exportOptions: { columns: ':visible' }
        },
        {
            extend: 'csv',
            className: 'btn-default',
            text: '<i class="fas fa-file-csv mr-1 text-info"></i> CSV',
            titleAttr: 'Export to CSV',
            exportOptions: { columns: ':visible' }
        },
        {
            extend: 'pdf',
            className: 'btn-default',
            text: '<i class="fas fa-file-pdf mr-1 text-danger"></i> PDF',
            titleAttr: 'Export to PDF',
            exportOptions: { columns: ':visible' }
        },
        {
            extend: 'print',
            className: 'btn-default',
            text: '<i class="fas fa-print mr-1"></i> Print',
            titleAttr: 'Print Table',
            exportOptions: { columns: ':visible' }
        }
    ];

    let table = $('#tracks-report-table').DataTable({
        retrieve: true,
        aaSorting: [],
        dom: 'lBfrtip',
        buttons: dtButtons,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        columnDefs: [
            { orderable: true, targets: '_all' }
        ]
    });

    // Custom Track Filter logic
    $('#filter_track').on('change', function() {
        let val = $(this).val();
        table.column(0).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
    });

    // Reset Filters
    $('#reset_filters').on('click', function() {
        $('#filter_track').val('').trigger('change');
    });

    // Initialize Select2 if available
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: "Select a track",
            allowClear: true
        });
    }
});
</script>
@endsection

@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-file-invoice-dollar text-success mr-2"></i> Paper Payments & Author Members Report
            </h3>
            <p class="text-muted mb-0">
                Detailed audit of paid papers, author membership details, BDT base fair collection, and multi-currency conversions (USD, INR, EUR, BDT).
            </p>
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
            <span class="badge badge-light border px-3 py-2 text-muted shadow-sm">
                <i class="far fa-clock mr-1 text-primary"></i> Generated: {{ now()->format('d M Y, h:i A') }}
            </span>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card stat-card shadow-sm border-0 h-100 bg-white border-left-primary">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small font-weight-bold text-muted mb-1">Paid Paper Users</div>
                        <div class="h3 font-weight-bold text-dark mb-0">{{ number_format($uniqueUsersCount) }}</div>
                        <div class="small text-muted mt-1">Unique Submitting Accounts</div>
                    </div>
                    <div class="stat-icon bg-light-primary text-primary">
                        <i class="fas fa-user-check fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Paid Papers -->
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card stat-card shadow-sm border-0 h-100 bg-white border-left-info">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small font-weight-bold text-muted mb-1">Total Paid Papers</div>
                        <div class="h3 font-weight-bold text-info mb-0">{{ number_format($totalPaidPapersCount) }}</div>
                        <div class="small text-muted mt-1">Status: Validated & Paid</div>
                    </div>
                    <div class="stat-icon bg-light-info text-info">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Author Members -->
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card stat-card shadow-sm border-0 h-100 bg-white border-left-warning">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small font-weight-bold text-muted mb-1">Total Members / Authors</div>
                        <div class="h3 font-weight-bold text-warning mb-0">{{ number_format($totalAuthorMembersCount) }}</div>
                        <div class="small text-muted mt-1">{{ number_format($uniqueAuthorsCount) }} Unique Individuals</div>
                    </div>
                    <div class="stat-icon bg-light-warning text-warning">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Collected BDT -->
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card stat-card shadow-sm border-0 h-100 bg-white border-left-success">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small font-weight-bold text-muted mb-1">Total Collected (BDT)</div>
                        <div class="h3 font-weight-bold text-success mb-0">BDT {{ number_format($totalBaseFairBDT, 2) }}</div>
                        <div class="small text-muted mt-1">Gateway Base Fair Total</div>
                    </div>
                    <div class="stat-icon bg-light-success text-success">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Breakdown Row -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                <i class="fas fa-globe mr-2 text-primary"></i> Original Currency Breakdown (Gateway Value B / C / D)
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <!-- BDT -->
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="p-3 rounded border bg-light d-flex align-items-center">
                        <div class="currency-badge bg-primary text-white font-weight-bold mr-3">BDT</div>
                        <div>
                            <div class="small text-muted font-weight-bold">Bangladesh Taka</div>
                            <div class="font-weight-bold text-dark h5 mb-0">BDT {{ number_format($currencySummary['BDT']['amount'] ?? 0, 2) }}</div>
                            <div class="small text-primary font-weight-bold">{{ $currencySummary['BDT']['count'] ?? 0 }} Transactions</div>
                        </div>
                    </div>
                </div>

                <!-- INR -->
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="p-3 rounded border bg-light d-flex align-items-center">
                        <div class="currency-badge bg-info text-white font-weight-bold mr-3">INR</div>
                        <div>
                            <div class="small text-muted font-weight-bold">Indian Rupee</div>
                            <div class="font-weight-bold text-dark h5 mb-0">INR {{ number_format($currencySummary['INR']['amount'] ?? 0, 2) }}</div>
                            <div class="small text-info font-weight-bold">{{ $currencySummary['INR']['count'] ?? 0 }} Transactions</div>
                        </div>
                    </div>
                </div>

                <!-- USD -->
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="p-3 rounded border bg-light d-flex align-items-center">
                        <div class="currency-badge bg-success text-white font-weight-bold mr-3">USD</div>
                        <div>
                            <div class="small text-muted font-weight-bold">US Dollar</div>
                            <div class="font-weight-bold text-dark h5 mb-0">USD {{ number_format($currencySummary['USD']['amount'] ?? 0, 2) }}</div>
                            <div class="small text-success font-weight-bold">{{ $currencySummary['USD']['count'] ?? 0 }} Transactions</div>
                        </div>
                    </div>
                </div>

                <!-- EUR -->
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="p-3 rounded border bg-light d-flex align-items-center">
                        <div class="currency-badge bg-dark text-white font-weight-bold mr-3">EUR</div>
                        <div>
                            <div class="small text-muted font-weight-bold">Euro</div>
                            <div class="font-weight-bold text-dark h5 mb-0">EUR {{ number_format($currencySummary['EUR']['amount'] ?? 0, 2) }}</div>
                            <div class="small text-dark font-weight-bold">{{ $currencySummary['EUR']['count'] ?? 0 }} Transactions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <div class="row align-items-end">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-coins mr-1 text-primary"></i> Filter by Currency
                    </label>
                    <select id="filter_currency" class="form-control form-control-sm select2">
                        <option value="">All Currencies</option>
                        <option value="BDT">BDT (Bangladesh)</option>
                        <option value="INR">INR (India)</option>
                        <option value="USD">USD (International)</option>
                        <option value="EUR">EUR (Europe)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-road mr-1 text-primary"></i> Filter by Track
                    </label>
                    <select id="filter_track" class="form-control form-control-sm select2">
                        <option value="">All Tracks</option>
                        @foreach($tracks as $track)
                            <option value="{{ $track->name }}">{{ $track->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-users mr-1 text-primary"></i> Authors / Members
                    </label>
                    <select id="filter_author_type" class="form-control form-control-sm">
                        <option value="">All Types</option>
                        <option value="single">Single Member</option>
                        <option value="multi">Multiple Members</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="reset_filters" class="btn btn-outline-secondary btn-sm btn-block">
                        <i class="fas fa-undo mr-1"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table Card -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover table-striped mb-0 align-middle w-100" id="paper-payments-table">
                    <thead class="bg-light text-muted text-uppercase small font-weight-bold">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 220px;">Submitter (User)</th>
                            <th style="width: 260px;">Paper Details</th>
                            <th style="min-width: 280px;">Members / Authors</th>
                            <th class="text-right" style="width: 140px;">Original Fee</th>
                            <th class="text-center" style="width: 120px;">Exchange Rate</th>
                            <th class="text-right" style="width: 150px;">Paid Base Fair</th>
                            <th style="width: 200px;">Transaction Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($papers as $paper)
                            @php
                                $payInfo = $paperPaymentMap[$paper->id] ?? null;
                                $authorCount = $paper->authors->count();
                                $origCurr = $payInfo['orig_currency'] ?? ($paper->currency ?: 'BDT');
                                $origAmt = $payInfo['orig_amount'] ?? $paper->pay_amount;
                                $baseFair = $payInfo['base_fair'] ?? ($origCurr == 'BDT' ? $paper->pay_amount : 0);
                                $rate = $payInfo['exchange_rate'] ?? 1.0;
                                $user = $paper->user;
                                $profile = $user ? $user->profile : null;
                            @endphp
                            <tr data-currency="{{ $origCurr }}" data-track="{{ $paper->track->name ?? '' }}" data-authors-count="{{ $authorCount }}">
                                <td class="font-weight-bold text-muted">
                                    {{ $loop->iteration }}
                                    @if($paper->submission_id)
                                        <br><small class="badge badge-light border text-monospace">{{ $paper->submission_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($user)
                                        <div class="font-weight-bold text-dark">{{ $user->name }}</div>
                                        <div class="small text-muted"><i class="far fa-envelope mr-1"></i>{{ $user->email }}</div>
                                        @if($profile && $profile->whatsapp_number)
                                            <div class="small text-muted"><i class="fab fa-whatsapp mr-1 text-success"></i>{{ $profile->whatsapp_number }}</div>
                                        @endif
                                        @if($profile && $profile->country)
                                            <div class="small text-muted"><i class="fas fa-map-marker-alt mr-1 text-danger"></i>{{ $profile->country->name }}</div>
                                        @endif
                                        @if($profile && $profile->registration_id)
                                            <span class="badge badge-secondary mt-1">{{ $profile->registration_id }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted font-italic">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.95rem; line-height: 1.35;">
                                        {{ $paper->title }}
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge badge-info text-wrap" style="font-size: 0.75rem;">
                                            <i class="fas fa-road mr-1"></i>{{ $paper->track->name ?? 'No Track' }}
                                        </span>
                                        @if($paper->subTrack)
                                            <span class="badge badge-light border text-wrap ml-1" style="font-size: 0.75rem;">
                                                {{ $paper->subTrack->name }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                        <span class="badge badge-{{ $authorCount > 1 ? 'warning' : 'primary' }} px-2 py-1 rounded-pill">
                                            <i class="fas fa-users mr-1"></i>{{ $authorCount }} {{ Str::plural('Member', $authorCount) }}
                                        </span>
                                        @if($paper->has_multiple_authors)
                                            <small class="badge badge-light border">Multi-Author Paper</small>
                                        @endif
                                    </div>
                                    <div class="authors-list">
                                        @foreach($paper->authors as $author)
                                            <div class="author-item p-2 mb-1 rounded {{ $loop->odd ? 'bg-light' : 'bg-white border' }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <span class="font-weight-bold text-dark" style="font-size: 0.88rem;">{{ $loop->iteration }}. {{ $author->name }}</span>
                                                        @if($author->is_presenting_author)
                                                            <span class="badge badge-success px-1 py-0 ml-1" style="font-size: 0.68rem;" title="Presenting Author">Presenter</span>
                                                        @endif
                                                        @if($author->is_student)
                                                            <span class="badge badge-info px-1 py-0 ml-1" style="font-size: 0.68rem;" title="Student Member">Student</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($author->email)
                                                    <div class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-envelope mr-1"></i>{{ $author->email }}
                                                    </div>
                                                @endif
                                                @if($author->institution || $author->department)
                                                    <div class="text-muted text-truncate" style="font-size: 0.73rem;" title="{{ $author->institution }}">
                                                        <i class="fas fa-university mr-1"></i>{{ $author->institution ?: $author->department }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="font-weight-bold font-monospace" style="font-size: 1.02rem;">
                                        {{ $origCurr }} {{ number_format($origAmt, 2) }}
                                    </div>
                                    <span class="badge badge-{{ $origCurr == 'BDT' ? 'primary' : ($origCurr == 'USD' ? 'success' : ($origCurr == 'INR' ? 'info' : 'dark')) }} px-2 py-0" style="font-size: 0.72rem;">
                                        {{ $origCurr }}
                                    </span>
                                </td>
                                <td class="text-center font-monospace">
                                    @if($origCurr !== 'BDT' && $rate && $rate != 1.0)
                                        <span class="badge badge-warning px-2 py-1 font-weight-bold" style="font-size: 0.82rem;" title="1 {{ $origCurr }} = {{ $rate }} BDT">
                                            {{ number_format($rate, 4) }}
                                        </span>
                                        <div class="small text-muted font-weight-bold" style="font-size: 0.7rem;">BDT / {{ $origCurr }}</div>
                                    @else
                                        <span class="badge badge-light border text-muted px-2 py-1 font-weight-bold" style="font-size: 0.82rem;">1.0000</span>
                                        <div class="small text-muted" style="font-size: 0.7rem;">BDT / BDT</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="font-weight-bold text-success font-monospace" style="font-size: 1.05rem;">
                                        BDT {{ number_format($baseFair, 2) }}
                                    </div>
                                    <small class="badge badge-light border text-muted">Base Fair</small>
                                </td>
                                <td>
                                    @if($payInfo)
                                        <div class="small font-weight-bold text-dark font-monospace text-truncate" title="Tran ID: {{ $payInfo['tran_id'] }}">
                                            <i class="fas fa-barcode mr-1 text-primary"></i>{{ $payInfo['tran_id'] }}
                                        </div>
                                        @if($payInfo['bank_tran_id'])
                                            <div class="small text-muted font-monospace text-truncate" title="Bank Tran ID: {{ $payInfo['bank_tran_id'] }}">
                                                <i class="fas fa-university mr-1"></i>{{ $payInfo['bank_tran_id'] }}
                                            </div>
                                        @endif
                                        @if($payInfo['tran_date'])
                                            <div class="small text-muted" style="font-size: 0.75rem;">
                                                <i class="far fa-calendar-alt mr-1"></i>{{ $payInfo['tran_date'] }}
                                            </div>
                                        @endif
                                        <div class="mt-1">
                                            <span class="badge badge-success px-2 py-0" style="font-size: 0.7rem;">VALIDATED</span>
                                            @if($payInfo['card_type'])
                                                <span class="badge badge-light border text-truncate px-1 py-0 ml-1" style="font-size: 0.68rem; max-width: 120px;" title="{{ $payInfo['card_type'] }}">
                                                    {{ $payInfo['card_type'] }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge badge-secondary">Pending Sync</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
    }
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    
    .bg-light-primary { background-color: rgba(78, 115, 223, 0.12); }
    .bg-light-info { background-color: rgba(54, 185, 204, 0.12); }
    .bg-light-warning { background-color: rgba(246, 194, 62, 0.15); }
    .bg-light-success { background-color: rgba(28, 200, 138, 0.12); }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .currency-badge {
        width: 50px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .authors-list {
        max-height: 220px;
        overflow-y: auto;
    }
    .author-item {
        transition: background-color 0.15s;
    }
    .author-item:hover {
        background-color: #f1f5f9 !important;
    }
    .font-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
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
            title: 'Paper_Payments_Report_' + '{{ now()->format("Ymd") }}',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                format: {
                    body: function (data, row, column, node) {
                        return $(node).text().replace(/\s+/g, ' ').trim();
                    }
                }
            }
        },
        {
            extend: 'csv',
            className: 'btn-default',
            text: '<i class="fas fa-file-csv mr-1 text-info"></i> CSV',
            title: 'Paper_Payments_Report_' + '{{ now()->format("Ymd") }}',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6], // Exclude Transaction Details (Column 7)
                format: {
                    body: function (data, row, column, node) {
                        return $(node).text().replace(/\s+/g, ' ').trim();
                    }
                }
            }
        },
        {
            extend: 'pdf',
            className: 'btn-default',
            text: '<i class="fas fa-file-pdf mr-1 text-danger"></i> PDF',
            orientation: 'landscape',
            pageSize: 'A4',
            title: 'Paper Payments & Members Report',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            }
        },
        {
            extend: 'print',
            className: 'btn-default',
            text: '<i class="fas fa-print mr-1"></i> Print',
            title: 'Paper Payments & Members Report',
            exportOptions: {
                columns: ':visible'
            }
        }
    ];

    let table = $('#paper-payments-table').DataTable({
        retrieve: true,
        aaSorting: [],
        dom: 'lBfrtip',
        buttons: dtButtons,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        columnDefs: [
            { orderable: false, targets: [3, 7] },
            { orderable: true, targets: [0, 1, 2, 4, 5, 6] }
        ]
    });

    // Custom Currency Filter
    $('#filter_currency').on('change', function() {
        let val = $(this).val();
        table.column(4).search(val ? val : '', true, false).draw();
    });

    // Custom Track Filter
    $('#filter_track').on('change', function() {
        let val = $(this).val();
        table.column(2).search(val ? val : '', true, false).draw();
    });

    // Custom Author Type Filter
    $('#filter_author_type').on('change', function() {
        let type = $(this).val();
        $.fn.dataTable.ext.search.pop(); // Remove prior custom search if any
        
        if (type === 'single') {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
                let rowNode = table.row(dataIndex).node();
                let count = parseInt($(rowNode).attr('data-authors-count')) || 0;
                return count === 1;
            });
        } else if (type === 'multi') {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
                let rowNode = table.row(dataIndex).node();
                let count = parseInt($(rowNode).attr('data-authors-count')) || 0;
                return count > 1;
            });
        }
        table.draw();
    });

    // Reset Filters
    $('#reset_filters').on('click', function() {
        $('#filter_currency').val('').trigger('change');
        $('#filter_track').val('').trigger('change');
        $('#filter_author_type').val('');
        $.fn.dataTable.ext.search.pop();
        table.search('').columns().search('').draw();
    });

    // Initialize Select2 if available
    if ($.fn.select2) {
        $('.select2').select2({
            allowClear: true
        });
    }
});
</script>
@endsection

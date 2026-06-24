@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h3 class="font-weight-bold text-dark">
                <i class="fas fa-file-alt mr-2 text-primary"></i> Submitted Abstracts
            </h3>
            @php
                $settings = \App\Models\Setting::pluck('value', 'key');
                $maxSubmissions = (int) ($settings['maximum_abstract_submission'] ?? $settings['maximum_abastract_submission'] ?? 1);
                $userPaperCount = auth()->user() ? \App\Models\Paper::where('user_id', auth()->id())->count() : 0;
                
                $eventStartDate = \Illuminate\Support\Carbon::parse($settings['registration_start_date'] ?? now());
                $abstractDeadline = \Illuminate\Support\Carbon::parse($settings['abstract_submission_deadline'] ?? $settings['registration_close_date'] ?? now());
                $currentDate = \Illuminate\Support\Carbon::now();
                
                $isSubmissionOpen = (($settings['is_abstract_submission_open'] ?? 'true') == 'true') 
                    && ($currentDate >= $eventStartDate) 
                    && ($currentDate <= $abstractDeadline);
            @endphp
            @if(auth()->user()->roles->contains('id', 3) && $userPaperCount < $maxSubmissions && $isSubmissionOpen)
                <a href="{{ route('papers.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Submit New Abstract
                </a>
            @endif
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 8px;">
            <i class="fas fa-check-circle mr-2 text-success"></i> {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 8px;">
            <i class="fas fa-check-circle mr-2 text-success"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 8px;">
            <i class="fas fa-exclamation-circle mr-2 text-danger"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($myProfile && auth()->user()->roles->contains('id', 3) && $unpaidPapers->count() > 0)
        @if(!$myProfile->author_list_confirmed)
            <div class="card mb-4 border-warning shadow-sm" style="border-width: 2px; border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-id-card mr-2"></i> Confirm Author List & Student Status
                    </h5>
                </div>
                <div class="card-body bg-white p-4">
                    <p class="text-muted mb-4">
                        Before proceeding to payment, please confirm the student status of all authors for your approved abstract(s). 
                        <strong>Bangladeshi student authors qualify for a flat registration fee of 2,000 BDT.</strong>
                    </p>
                    
                    <form action="{{ route('profile.confirm-student-status') }}" method="POST">
                        @csrf
                        
                        @foreach($unpaidPapers as $paper)
                            <div class="mb-4 p-3 bg-light rounded border">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-file-alt mr-1"></i> Paper ID: {{ $paper->submission_id }} - {{ $paper->title }}
                                </h6>
                                
                                <table class="table table-sm table-bordered bg-white mb-0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Author Name</th>
                                            <th>Email</th>
                                            <th>Designation</th>
                                            <th>Country</th>
                                            <th style="width: 180px;" class="text-center">Is Student?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paper->authors as $author)
                                            <tr>
                                                <td class="align-middle font-weight-bold">{{ $author->name }}</td>
                                                <td class="align-middle">{{ $author->email }}</td>
                                                <td class="align-middle">{{ $author->designation }}</td>
                                                <td class="align-middle">{{ $author->country->name ?? 'N/A' }}</td>
                                                <td class="align-middle text-center">
                                                    <input type="hidden" name="authors[{{ $author->id }}][id]" value="{{ $author->id }}">
                                                    <div class="student-status-toggle">
                                                        <input type="radio" id="student_yes_{{ $author->id }}" name="authors[{{ $author->id }}][is_student]" value="1" {{ $author->is_student ? 'checked' : '' }} required>
                                                        <label for="student_yes_{{ $author->id }}" class="toggle-btn toggle-yes">Yes</label>
                                                        
                                                        <input type="radio" id="student_no_{{ $author->id }}" name="authors[{{ $author->id }}][is_student]" value="0" {{ !$author->is_student ? 'checked' : '' }} required>
                                                        <label for="student_no_{{ $author->id }}" class="toggle-btn toggle-no">No</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                        
                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-warning btn-lg font-weight-bold shadow-sm">
                                <i class="fas fa-check-circle mr-1"></i> Confirm & Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius: 8px; background-color: #e8f5e9;">
                <i class="fas fa-check-circle mr-2 text-success"></i> 
                <strong>Author list and student status confirmed!</strong> You can now proceed with the payment for your approved abstract(s) in the table below.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-info-circle mr-1 text-primary"></i> Status Filter
                    </label>
                    <select id="filter_status" class="form-control form-control-sm select2">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-road mr-1 text-primary"></i> Track Filter
                    </label>
                    <select id="filter_track" class="form-control form-control-sm select2">
                        <option value="">All Tracks</option>
                        @foreach($tracks as $track)
                            <option value="{{ $track->id }}">{{ $track->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-credit-card mr-1 text-primary"></i> Payment Filter
                    </label>
                    <select id="filter_payment" class="form-control form-control-sm select2">
                        <option value="">All Payment Status</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>
            </div>
            <div class="row align-items-end">
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-building mr-1 text-primary"></i> Department
                    </label>
                    <input type="text" id="filter_department" class="form-control form-control-sm" placeholder="Search department...">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-university mr-1 text-primary"></i> University / Institute
                    </label>
                    <input type="text" id="filter_institution" class="form-control form-control-sm" placeholder="Search university/institute...">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-globe mr-1 text-primary"></i> Country
                    </label>
                    <select id="filter_country" class="form-control form-control-sm select2">
                        <option value="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mt-3 mt-md-0">
                    <button type="button" id="reset_filters" class="btn btn-sm btn-outline-secondary btn-block">
                        <i class="fas fa-undo mr-1"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover mb-0 align-middle datatable-Paper w-100" id="papers-table">
                    <thead class="bg-light text-muted text-uppercase small font-weight-bold">
                        <tr>
                            <th>Submission ID</th>
                            <th>Full Title</th>
                            <th>Submitted By</th>
                            <th>Designation</th>
                            <th>Authors</th>
                            <th>Total Member</th>
                            <th>Department</th>
                            <th>University</th>
                            <th>Country</th>
                            <th>Participation Mode</th>
                            <th>Pay Amount</th>
                            <th>Currency</th>
                            <th>Track</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Date</th>
                            <th>Abstract</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reusable Payment Review Modal -->
<div class="modal fade" id="paymentReviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark">Payment Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-3 pb-4" id="modal-content-area">
                <div class="text-center py-4" id="modal-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="modal-actual-content" style="display: none;">
                    <p class="text-muted mb-4 small">Review the payment details for your approved abstract before proceeding to checkout.</p>

                    <div class="bg-light p-3 rounded mb-4 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted font-weight-bold">Abstract ID</span>
                            <span class="font-weight-bold text-dark" id="modal-submission-id"></span>
                        </div>
                        <div id="modal-authors-section" class="mb-3 mt-2" style="display: none;">
                            <span class="text-muted font-weight-bold d-block mb-1" style="font-size: 0.85rem;">Authors List (<span id="modal-authors-count"></span>)</span>
                            <ul class="mb-0 small text-dark pl-3" style="line-height: 1.4;" id="modal-authors-list"></ul>
                        </div>
                        <div id="modal-per-person-alert" class="alert alert-success py-2 px-3 small border-0 mb-2 mt-2" style="background-color: #e8f5e9; border-radius: 6px; display: none;">
                            <i class="fas fa-check-circle mr-1 text-success"></i>
                            Price is <strong id="modal-per-person-price"></strong> per person. The total payment covers all authors securely.
                        </div>
                        <hr class="my-2 border-dashed">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Base Rate (<span id="modal-pricing-stage"></span> Price)</span>
                            <span class="text-dark"><span id="modal-currency"></span> <span id="modal-base-price"></span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="modal-discount-row" style="display: none;">
                            <span class="text-success"><i class="fas fa-tags mr-1"></i> Special/Domain Discount</span>
                            <span class="text-success">- <span id="modal-currency-2"></span> <span id="modal-discount-amount"></span></span>
                        </div>
                        <hr class="my-2 border-dark" style="border-top-width: 2px;">
                        <div class="d-flex justify-content-between mt-3">
                            <span class="font-weight-bold text-dark" style="font-size: 1.1rem;">Total Amount</span>
                            <span class="font-weight-bold text-primary" style="font-size: 1.25rem;"><span id="modal-currency-3"></span> <span id="modal-total-amount"></span></span>
                        </div>
                    </div>

                    <form action="{{ route('payNowPapers') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="modal-form-user-id">
                        <input type="hidden" name="paper_ids[]" id="modal-form-paper-id">
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm" style="border-radius: 8px;">
                            <i class="fas fa-lock mr-2"></i> Proceed to Secure Checkout
                        </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .student-status-toggle {
        display: inline-flex;
        border: 1px solid #ced4da;
        border-radius: 20px;
        overflow: hidden;
        background-color: #f8f9fa;
        padding: 2px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    .student-status-toggle input[type="radio"] {
        display: none;
    }
    .student-status-toggle .toggle-btn {
        padding: 4px 16px;
        margin: 0;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border-radius: 18px;
        transition: all 0.2s ease-in-out;
        color: #6c757d;
        text-align: center;
        user-select: none;
    }
    .student-status-toggle .toggle-btn:hover {
        color: #495057;
        background-color: #e9ecef;
    }
    .student-status-toggle input[type="radio"]:checked + .toggle-yes {
        background-color: #28a745;
        color: #fff;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
    }
    .student-status-toggle input[type="radio"]:checked + .toggle-no {
        background-color: #6c757d;
        color: #fff;
        box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
    }

    .font-weight-600 { font-weight: 600; }
    .list-title-truncate { max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
    .border-dashed { border-style: dashed !important; }
</style>

@section('scripts')
<script>
$(function () {
    let dtButtons = [
        {
            extend: 'excel',
            className: 'btn-default',
            text: '<i class="fas fa-file-excel mr-1 text-success"></i> Excel',
            titleAttr: 'Export to Excel',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15] }
        },
        {
            extend: 'csv',
            className: 'btn-default',
            text: '<i class="fas fa-file-csv mr-1 text-info"></i> CSV',
            titleAttr: 'Export to CSV',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15] }
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

    let table = $('#papers-table').DataTable({
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: {
            url: "{{ route('papers.index') }}",
            data: function (d) {
                d.status = $('#filter_status').val();
                d.track_id = $('#filter_track').val();
                d.payment_status = $('#filter_payment').val();
                d.department = $('#filter_department').val();
                d.institution = $('#filter_institution').val();
                d.country_id = $('#filter_country').val();
            }
        },
        columns: [
            { data: 'submission_id', name: 'submission_id' },
            { data: 'title', name: 'title' },
            { data: 'submitted_by', name: 'user.name' },
            { data: 'designation', name: 'designation', searchable: true, orderable: false },
            { data: 'authors', name: 'authors', searchable: false, orderable: false },
            { data: 'total_member', name: 'total_member', class: 'text-center', searchable: false, orderable: false },
            { data: 'department', name: 'department', searchable: true, orderable: false },
            { data: 'institution', name: 'institution', searchable: true, orderable: false },
            { data: 'country', name: 'country', searchable: true, orderable: false },
            { data: 'mode_of_participation', name: 'mode_of_participation', searchable: true, orderable: true },
            { data: 'pay_amount', name: 'pay_amount', class: 'text-right', searchable: true, orderable: false },
            { data: 'currency', name: 'currency', class: 'text-center', searchable: true, orderable: false },
            { data: 'track', name: 'track.name' },
            { data: 'status', name: 'status', class: 'text-center' },
            { data: 'created_at', name: 'created_at', class: 'text-center' },
            { data: 'abstract', name: 'abstract', visible: false, searchable: false, orderable: false },
            { data: 'actions', name: '{{ trans('global.actions') }}', orderable: false, searchable: false, class: 'text-right' }
        ],
        orderCellsTop: true,
        order: [[12, 'desc']],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 25,
        dom: 'lBfrtip',
        buttons: dtButtons
    });

    // Filter Change Listeners
    $('#filter_status, #filter_track, #filter_payment, #filter_country').on('change', function() {
        table.draw();
    });
    $('#filter_department, #filter_institution').on('keyup', function() {
        table.draw();
    });

    // Reset Filters
    $('#reset_filters').on('click', function() {
        $('#filter_status, #filter_track, #filter_payment, #filter_country').val('').trigger('change');
        $('#filter_department, #filter_institution').val('');
        table.draw();
    });

    // Dynamic Payment Modal Logic
    window.openPaymentModal = function(paperId) {
        let modal = $('#paymentReviewModal');
        let loading = $('#modal-loading');
        let content = $('#modal-actual-content');

        modal.modal('show');
        loading.show();
        content.hide();

        $.ajax({
            url: "{{ route('papers.pricing', ['paper' => ':paperId']) }}".replace(':paperId', paperId),
            method: 'GET',
            success: function(response) {
                // Populate Modal Data
                $('#modal-submission-id').text(response.submission_id);
                $('#modal-form-user-id').val(response.user_id);
                $('#modal-form-paper-id').val(response.paper_id);

                let pricing = response.pricing;
                $('#modal-pricing-stage').text(pricing.stage.charAt(0).toUpperCase() + pricing.stage.slice(1));
                $('#modal-currency, #modal-currency-2, #modal-currency-3').text(pricing.currency);
                $('#modal-base-price').text(pricing.base_price.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#modal-total-amount').text(pricing.final_price.toLocaleString(undefined, {minimumFractionDigits: 2}));

                // Authors List
                if (response.authors.length > 1) {
                    $('#modal-authors-section').show();
                    $('#modal-authors-count').text(response.authors.length);
                    let authorListHtml = '';
                    let fees = [];
                    response.authors.forEach(author => {
                        let designationText = author.designation ? ` (${author.designation})` : '';
                        let feeFormatted = Number(author.fee).toLocaleString(undefined, {minimumFractionDigits: 2});
                        authorListHtml += `<li>${author.name}<span class="text-muted small">${designationText}</span> - <strong class="text-primary">${pricing.currency} ${feeFormatted}</strong></li>`;
                        fees.push(author.fee);
                    });
                    $('#modal-authors-list').html(authorListHtml);

                    $('#modal-per-person-alert').show();
                    let uniqueFees = [...new Set(fees)];
                    if (uniqueFees.length > 1) {
                        $('#modal-per-person-alert').removeClass('alert-success').addClass('alert-info')
                            .html(`<i class="fas fa-info-circle mr-1"></i> Individual rates apply based on student/regular status.`);
                    } else {
                        let formattedPrice = Number(pricing.individual_final_price).toLocaleString(undefined, {minimumFractionDigits: 2});
                        $('#modal-per-person-alert').removeClass('alert-info').addClass('alert-success')
                            .html(`<i class="fas fa-check-circle mr-1 text-success"></i> Rate is <strong>${pricing.currency} ${formattedPrice}</strong> per person. The total payment covers all authors securely.`);
                    }
                } else {
                    $('#modal-authors-section').hide();
                    $('#modal-per-person-alert').hide();
                }

                // Discount logic
                if (pricing.discount > 0) {
                    $('#modal-discount-row').show();
                    $('#modal-discount-amount').text(pricing.discount.toLocaleString(undefined, {minimumFractionDigits: 2}));
                } else {
                    $('#modal-discount-row').hide();
                }

                loading.hide();
                content.fadeIn();
            },
            error: function() {
                alert('Failed to load payment details. Please try again.');
                modal.modal('hide');
            }
        });
    };
});
</script>
@endsection
@endsection

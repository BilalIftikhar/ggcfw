@extends('layouts.app')

@section('title', 'Academic Sessions')

@section('content')
    <style>
        /* Table styling */
        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th, .table td {
            vertical-align: middle !important;
            border: 1px solid #dee2e6;
        }

        /* Stripped rows */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Header styling */
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 2px solid #a5d6a7 !important;
            color: #2e7d32;
            font-weight: 600;
        }

        /* DataTable controls styling */
        /* Ensure search bar does not cut off */
        .dataTables_wrapper .dataTables_filter {
            width: 100%;
            max-width: 100%;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }

        /* Button styling */
        .btn {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* View Study Levels button - make it stand out */
        .btn-view-study-levels {
            background-color: #4a6fdc;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-view-study-levels:hover {
            background-color: #3a5bc7;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Active session badge */
        .badge-active {
            background-color: #1e88e5;
            color: white;
        }

        /* Inactive session badge */
        .badge-inactive {
            background-color: #ffc107;
            color: #212529;
        }

        /* Admission open badge */
        .badge-admission-open {
            background-color: #4caf50;
            color: white;
        }

        /* Admission closed badge */
        .badge-admission-closed {
            background-color: #9e9e9e;
            color: white;
        }

        /* Modal header */
        .modal-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-bottom: none;
        }

        /* Form control focus */
        .form-control:focus {
            border-color: #4caf50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        /* Switch styling */
        .form-check-input:checked {
            background-color: #2e7d32;
            border-color: #2e7d32;
        }

        /* Action buttons container */
        .action-buttons {
            min-width: 180px;
        }

        /* Hover effect on table rows */
        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .action-buttons {
                min-width: auto;
            }
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Academic Sessions</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Academic Sessions</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_academic_session')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end" style="padding: 1.5rem">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Academic Session
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">List of Academic Sessions</h5>

                            <div class="table-responsive">
                                <table id="sessionsTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Admission End</th>
                                        <th>Admission</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($sessions as $index => $session)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $session->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($session->start_date)->format('d-M-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($session->end_date)->format('d-M-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($session->admission_end_date)->format('d-M-Y') }}</td>
                                            <td>
                                                @if($session->allow_admission)
                                                    <span class="badge badge-admission-open rounded-pill">Open</span>
                                                @else
                                                    <span class="badge badge-admission-closed rounded-pill">Closed</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($session->is_active)
                                                    <span class="badge badge-active rounded-pill">Active</span>
                                                @else
                                                    <span class="badge badge-inactive rounded-pill">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('update_academic_session')
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 editBtn"
                                                                data-id="{{ $session->id }}"
                                                                data-name="{{ $session->name }}"
                                                                data-start="{{ $session->start_date }}"
                                                                data-end="{{ $session->end_date }}"
                                                                data-admission="{{ $session->admission_end_date }}"
                                                                data-allow="{{ $session->allow_admission }}"
                                                                data-active="{{ $session->is_active }}"
                                                                data-bs-toggle="tooltip" title="Edit Session">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                    @can('view_study_level')
                                                        <a href="{{ route('study-levels.index', ['session_id' => $session->id]) }}"
                                                           class="btn btn-sm btn-view-study-levels me-2"
                                                           data-bs-toggle="tooltip" title="View/Manage Study Levels">
                                                            <i class="bi bi-layers"></i> Levels
                                                        </a>
                                                    @endcan

                                                    @can('delete_academic_session')
                                                        <form action="{{ route('academic-session.destroy', $session->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip" title="Delete Session">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Create/Edit Modal -->
        <div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="sessionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="sessionModalLabel">Add Academic Session</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="sessionForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Session Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="admission_end_date" class="form-label">Admission End Date</label>
                                <input type="date" class="form-control" name="admission_end_date" id="admission_end_date" required>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="allow_admission" id="allow_admission">
                                <label class="form-check-label" for="allow_admission">Allow Admission</label>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">Active Session</label>
                            </div>

                            <div class="modal-footer border-top-0">
                                <button type="button" class="btn btn-outline-secondary" id="cancelModalBtn">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTable with better configuration
            $('#sessionsTable').DataTable({
                dom:
                    '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-md-end justify-content-start"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-md-end justify-content-start"p>>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-files me-1"></i> Copy',
                        titleAttr: 'Copy to clipboard',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                        titleAttr: 'Export to CSV',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                        titleAttr: 'Print table',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Search sessions...",
                    lengthMenu: "Show _MENU_ sessions",
                    info: "Showing _START_ to _END_ of _TOTAL_ sessions",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                },
                columnDefs: [
                    {
                        orderable: false,
                        targets: -1,
                        className: 'text-center'
                    }
                ],
                initComplete: function () {
                    $('.dataTables_filter input')
                        .addClass('form-control form-control-sm')
                        .css({
                            'max-width': '300px',
                            'width': '100%'
                        });
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });



            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Modal: Open for new session
            $('#openModalBtn').click(function () {
                $('#sessionForm').attr('action', '{{ route('academic-session.store') }}');
                $('#_method').val('POST');
                $('#sessionForm')[0].reset();
                $('#sessionModalLabel').text('Add Academic Session');
                $('#sessionModal').modal('show');
            });

            // Modal: Open for editing
            $('.editBtn').click(function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const start = $(this).data('start');
                const end = $(this).data('end');
                const admission = $(this).data('admission');
                const allow = $(this).data('allow');
                const active = $(this).data('active');

                $('#name').val(name);
                $('#start_date').val(start);
                $('#end_date').val(end);
                $('#admission_end_date').val(admission);
                $('#allow_admission').prop('checked', allow);
                $('#is_active').prop('checked', active);
                $('#_method').val('PUT');
                $('#sessionForm').attr('action', `/academic-sessions/${id}`);
                $('#sessionModalLabel').text('Edit Academic Session');
                $('#sessionModal').modal('show');
            });

            // Date validation
            $('#sessionForm').submit(function (e) {
                const startDate = new Date($('#start_date').val());
                const endDate = new Date($('#end_date').val());
                const admissionEndDate = new Date($('#admission_end_date').val());

                if (startDate >= endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Dates',
                        text: 'End date must be after start date',
                        confirmButtonColor: '#2e7d32'
                    });
                    e.preventDefault();
                    return false;
                }

                if (admissionEndDate > endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Admission Date',
                        text: 'Admission end date cannot be after session end date',
                        confirmButtonColor: '#2e7d32'
                    });
                    e.preventDefault();
                    return false;
                }

                return true;
            });

            // SweetAlert delete confirmation
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this academic session? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete Session',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

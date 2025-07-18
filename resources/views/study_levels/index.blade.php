@extends('layouts.app')

@section('title', 'Study Levels')

@section('content')
    <style>
        /* Table styling */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #dee2e6;
        }

        .table th, .table td {
            vertical-align: middle !important;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
        }

        /* Striped table */
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
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 5px 10px;
        }

        /* Button styling */
        .btn {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* View Programs button - make it stand out */
        .btn-view-programs {
            background-color: #5c6bc0;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-view-programs:hover {
            background-color: #3949ab;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
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

        /* Action buttons container */
        .action-buttons {
            min-width: 200px;
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
            <h1>Study Levels</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Study Levels</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_study_level')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end" style="padding: 1.5rem">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Study Level
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
                            <h5 class="card-title">
                                List of Study Levels
                                @isset($academicSession)
                                    <span class="text-muted">- {{ $academicSession->name }}</span>
                                @endisset
                            </h5>

                            <div class="table-responsive">
                                <table id="studyLevelsTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Academic Session</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($studyLevels as $index => $level)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $level->name }}</td>
                                            <td>
                                                {{ $level->academicSession->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('view_program')
                                                        <a href="{{ route('programs.index', ['level_id' => $level->id]) }}"
                                                           class="btn btn-sm btn-view-programs me-2"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="View/Manage Programs">
                                                            <i class="bi bi-layers"></i> Programs
                                                        </a>
                                                    @endcan

                                                    @can('update_study_level')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-primary me-2 editBtn"
                                                                data-id="{{ $level->id }}"
                                                                data-name="{{ $level->name }}"
                                                                data-session="{{ $level->academic_session_id }}"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                title="Edit Study Level">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                    @can('delete_study_level')
                                                        <form action="{{ route('study-levels.destroy', $level->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="Delete Study Level">
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
        <div class="modal fade" id="studyLevelModal" tabindex="-1" aria-labelledby="studyLevelModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="studyLevelModalLabel">Add Study Level</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="studyLevelForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Level Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="academic_session_id" class="form-label">Academic Session</label>
                                <select name="academic_session_id" id="academic_session_id" class="form-select" required>
                                    @isset($academicSession)
                                        <option value="{{ $academicSession->id }}" selected>{{ $academicSession->name }}</option>
                                    @else
                                        @foreach(\App\Models\AcademicSession::orderBy('start_date', 'desc')->get() as $session)
                                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
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
            $('#studyLevelsTable').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
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
                    searchPlaceholder: "Search levels...",
                    lengthMenu: "Show _MENU_ levels",
                    info: "Showing _START_ to _END_ of _TOTAL_ levels",
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
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Modal: Open for new level
            $('#openModalBtn').click(function () {
                $('#studyLevelForm').attr('action', '{{ route('study-levels.store') }}');
                $('#_method').val('POST');
                $('#studyLevelForm')[0].reset();
                $('#studyLevelModalLabel').text('Add Study Level');
                $('#studyLevelModal').modal('show');
            });

            // Modal: Open for editing
            $('.editBtn').click(function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const session = $(this).data('session');

                $('#name').val(name);
                $('#academic_session_id').val(session);
                $('#_method').val('PUT');
                $('#studyLevelForm').attr('action', `/study-levels/${id}`);
                $('#studyLevelModalLabel').text('Edit Study Level');
                $('#studyLevelModal').modal('show');
            });

            // SweetAlert delete confirmation
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this study level? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete Level',
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

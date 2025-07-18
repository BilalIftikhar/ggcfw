@extends('layouts.app')

@section('title', 'Classes')

@section('content')
    <style>
        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th, .table td {
            vertical-align: middle !important;
            border: 1px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 2px solid #a5d6a7 !important;
            color: #2e7d32;
            font-weight: 600;
        }

        .btn-show-courses {
            background-color: #1e88e5;
            color: #fff;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-show-courses:hover {
            background-color: #1565c0;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        .btn-toggle-status-active {
            background-color: #d32f2f;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-toggle-status-active:hover {
            background-color: #b71c1c;
            transform: translateY(-1px);
        }

        .btn-toggle-status-inactive {
            background-color: #2e7d32;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-toggle-status-inactive:hover {
            background-color: #1b5e20;
            transform: translateY(-1px);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.3);
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 250px !important;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Classes</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Classes</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">List of Classes</h5>

                            <div class="table-responsive">
                                <table id="classesTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Class Name</th>
                                        <th>Program</th>
                                        <th>Study Level</th>
                                        <th>Academic Session</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($classes as $index => $class)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $class->name }}</td>
                                            <td>{{ $class->program->name ?? 'N/A' }}</td>
                                            <td>{{ $class->program->studyLevel->name ?? 'N/A' }}</td>
                                            <td>{{ $class->program->academicSession->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $class->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @can('view_course')
                                                        <a href="{{ route('courses.index', ['class_id' => $class->id]) }}"
                                                           class="btn btn-sm btn-show-courses"
                                                           title="Show Courses">
                                                            <i class="bi bi-journal-text me-1"></i> Show Courses
                                                        </a>
                                                    @endcan

                                                    @can('update_program')
                                                        <button type="button"
                                                                class="btn btn-sm {{ $class->is_active ? 'btn-toggle-status-active' : 'btn-toggle-status-inactive' }} toggle-status-btn"
                                                                data-id="{{ $class->id }}"
                                                                data-status="{{ $class->is_active ? 'active' : 'inactive' }}"
                                                                title="{{ $class->is_active ? 'Deactivate' : 'Activate' }} Class">
                                                            <i class="bi {{ $class->is_active ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                                            {{ $class->is_active ? 'Deactivate' : 'Activate' }}
                                                        </button>
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
    </main>
@endsection

@section('scripts')
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <script>
        $(document).ready(function () {
            $('#classesTable').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6 d-flex justify-content-end"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-files me-1"></i> Copy',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                        exportOptions: { columns: ':not(:last-child)' }
                    }
                ],
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Search classes...",
                    lengthMenu: "Show _MENU_ classes",
                    info: "Showing _START_ to _END_ of _TOTAL_ classes",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm').css('width', '250px');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Toggle Status AJAX
            $(document).on('click', '.toggle-status-btn', function () {
                let button = $(this);
                let classId = button.data('id');

                $.ajax({
                    url: `/classes/${classId}/toggle-status`,
                    type: 'PATCH',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);

                            let badge = button.closest('tr').find('td:nth-child(6) .badge');
                            if (response.is_active) {
                                badge.removeClass('bg-warning').addClass('bg-success').text('Active');
                                button
                                    .removeClass('btn-toggle-status-inactive')
                                    .addClass('btn-toggle-status-active')
                                    .html('<i class="bi bi-x-circle"></i> Deactivate')
                                    .attr('title', 'Deactivate Class');
                            } else {
                                badge.removeClass('bg-success').addClass('bg-warning').text('Inactive');
                                button
                                    .removeClass('btn-toggle-status-active')
                                    .addClass('btn-toggle-status-inactive')
                                    .html('<i class="bi bi-check-circle"></i> Activate')
                                    .attr('title', 'Activate Class');
                            }
                        } else {
                            toastr.error('Unexpected error occurred.');
                        }
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message ?? 'Failed to update status.');
                    }
                });
            });
        });
    </script>
@endsection

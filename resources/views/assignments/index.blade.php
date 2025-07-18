@extends('layouts.app')

@section('title', 'Assignments')

@section('content')
    <style>
        /* Table cell alignment */
        .table th, .table td {
            vertical-align: middle !important;
        }

        /* Light green header styling */
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 1px solid #c8e6c9;
        }
        .dataTables_scrollHead {
            border-top: 1px solid #c8e6c9 !important;
            background-color: #e8f5e9;
        }

        /* Badge styling */
        .badge {
            font-size: 0.85em;
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-secondary {
            background-color: #6c757d;
        }

        /* Button styling */
        .btn i {
            margin-right: 5px;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .btn-group-sm > .btn, .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        /* Table spacing and borders */
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
        .table-responsive {
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        /* Status column width */
        .status-col {
            width: 100px;
        }
        .actions-col {
            width: 150px;
        }

        /* Title link styling */
        .assignment-title {
            color: #2c3e50;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }
        .assignment-title:hover {
            color: #3490dc;
            text-decoration: underline;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Assignments</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Assignments</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section dashboard">
            @can('create_assignment')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <a href="{{ route('assignments.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Create Assignment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">List of Assignments</h5>

                            <div class="table-responsive">
                                <table id="assignmentTable" class="table table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Program</th>
                                        <th>Course</th>
                                        <th>Section</th>
                                        <th>Teacher</th>
                                        <th>Due Date</th>
                                        <th class="status-col">Status</th>
                                        <th class="actions-col">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($assignments as $index => $assignment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('assignments.show', $assignment->id) }}" class="assignment-title">
                                                    {{ $assignment->title }}
                                                </a>
                                            </td>
                                            <td>{{ $assignment->program->name ?? 'N/A' }}</td>
                                            <td>{{ $assignment->course->name ?? 'N/A' }}</td>
                                            <td>{{ $assignment->section->name ?? 'N/A' }}</td>
                                            <td>{{ $assignment->teacher->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, h:i A') }}</td>
                                            <td>
                                                @php
                                                    $dueDate = \Carbon\Carbon::parse($assignment->due_date);
                                                    $now = \Carbon\Carbon::now();
                                                    $status = $dueDate < $now ? 'Expired' : 'Active';
                                                    $badgeClass = $dueDate < $now ? 'badge-danger' : 'badge-success';
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm " role="group">
                                                    <a href="{{ route('assignments.show', $assignment->id) }}"
                                                       class="btn btn-outline-info me-2"
                                                       data-bs-toggle="tooltip" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @can('update_assignment')
                                                        <a href="{{ route('assignments.edit', $assignment->id) }}"
                                                           class="btn btn-outline-primary me-2"
                                                           data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endcan

                                                    @can('view_submission')
                                                        <a href="{{ route('assignments.submissions', $assignment->id) }}"
                                                           class="btn btn-outline-secondary me-2"
                                                           data-bs-toggle="tooltip" title="View Submissions">
                                                            <i class="bi bi-list-check"></i>
                                                        </a>
                                                    @endcan

                                                @can('delete_assignment')
                                                        <form action="{{ route('assignments.destroy', $assignment->id) }}" method="POST" class="deleteForm d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger delete-btn me-2"
                                                                    data-bs-toggle="tooltip" title="Delete Assignment">
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
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTable with enhanced options
            $('#assignmentTable').DataTable({
                responsive: true,
                dom: '<"top"lf>rt<"bottom"ip>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search assignments...",
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 1 }, // Title
                    { responsivePriority: 2, targets: -1 }, // Actions
                    { responsivePriority: 3, targets: -2 }, // Status
                    { orderable: false, targets: -1 } // Disable sorting for actions column
                ],
                initComplete: function() {
                    // Add custom filter for status (now only Active/Expired)
                    this.api().columns([7]).every(function() {
                        var column = this;
                        var select = $('<select class="form-select form-select-sm"><option value="">All Status</option><option value="Active">Active</option><option value="Expired">Expired</option></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });
                    });
                }
            });

            // Delete confirmation
            $('.deleteForm').submit(function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection

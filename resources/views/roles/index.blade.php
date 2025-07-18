@extends('layouts.app')

@section('title', 'Role Management')

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

        /* Header styling */
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 2px solid #a5d6a7 !important;
            color: #2e7d32;
            font-weight: 600;
        }

        /* DataTable controls styling */
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

        /* System role badge */
        .badge-system {
            background-color: #6c757d;
            color: white;
            font-size: 0.75rem;
        }

        /* Action buttons container */
        .action-buttons {
            min-width: 150px;
        }

        /* Hover effect on table rows */
        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.3);
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .action-buttons {
                min-width: auto;
            }
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Role Management</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            @can('create_role')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end" style="padding: 1.5rem">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Role
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
                            <h5 class="card-title">List of Roles</h5>

                            <div class="table-responsive">
                                <table id="rolesTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Role Name</th>
                                        <th>Type</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($roles as $index => $role)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold text-capitalize">
                                                {{ $role->name }}
                                            </td>
                                            <td>
                                                @if($role->is_system)
                                                    <span class="badge badge-system rounded-pill">System</span>
                                                @else
                                                    <span class="badge bg-primary rounded-pill">Custom</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('edit_role')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-primary me-2 editBtn"
                                                                data-id="{{ $role->id }}"
                                                                data-name="{{ $role->name }}"
                                                                data-is-system="{{ $role->is_system }}"
                                                                data-bs-toggle="tooltip"
                                                                title="Edit Role">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                    @can('delete_role')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger me-2 deleteBtn"
                                                                data-id="{{ $role->id }}"
                                                                data-is-system="{{ $role->is_system }}"
                                                                data-bs-toggle="tooltip"
                                                                title="Delete Role">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endcan

                                                    @can('assign_permission')
                                                        <a href="{{ route('roles.permissions', $role->id) }}"
                                                           class="btn btn-sm btn-outline-success"
                                                           data-bs-toggle="tooltip"
                                                           title="Manage Permissions">
                                                            <i class="bi bi-shield-lock"></i>
                                                        </a>
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

        <!-- Add/Edit Role Modal -->
        <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="roleModalLabel">Add Role</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="roleForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">

                            <div class="form-group mb-4">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>

                            <div class="modal-footer border-top-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
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
            $('#rolesTable').DataTable({
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
                    searchPlaceholder: "Search roles...",
                    lengthMenu: "Show _MENU_ roles",
                    info: "Showing _START_ to _END_ of _TOTAL_ roles",
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
                    },
                    {
                        width: '5%',
                        targets: 0
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

            // Modal: Open for new role
            $('#openModalBtn').click(function () {
                $('#roleForm').attr('action', '{{ route('roles.store') }}');
                $('#_method').val('POST');
                $('#roleForm')[0].reset();
                $('#roleModalLabel').text('Add Role');
                $('#roleModal').modal('show');
            });

            // Modal: Open for editing
            $(document).on('click', '.editBtn', function() {
                const isSystem = $(this).data('is-system');
                if (isSystem) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'System Role',
                        text: 'System roles cannot be modified',
                        confirmButtonColor: '#2e7d32'
                    });
                    return;
                }

                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#name').val(name);
                $('#_method').val('PUT');
                $('#roleForm').attr('action', `/roles/${id}`);
                $('#roleModalLabel').text('Edit Role');
                $('#roleModal').modal('show');
            });

            // Delete button handler
            $(document).on('click', '.deleteBtn', function() {
                const isSystem = $(this).data('is-system');
                if (isSystem) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'System Role',
                        text: 'System roles cannot be deleted',
                        confirmButtonColor: '#2e7d32'
                    });
                    return;
                }

                const roleId = $(this).data('id');

                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this role? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete Role',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/roles/${roleId}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        confirmButtonColor: '#2e7d32'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonColor: '#2e7d32'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'An error occurred',
                                    confirmButtonColor: '#2e7d32'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

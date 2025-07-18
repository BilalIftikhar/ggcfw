@extends('layouts.app')

@section('title', 'Fee Types')

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

        .dataTables_wrapper .dataTables_filter {
            width: 100%;
            max-width: 100%;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }

        .btn {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .badge-group {
            background-color: #4a6fdc;
            color: white;
        }

        .modal-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-bottom: none;
        }

        .form-control:focus {
            border-color: #4caf50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .form-check-input:checked {
            background-color: #2e7d32;
            border-color: #2e7d32;
        }

        .action-buttons {
            min-width: 150px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.3);
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Fee Types</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Fee Types</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_fee_type')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end" style="padding: 1.5rem">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Fee Type
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
                            <h5 class="card-title">List of Fee Types</h5>

                            <div class="table-responsive">
                                <table id="feeTypesTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Fee Group</th>
                                        <th>Account Code</th>
                                        <th>Bank Name</th>
                                        <th>Description</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($feeTypes as $index => $feeType)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $feeType->name }}</td>
                                            <td>
                                                <span class="badge badge-group rounded-pill">{{ $feeType->feeGroup->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $feeType->account_code ?? '-' }}</td>
                                            <td>{{ $feeType->bank_name ?? '-' }}</td>
                                            <td>{{ $feeType->description ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('update_fee_type')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-primary me-2 editBtn"
                                                                data-id="{{ $feeType->id }}"
                                                                data-name="{{ $feeType->name }}"
                                                                data-group="{{ $feeType->fee_group_id }}"
                                                                data-account="{{ $feeType->account_code }}"
                                                                data-bank="{{ $feeType->bank_name }}"
                                                                data-description="{{ $feeType->description }}"
                                                                data-bs-toggle="tooltip"
                                                                title="Edit Fee Type">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                    @can('delete_fee_type')
                                                        <form action="{{ route('fee-type.destroy', $feeType->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip" title="Delete Fee Type">
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
        <div class="modal fade" id="feeTypeModal" tabindex="-1" aria-labelledby="feeTypeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="feeTypeModalLabel">Add Fee Type</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="feeTypeForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">

                            <div class="mb-3">
                                <label for="name" class="form-label">Fee Type Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="fee_group_id" class="form-label">Fee Group</label>
                                <select class="form-select" name="fee_group_id" id="fee_group_id" required>
                                    <option value="">Select Fee Group</option>
                                    @foreach ($feeGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="account_code" class="form-label">Account Code</label>
                                <input type="text" class="form-control" name="account_code" id="account_code">
                            </div>

                            <div class="mb-3">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" name="bank_name" id="bank_name">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="2"></textarea>
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
            $('#feeTypesTable').DataTable({
                dom:
                    '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-md-end justify-content-start"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-md-end justify-content-start"p>>',
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
                    searchPlaceholder: "Search fee types...",
                    lengthMenu: "Show _MENU_ fee types",
                    info: "Showing _START_ to _END_ of _TOTAL_ fee types",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: -1, className: 'text-center' }
                ],
                initComplete: function () {
                    $('.dataTables_filter input')
                        .addClass('form-control form-control-sm')
                        .css({ 'max-width': '300px', 'width': '100%' });
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#openModalBtn').click(function () {
                $('#feeTypeForm').attr('action', '{{ route('fee-type.store') }}');
                $('#_method').val('POST');
                $('#feeTypeForm')[0].reset();
                $('#feeTypeModalLabel').text('Add Fee Type');
                $('#feeTypeModal').modal('show');
            });

            $('.editBtn').click(function () {
                const id = $(this).data('id');
                $('#name').val($(this).data('name'));
                $('#fee_group_id').val($(this).data('group'));
                $('#account_code').val($(this).data('account'));
                $('#bank_name').val($(this).data('bank'));
                $('#description').val($(this).data('description'));
                $('#_method').val('PUT');
                $('#feeTypeForm').attr('action', `/fee-types/${id}`);
                $('#feeTypeModalLabel').text('Edit Fee Type');
                $('#feeTypeModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this fee type? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete Fee Type',
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

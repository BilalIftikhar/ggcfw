@extends('layouts.app')

@section('title', 'Visitor Logs')

@section('content')
    <style>
        .table { border-collapse: collapse; width: 100%; }
        .table th, .table td { vertical-align: middle !important; border: 1px solid #dee2e6; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0, 0, 0, 0.02); }
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 2px solid #a5d6a7 !important;
            color: #2e7d32;
            font-weight: 600;
        }
        .btn { font-weight: 500; letter-spacing: 0.5px; }
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
        .action-buttons { min-width: 130px; }
        .table-hover tbody tr:hover { background-color: rgba(232, 245, 233, 0.3); }
        @media (max-width: 768px) { .action-buttons { min-width: auto; } }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Visitor Logs</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Visitor Logs</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_visitor_log')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end p-3">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Visitor Log
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
                            <h5 class="card-title">List of Visitor Logs</h5>
                            <div class="table-responsive">
                                <table id="visitorLogsTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Purpose</th>
                                        <th>Visit Date</th>
                                        <th>Note</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($visitorLogs as $index => $log)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $log->name }}</td>
                                            <td>{{ $log->contact_number ?? '-' }}</td>
                                            <td>{{ $log->address ?? '-' }}</td>
                                            <td>{{ $log->purpose ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($log->date_of_visit)->format('d-M-Y') }}</td>
                                            <td>{{ $log->note ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('update_visitor_log')
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 editBtn"
                                                                data-id="{{ $log->id }}"
                                                                data-name="{{ $log->name }}"
                                                                data-contact_number="{{ $log->contact_number }}"
                                                                data-address="{{ $log->address }}"
                                                                data-purpose="{{ $log->purpose }}"
                                                                data-note="{{ $log->note }}"
                                                                data-dateofvisit="{{ \Carbon\Carbon::parse($log->date_of_visit)->format('Y-m-d') }}"
                                                                data-bs-toggle="tooltip" title="Edit Visitor Log">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                    @can('delete_visitor_log')
                                                        <form action="{{ route('visitor-logs.destroy', $log->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip" title="Delete Visitor Log">
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
        <div class="modal fade" id="visitorModal" tabindex="-1" aria-labelledby="visitorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="visitorModalLabel">Add Visitor Log</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="visitorForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contact</label>
                                <input type="text" class="form-control" name="contact_number" id="contact_number">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" id="address">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Purpose of Visit</label>
                                <input type="text" class="form-control" name="purpose" id="purpose">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Visit Date</label>
                                <input type="date" class="form-control" name="date_of_visit" id="date_of_visit" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea class="form-control" name="note" id="note" rows="2"></textarea>
                            </div>

                            <div class="modal-footer border-top-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save</button>
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
            $('#visitorLogsTable').DataTable({
                dom:
                    '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-md-end justify-content-start"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-md-end justify-content-start"p>>',
                buttons: [
                    { extend: 'copy', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-files me-1"></i> Copy', exportOptions: { columns: ':not(:last-child)' }},
                    { extend: 'csv', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV', exportOptions: { columns: ':not(:last-child)' }},
                    { extend: 'print', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-printer me-1"></i> Print', exportOptions: { columns: ':not(:last-child)' }},
                ],
                responsive: true,
                language: { search: "", searchPlaceholder: "Search visitors...", lengthMenu: "Show _MENU_ visitors" },
                columnDefs: [{ orderable: false, targets: -1, className: 'text-center' }]
            });

            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#openModalBtn').click(function () {
                $('#visitorForm').attr('action', '{{ route('visitor-logs.store') }}');
                $('#_method').val('POST');
                $('#visitorForm')[0].reset();
                $('#visitorModalLabel').text('Add Visitor Log');
                $('#visitorModal').modal('show');
            });

            $('.editBtn').click(function () {
                const btn = $(this);

                $('#name').val(btn.data('name'));
                $('#contact_number').val(btn.data('contact_number'));
                $('#address').val(btn.data('address'));
                $('#purpose').val(btn.data('purpose'));
                $('#note').val(btn.data('note'));
                $('#date_of_visit').val(btn.data('dateofvisit'));
                $('#_method').val('PUT');
                $('#visitorForm').attr('action', `/visitor-logs/${btn.data('id')}`);
                $('#visitorModalLabel').text('Edit Visitor Log');
                $('#visitorModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this visitor log?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) { form.submit(); }
                });
            });
        });
    </script>
@endsection

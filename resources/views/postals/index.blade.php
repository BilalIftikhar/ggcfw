@extends('layouts.app')

@section('title', 'Postal Records')

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
        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }
        .btn {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .badge-dispatch {
            background-color: #1e88e5;
            color: white;
        }
        .badge-receive {
            background-color: #ffc107;
            color: #212529;
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
            min-width: 140px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.3);
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Postal Records</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Postal Records</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_post_log')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end" style="padding: 1.5rem">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Postal Record
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
                            <h5 class="card-title">List of Postal Records</h5>
                            <div class="table-responsive">
                                <table id="postalsTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Reference</th>
                                        <th>To</th>
                                        <th>From</th>
                                        <th>Courier</th>
                                        <th>Date</th>
                                        <th>Note</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($postals as $index => $postal)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($postal->type == 'dispatch')
                                                    <span class="badge badge-dispatch rounded-pill">Dispatch</span>
                                                @else
                                                    <span class="badge badge-receive rounded-pill">Receive</span>
                                                @endif
                                            </td>
                                            <td>{{ $postal->reference_number }}</td>
                                            <td>{{ $postal->to_title }}</td>
                                            <td>{{ $postal->from_title }}</td>
                                            <td>{{ $postal->courier }}</td>
                                            <td>{{ \Carbon\Carbon::parse($postal->date)->format('d-M-Y') }}</td>
                                            <td>{{ Str::limit($postal->note, 30) }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('update_post_log')
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 editBtn"
                                                                data-id="{{ $postal->id }}"
                                                                data-type="{{ $postal->type }}"
                                                                data-reference="{{ $postal->reference_number }}"
                                                                data-to="{{ $postal->to_title }}"
                                                                data-from="{{ $postal->from_title }}"
                                                                data-address="{{ $postal->address }}"
                                                                data-tracking="{{ $postal->tracking_no }}"
                                                                data-courier="{{ $postal->courier }}"
                                                                data-note="{{ $postal->note }}"
                                                                data-date="{{ $postal->date }}"
                                                                data-bs-toggle="tooltip" title="Edit Postal">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                    @can('delete_post_log')
                                                        <form action="{{ route('postals.destroy', $postal->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip" title="Delete Postal">
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

        <!-- Modal -->
        <div class="modal fade" id="postalModal" tabindex="-1" aria-labelledby="postalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-lg for better two-column fit -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="postalModalLabel">Add Postal Record</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="postalForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" name="type" id="type" required>
                                        <option value="">Select Type</option>
                                        <option value="dispatch">Dispatch</option>
                                        <option value="receive">Receive</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="reference_number" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="reference_number" id="reference_number" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="to_title" class="form-label">To</label>
                                    <input type="text" class="form-control" name="to_title" id="to_title">
                                </div>
                                <div class="col-md-6">
                                    <label for="from_title" class="form-label">From</label>
                                    <input type="text" class="form-control" name="from_title" id="from_title">
                                </div>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" id="address">
                                </div>
                                <div class="col-md-6">
                                    <label for="tracking_no" class="form-label">Tracking Number</label>
                                    <input type="text" class="form-control" name="tracking_no" id="tracking_no">
                                </div>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="courier" class="form-label">Courier</label>
                                    <input type="text" class="form-control" name="courier" id="courier">
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" name="date" id="date" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-12">
                                    <label for="note" class="form-label">Note</label>
                                    <textarea class="form-control" name="note" id="note" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer border-top-0 mt-2">
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
            $('#postalsTable').DataTable({
                dom: '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-md-end justify-content-start"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-md-end justify-content-start"p>>',
                buttons: [
                    { extend: 'copy', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-files me-1"></i> Copy', exportOptions: { columns: ':not(:last-child)' }},
                    { extend: 'csv', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV', exportOptions: { columns: ':not(:last-child)' }},
                    { extend: 'print', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-printer me-1"></i> Print', exportOptions: { columns: ':not(:last-child)' }}
                ],
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Search postal records...",
                    lengthMenu: "Show _MENU_ records",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: -1, className: 'text-center' }
                ]
            });

            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#openModalBtn').click(function () {
                $('#postalForm').attr('action', '{{ route('postals.store') }}');
                $('#_method').val('POST');
                $('#postalForm')[0].reset();
                $('#postalModalLabel').text('Add Postal Record');
                $('#postalModal').modal('show');
            });

            $('.editBtn').click(function () {
                const btn = $(this);
                $('#type').val(btn.data('type'));
                $('#reference_number').val(btn.data('reference'));
                $('#to_title').val(btn.data('to'));
                $('#from_title').val(btn.data('from'));
                $('#address').val(btn.data('address'));
                $('#tracking_no').val(btn.data('tracking'));
                $('#courier').val(btn.data('courier'));
                $('#note').val(btn.data('note'));
                $('#date').val(btn.data('date'));

                $('#_method').val('PUT');
                $('#postalForm').attr('action', `/postals/${btn.data('id')}`);
                $('#postalModalLabel').text('Edit Postal Record');
                $('#postalModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this postal record? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete',
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

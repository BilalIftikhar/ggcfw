@extends('layouts.app')

@section('title', 'Examination Sessions')

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
        .badge-active { background-color: #1e88e5; color: white; }
        .badge-inactive { background-color: #ffc107; color: #212529; }
        .badge-running { background-color: #4caf50; color: white; }
        .badge-not-running { background-color: #9e9e9e; color: white; }
        .badge-taken { background-color: #673ab7; color: white; }
        .badge-not-taken { background-color: #e91e63; color: white; }
        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.3);
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Examination Sessions</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Examination Sessions</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_examination_session')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end p-3">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Examination Session
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
                            <h5 class="card-title">List of Examination Sessions</h5>
                            <div class="table-responsive">
                                <table id="sessionsTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Running</th>
                                        <th>Taken</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sessions as $index => $session)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $session->title }}</td>
                                            <td>{{ \Carbon\Carbon::parse($session->start_date)->format('d-M-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($session->end_date)->format('d-M-Y') }}</td>
                                            <td>
                                                @if($session->is_active)
                                                    <span class="badge badge-active rounded-pill">Active</span>
                                                @else
                                                    <span class="badge badge-inactive rounded-pill">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('update_examination_session')
                                                    <button type="button" class="btn btn-sm toggle-running-btn p-0 border-0 bg-transparent"
                                                            data-id="{{ $session->id }}"
                                                            data-running="{{ $session->is_running ? 1 : 0 }}"
                                                            data-bs-toggle="tooltip" title="Toggle Running Status">
                                                        @if($session->is_running)
                                                            <span class="badge badge-running rounded-pill">Running</span>
                                                        @else
                                                            <span class="badge badge-not-running rounded-pill">Not Running</span>
                                                        @endif
                                                    </button>
                                                @else
                                                    @if($session->is_running)
                                                        <span class="badge badge-running rounded-pill">Running</span>
                                                    @else
                                                        <span class="badge badge-not-running rounded-pill">Not Running</span>
                                                    @endif
                                                @endcan
                                            </td>
                                            <td>
                                                @if($session->is_examination_taken)
                                                    <span class="badge badge-taken rounded-pill">Taken</span>
                                                @else
                                                    <span class="badge badge-not-taken rounded-pill">Not Taken</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @can('update_examination_session')
                                                        <button type="button" class="btn btn-sm btn-outline-primary editBtn"
                                                                data-id="{{ $session->id }}"
                                                                data-title="{{ $session->title }}"
                                                                data-start="{{ $session->start_date }}"
                                                                data-end="{{ $session->end_date }}"
                                                                data-description="{{ $session->description }}"
                                                                data-active="{{ $session->is_active }}"
                                                                data-running="{{ $session->is_running }}"
                                                                data-taken="{{ $session->is_examination_taken }}"
                                                                data-bs-toggle="tooltip" title="Edit Session">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete_examination_session')
                                                        <form action="{{ route('examination-session.destroy', $session->id) }}" method="POST" class="d-inline">
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

            <!-- Modal -->
            <div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="sessionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="sessionModalLabel">Add Examination Session</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="sessionForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Session Title</label>
                                    <input type="text" class="form-control" name="title" id="title" required>
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
                                    <label for="description" class="form-label">Description (Optional)</label>
                                    <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active">
                                    <label class="form-check-label" for="is_active">Active Session</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_running" id="is_running">
                                    <label class="form-check-label" for="is_running">Running</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_examination_taken" id="is_examination_taken">
                                    <label class="form-check-label" for="is_examination_taken">Examination Taken</label>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#sessionsTable').DataTable({
                dom:
                    '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-md-end justify-content-start"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-md-end justify-content-start"p>>',
                buttons: [
                    { extend: 'copy', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-files me-1"></i> Copy', exportOptions: { columns: ':not(:last-child)' }},
                    { extend: 'csv', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV', exportOptions: { columns: ':not(:last-child)' }},
                    { extend: 'print', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-printer me-1"></i> Print', exportOptions: { columns: ':not(:last-child)' }}
                ],
                responsive: true
            });

            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#openModalBtn').click(function () {
                $('#sessionForm').attr('action', '{{ route('examination-session.store') }}');
                $('#_method').val('POST');
                $('#sessionForm')[0].reset();
                $('#sessionModalLabel').text('Add Examination Session');
                $('#sessionModal').modal('show');
            });

            $('.editBtn').click(function () {
                const id = $(this).data('id');
                $('#title').val($(this).data('title'));
                $('#start_date').val($(this).data('start'));
                $('#end_date').val($(this).data('end'));
                $('#description').val($(this).data('description'));
                $('#is_active').prop('checked', $(this).data('active'));
                $('#is_running').prop('checked', $(this).data('running'));
                $('#is_examination_taken').prop('checked', $(this).data('taken'));
                $('#sessionForm').attr('action', `/examination-sessions/${id}`);
                $('#_method').val('PUT');
                $('#sessionModalLabel').text('Edit Examination Session');
                $('#sessionModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this examination session?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) { form.submit(); }
                });
            });

            $(document).on('click', '.toggle-running-btn', function () {
                let button = $(this);
                let sessionId = button.data('id');
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm text-success"></span>');

                $.ajax({
                    url: `/examination-sessions/${sessionId}/toggle-running`,
                    method: 'PATCH',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        let badge = response.is_running
                            ? '<span class="badge badge-running rounded-pill">Running</span>'
                            : '<span class="badge badge-not-running rounded-pill">Not Running</span>';
                        button.data('running', response.is_running ? 1 : 0);
                        button.html(badge);
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: 'Running status updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update running status.' });
                    },
                    complete: function () {
                        button.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection

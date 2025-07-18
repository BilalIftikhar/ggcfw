@extends('layouts.app')

@section('title', 'Fees')

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
        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }
        .badge-fixed { background-color: #4caf50; color: white; }
        .badge-per-credit-hour { background-color: #2196f3; color: white; }
        .modal-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-bottom: none;
        }
        .form-control:focus {
            border-color: #4caf50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .action-buttons { min-width: 160px; }
        .table-hover tbody tr:hover { background-color: rgba(232, 245, 233, 0.3); }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Fees</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Fees</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_fee')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-sm rounded">
                            <div class="card-body text-end p-3">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle me-1"></i> Add Fee
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm rounded">
                        <div class="card-body">
                            <h5 class="card-title text-success">List of Fees</h5>
                            <div class="table-responsive">
                                <table id="feesTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Program</th>
                                        <th>Session</th>
                                        <th>Fee Group</th>
                                        <th>Fee Type</th>
                                        <th>Mode</th>
                                        <th>Amount</th>
                                        <th>Per Credit Rate</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($fees as $index => $fee)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $fee->program->name ?? 'N/A' }}</td>
                                            <td>{{ $fee->academicSession->name ?? 'N/A' }}</td>
                                            <td>{{ $fee->feeType->feeGroup->name ?? 'N/A' }}</td>
                                            <td>{{ $fee->feeType->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $fee->fee_mode == 'fixed' ? 'badge-fixed' : 'badge-per-credit-hour' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $fee->fee_mode)) }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($fee->amount, 2) }}</td>
                                            <td>{{ number_format($fee->per_credit_hour_rate, 2) }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('update_fee')
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 editBtn"
                                                                data-id="{{ $fee->id }}"
                                                                data-program="{{ $fee->program_id }}"
                                                                data-session="{{ $fee->academic_session_id }}"
                                                                data-fee-group="{{ $fee->feeType->fee_group_id ?? '' }}"
                                                                data-type="{{ $fee->fee_type_id }}"
                                                                data-mode="{{ $fee->fee_mode }}"
                                                                data-amount="{{ $fee->amount }}"
                                                                data-rate="{{ $fee->per_credit_hour_rate }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete_fee')
                                                        <form action="{{ route('fee.destroy', $fee->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger deleteBtn">
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
        <div class="modal fade" id="feeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="feeForm" method="POST">
                    @csrf
                    <input type="hidden" id="_method" name="_method" value="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="feeModalLabel">Add Fee</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Academic Session</label>
                                <select class="form-select" id="academic_session_id" name="academic_session_id" required>
                                    <option value="">Select Session</option>
                                    @foreach ($academicSessions as $session)
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Study Level</label>
                                <select class="form-select" id="study_level_id" name="study_level_id" disabled required>
                                    <option value="">Select Study Level</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Program</label>
                                <select class="form-select" id="program_id" name="program_id" disabled required>
                                    <option value="">Select Program</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Fee Group</label>
                                <select class="form-select" id="fee_group_id" name="fee_group_id" required>
                                    <option value="">Select Fee Group</option>
                                    @foreach ($feeGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Fee Type</label>
                                <select class="form-select" id="fee_type_id" name="fee_type_id" disabled required>
                                    <option value="">Select Fee Type</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Fee Mode</label>
                                <select class="form-select" id="fee_mode" name="fee_mode" required>
                                    <option value="fixed">Fixed</option>
                                    <option value="per_credit_hour">Per Credit Hour</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Amount</label>
                                <input type="number" step="0.01" class="form-control" name="amount" id="amount">
                            </div>
                            <div class="mb-3">
                                <label>Per Credit Hour Rate</label>
                                <input type="number" step="0.01" class="form-control" name="per_credit_hour_rate" id="per_credit_hour_rate">
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Fee</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#feesTable').DataTable({
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

            $('#openModalBtn').click(() => openFeeModal('add'));
            $('.editBtn').click(function () {
                openFeeModal('edit', $(this).data());
            });

            function openFeeModal(mode, data = {}) {
                $('#feeForm')[0].reset();
                if (mode === 'add') {
                    $('#feeModalLabel').text('Add Fee');
                    $('#feeForm').attr('action', '{{ route('fee.store') }}');
                    $('#_method').val('POST');
                } else {
                    $('#feeModalLabel').text('Edit Fee');
                    $('#feeForm').attr('action', `/fees/${data.id}`);
                    $('#_method').val('PUT');
                    $('#academic_session_id').val(data.session).trigger('change');
                    setTimeout(() => $('#program_id').val(data.program), 500);
                    $('#fee_group_id').val(data.feeGroup).trigger('change');
                    setTimeout(() => $('#fee_type_id').val(data.type), 500);
                    $('#fee_mode').val(data.mode);
                    $('#amount').val(data.amount);
                    $('#per_credit_hour_rate').val(data.rate);
                }
                $('#feeModal').modal('show');
            }

            $('#academic_session_id').change(function () {
                let id = $(this).val();
                $('#study_level_id').prop('disabled', true).html('<option>Loading...</option>');
                $('#program_id').prop('disabled', true).html('<option>Select Program</option>');
                if (id) {
                    $.get('/ajax-study-levels', { academic_session_id: id }, function (data) {
                        let options = '<option value="">Select Study Level</option>';
                        data.forEach(item => options += `<option value="${item.id}">${item.name}</option>`);
                        $('#study_level_id').html(options).prop('disabled', false);
                    });
                }
            });

            $('#study_level_id').change(function () {
                let id = $(this).val();
                $('#program_id').prop('disabled', true).html('<option>Loading...</option>');
                if (id) {
                    $.get('/ajax-programs', { study_level_id: id }, function (data) {
                        let options = '<option value="">Select Program</option>';
                        data.forEach(item => options += `<option value="${item.id}">${item.name}</option>`);
                        $('#program_id').html(options).prop('disabled', false);
                    });
                }
            });

            $('#fee_group_id').change(function () {
                let id = $(this).val();
                $('#fee_type_id').prop('disabled', true).html('<option>Loading...</option>');
                if (id) {
                    $.get('/ajax-fee-types', { fee_group_id: id }, function (data) {
                        let options = '<option value="">Select Fee Type</option>';
                        data.forEach(item => options += `<option value="${item.id}">${item.name}</option>`);
                        $('#fee_type_id').html(options).prop('disabled', false);
                    });
                } else {
                    $('#fee_type_id').html('<option>Select Fee Type</option>').prop('disabled', true);
                }
            });

            $('.deleteBtn').click(function () {
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this fee?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete Fee',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then(result => { if (result.isConfirmed) form.submit(); });
            });
        });
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Rooms')

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
            background-color: #e3f2fd;
            border-bottom: 2px solid #90caf9 !important;
            color: #1565c0;
            font-weight: 600;
        }
        .btn {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .modal-header {
            background: linear-gradient(135deg, #1565c0, #42a5f5);
            border-bottom: none;
        }
        .form-control:focus {
            border-color: #42a5f5;
            box-shadow: 0 0 0 0.2rem rgba(66, 165, 245, 0.25);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(227, 242, 253, 0.3);
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Rooms</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rooms</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_room')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end p-3">
                                <button type="button" class="btn btn-primary" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Room
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
                            <h5 class="card-title">List of Rooms</h5>
                            <div class="table-responsive">
                                <table id="roomsTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Room Number</th>
                                        <th>Building</th>
                                        <th>Capacity</th>
                                        <th>Room Type</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($rooms as $index => $room)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $room->room_number }}</td>
                                            <td>{{ $room->building ?? '-' }}</td>
                                            <td>{{ $room->capacity ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $room->room_type }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @can('updated_room')
                                                        <button type="button" class="btn btn-sm btn-outline-primary editBtn"
                                                                data-id="{{ $room->id }}"
                                                                data-room_number="{{ $room->room_number }}"
                                                                data-building="{{ $room->building }}"
                                                                data-capacity="{{ $room->capacity }}"
                                                                data-room_type="{{ $room->room_type }}"
                                                                data-bs-toggle="tooltip" title="Edit Room">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete_room')
                                                        <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip" title="Delete Room">
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
            <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="roomModalLabel">Add Room</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="roomForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="room_number" class="form-label">Room Number</label>
                                    <input type="text" class="form-control" name="room_number" id="room_number" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="building" class="form-label">Building</label>
                                    <input type="text" class="form-control" name="building" id="building">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="capacity" class="form-label">Capacity</label>
                                    <input type="number" class="form-control" name="capacity" id="capacity" min="1">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="room_type" class="form-label">Room Type</label>
                                    <select class="form-select" name="room_type" id="room_type" required>
                                        <option value="">Select Type</option>
                                        <option value="lab">Lab</option>
                                        <option value="lecture_hall">Lecture Hall</option>
                                    </select>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
            $('#roomsTable').DataTable({
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
                $('#roomForm').attr('action', '{{ route('rooms.store') }}');
                $('#_method').val('POST');
                $('#roomForm')[0].reset();
                $('#roomModalLabel').text('Add Room');
                $('#roomModal').modal('show');
            });

            $('.editBtn').click(function () {
                const id = $(this).data('id');
                $('#room_number').val($(this).data('room_number'));
                $('#building').val($(this).data('building'));
                $('#capacity').val($(this).data('capacity'));
                $('#room_type').val($(this).data('room_type'));
                $('#roomForm').attr('action', `/rooms/${id}`);
                $('#_method').val('PUT');
                $('#roomModalLabel').text('Edit Room');
                $('#roomModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this room?",
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
        });
    </script>
@endsection

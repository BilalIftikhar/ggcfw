@php use Carbon\Carbon; @endphp

@extends('layouts.app')

@section('title', 'Time Slots')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Time Slots Management</h1>
                <div class="d-flex">
                    {{-- Optional help button --}}
                </div>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Time Slots</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_time_slot')
                <div class="row mb-3">
                    <div class="col-12">
                        <button class="btn btn-sm btn-outline-primary" id="copySlotsBtn">
                            <i class="bi bi-files me-1"></i> Copy Slots
                        </button>
                    </div>
                </div>
            @endcan

            <div class="row">
                @foreach($workingDays->chunk(2) as $dayGroup)
                    <div class="row mb-4">
                        @foreach($dayGroup as $day)
                            <div class="col-lg-6">
                                <div class="card time-slot-card">
                                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-dark">
                                            <i class="bi bi-calendar-day me-2"></i>{{ ucfirst($day->day) }} Time Slots
                                        </h5>
                                        @can('create_time_slot')
                                            <button class="btn btn-sm btn-outline-primary show-form-btn" data-day="{{ $day->id }}">
                                                <i class="bi bi-plus-circle me-1"></i> Add Slot
                                            </button>
                                        @endcan
                                    </div>

                                    <div class="card-body">
                                        <div class="add-form mt-3 mb-4 d-none" id="form-{{ $day->id }}">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Add New Time Slot</h6>
                                                </div>
                                                <div class="card-body">
                                                    <form class="time-slot-form" data-day="{{ $day->id }}">
                                                        @csrf
                                                        <input type="hidden" name="working_day_id" value="{{ $day->id }}">

                                                        <div class="mb-3">
                                                            <label class="form-label">Slot Name</label>
                                                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Morning Session" required>
                                                        </div>

                                                        <div class="row g-2 mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Start Time</label>
                                                                <input type="time" name="start_time" class="form-control form-control-sm" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">End Time</label>
                                                                <input type="time" name="end_time" class="form-control form-control-sm" required>
                                                            </div>
                                                        </div>

                                                        <div class="row g-2 mb-3">
                                                            <div class="col-md-6">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" name="is_break" id="break-{{ $day->id }}">
                                                                    <label class="form-check-label" for="break-{{ $day->id }}">Mark as Break Time</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Sort Order</label>
                                                                <input type="number" name="sort_order" class="form-control form-control-sm" placeholder="1" required min="1">
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary cancel-add-btn" data-day="{{ $day->id }}">Cancel</button>
                                                            <button type="submit" class="btn btn-sm btn-primary">Save Slot</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        @if($day->timeSlots->isEmpty())
                                            <div class="alert alert-light mb-0">
                                                <i class="bi bi-info-circle me-2"></i> No time slots defined for this day.
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover table-sm mb-0">
                                                    <thead class="bg-light">
                                                    <tr>
                                                        <th width="25%">Name</th>
                                                        <th width="15%">Start</th>
                                                        <th width="15%">End</th>
                                                        <th width="10%" class="text-center">Break</th>
                                                        <th width="10%" class="text-center">Order</th>
                                                        <th width="25%" class="text-end">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($day->timeSlots->sortBy('sort_order') as $slot)
                                                        <tr data-id="{{ $slot->id }}" class="{{ $slot->is_break ? 'bg-light-warning' : '' }}">
                                                            <td>
                                                                <span class="view-mode">{{ $slot->name }}</span>
                                                                <input type="text" name="name" class="form-control form-control-sm d-none edit-mode" value="{{ $slot->name }}">
                                                            </td>
                                                            <td>
                                                                <span class="view-mode">{{ Carbon::parse($slot->start_time)->format('h:i A') }}</span>
                                                                <input type="time" name="start_time" class="form-control form-control-sm d-none edit-mode" value="{{ Carbon::parse($slot->start_time)->format('H:i') }}">
                                                            </td>
                                                            <td>
                                                                <span class="view-mode">{{ Carbon::parse($slot->end_time)->format('h:i A') }}</span>
                                                                <input type="time" name="end_time" class="form-control form-control-sm d-none edit-mode" value="{{ Carbon::parse($slot->end_time)->format('H:i') }}">
                                                            </td>
                                                            <td class="text-center">
                                                                    <span class="view-mode">
                                                                        @if($slot->is_break)
                                                                            <span class="badge bg-warning bg-opacity-25 text-warning-emphasis">Yes</span>
                                                                        @else
                                                                            <span class="badge bg-success bg-opacity-25 text-success-emphasis">No</span>
                                                                        @endif
                                                                    </span>
                                                                <select name="is_break" class="form-select form-select-sm d-none edit-mode">
                                                                    <option value="0" {{ !$slot->is_break ? 'selected' : '' }}>No</option>
                                                                    <option value="1" {{ $slot->is_break ? 'selected' : '' }}>Yes</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="view-mode">{{ $slot->sort_order }}</span>
                                                                <input type="number" name="sort_order" class="form-control form-control-sm d-none edit-mode" value="{{ $slot->sort_order }}" min="1">
                                                            </td>
                                                            <td class="text-end">
                                                                @can('updated_time_slots')
                                                                    <button class="btn btn-sm btn-outline-primary edit-btn view-mode">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-success update-btn d-none edit-mode">
                                                                        <i class="bi bi-check-circle"></i> Save
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-secondary cancel-btn d-none edit-mode">
                                                                        <i class="bi bi-x-circle"></i> Cancel
                                                                    </button>
                                                                @endcan
                                                                @can('delete_time_slot')
                                                                    <button class="btn btn-sm btn-outline-danger delete-btn view-mode">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Copy Slots Modal -->
        <div class="modal fade" id="copySlotsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Copy Time Slots</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="copySlotsForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Copy From (Source Day)</label>
                                <select name="source_day" class="form-select" required>
                                    <option value="">Select a day</option>
                                    @foreach($workingDays as $day)
                                        <option value="{{ $day->id }}">{{ ucfirst($day->day) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Paste To (Destination Days)</label>
                                <select name="destination_days[]" class="form-select" multiple required>
                                    @foreach($workingDays as $day)
                                        <option value="{{ $day->id }}">{{ ucfirst($day->day) }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple days</small>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="overwrite" id="overwriteSlots">
                                <label class="form-check-label" for="overwriteSlots">
                                    Overwrite existing slots in destination days
                                </label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmCopy">Copy Slots</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('styles')
    <style>
        .time-slot-card {
            border-radius: 0.5rem;
            border: 1px solid #e9ecef;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            height: 100%;
        }

        .time-slot-card:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            border-color: #dee2e6;
        }

        .time-slot-card .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 0.75rem 1.25rem;
            background-color: #f8f9fa !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .edit-mode .form-control, .edit-mode .form-select {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border: 1px solid #dee2e6;
        }

        .bg-light-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .badge {
            padding: 0.35em 0.5em;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Copy Slots Modal Styles */
        select[multiple] {
            min-height: 120px;
        }
        .modal-body .form-select {
            padding: 0.375rem 0.75rem;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Toggle add form
            $('.show-form-btn').click(function () {
                const dayId = $(this).data('day');
                $('#form-' + dayId).removeClass('d-none');
                $(this).addClass('d-none');
                $('html, body').animate({
                    scrollTop: $('#form-' + dayId).offset().top - 20
                }, 200);
            });

            // Cancel add form
            $('.cancel-add-btn').click(function() {
                const dayId = $(this).data('day');
                $('#form-' + dayId).addClass('d-none');
                $('.show-form-btn[data-day="' + dayId + '"]').removeClass('d-none');
            });

            // Create new slot
            $('.time-slot-form').submit(function (e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = form.find('[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                $.post("{{ route('time-slots.store') }}", form.serialize())
                    .done(res => {
                        toastr.success(res.message || 'Time slot saved successfully.');
                        form.closest('.add-form').addClass('d-none');
                        form[0].reset();
                        $('.show-form-btn[data-day="' + form.data('day') + '"]').removeClass('d-none');
                        setTimeout(() => location.reload(), 1000);
                    })
                    .fail(xhr => {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, (key, val) => toastr.error(val[0]));
                        } else {
                            toastr.error('Error saving time slot. Please try again.');
                        }
                    })
                    .always(() => {
                        submitBtn.prop('disabled', false).html(originalText);
                    });
            });

            // Edit
            $(document).on('click', '.edit-btn', function () {
                const row = $(this).closest('tr');
                row.find('.view-mode').addClass('d-none');
                row.find('.edit-mode').removeClass('d-none');
                row.find('.edit-mode').first().focus();
            });

            // Cancel edit
            $(document).on('click', '.cancel-btn', function () {
                const row = $(this).closest('tr');
                row.find('.edit-mode').addClass('d-none');
                row.find('.view-mode').removeClass('d-none');
            });

            // Update
            $(document).on('click', '.update-btn', function () {
                const row = $(this).closest('tr');
                const id = row.data('id');
                const updateBtn = $(this);
                const originalText = updateBtn.html();

                updateBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                const data = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    name: row.find('input[name="name"]').val(),
                    start_time: row.find('input[name="start_time"]').val(),
                    end_time: row.find('input[name="end_time"]').val(),
                    is_break: row.find('select[name="is_break"]').val(),
                    sort_order: row.find('input[name="sort_order"]').val()
                };

                $.ajax({
                    url: `/time-slots/${id}`,
                    method: 'POST',
                    data: data,
                    success: function (res) {
                        toastr.success(res.message || 'Time slot updated successfully.');
                        setTimeout(() => location.reload(), 800);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, (key, val) => toastr.error(val[0]));
                        } else {
                            toastr.error('Update failed. Please try again.');
                        }
                        updateBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Delete
            $(document).on('click', '.delete-btn', function () {
                const row = $(this).closest('tr');
                const id = row.data('id');
                const slotName = row.find('input[name="name"]').val() || 'this time slot';

                Swal.fire({
                    title: 'Confirm Deletion',
                    html: `Are you sure you want to delete <strong>${slotName}</strong>?<br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/time-slots/${id}`,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            beforeSend: function() {
                                Swal.showLoading();
                            },
                            success: function (res) {
                                Swal.fire(
                                    'Deleted!',
                                    res.message || 'Time slot has been deleted.',
                                    'success'
                                );
                                setTimeout(() => location.reload(), 800);
                            },
                            error: function () {
                                Swal.fire(
                                    'Error',
                                    'Failed to delete time slot.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Copy Slots functionality
            const copySlotsModal = new bootstrap.Modal(document.getElementById('copySlotsModal'));

            // Show modal when copy button is clicked
            $('#copySlotsBtn').click(function() {
                $('#copySlotsForm')[0].reset();
                copySlotsModal.show();
            });

            // Handle copy confirmation
            $('#confirmCopy').click(function() {
                const form = $('#copySlotsForm');
                const btn = $(this);
                const originalText = btn.html();

                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Copying...');

                $.post("{{ route('time-slots.copy') }}", form.serialize())
                    .done(res => {
                        toastr.success(res.message || 'Time slots copied successfully!');
                        copySlotsModal.hide();
                        setTimeout(() => location.reload(), 1000);
                    })
                    .fail(xhr => {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, (key, val) => toastr.error(val[0]));
                        } else {
                            toastr.error(xhr.responseJSON.message || 'Error copying time slots. Please try again.');
                        }
                    })
                    .always(() => {
                        btn.prop('disabled', false).html(originalText);
                    });
            });
        });
    </script>
@endsection

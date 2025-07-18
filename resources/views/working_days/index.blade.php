@extends('layouts.app')

@section('title', 'Working Days')

@section('content')
    <style>
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.4em;
            cursor: pointer;
        }
        .border-dashed {
            border-style: dashed !important;
            border-color: #dee2e6 !important;
        }
        .bg-success-light {
            background-color: rgba(15, 216, 123, 0.1);
        }
        .bg-secondary-light {
            background-color: rgba(108, 117, 125, 0.1);
        }
        .card {
            border-radius: 0.5rem;
            width: calc(100% - 2rem);
            margin: 0 auto;
        }
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border-bottom: 1px dashed #81c784 !important;
            padding: 1rem 1.5rem;
        }
        .main-content {
            min-height: calc(100vh - 120px);
            padding-bottom: 2rem;
        }
        .status-header {
            text-align: center;
            padding-right: 3.5rem;
        }
        .status-cell {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .alert-info-custom {
            background-color: #e8f5e9;
            border-color: #c8e6c9;
            color: #2e7d32;
        }
    </style>

    <main id="main" class="main main-content">
        <div class="pagetitle">
            <h1>Working Days Configuration</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Working Days</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header card-header-custom">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-calendar-week me-2"></i>Set Working Days
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="alert alert-info-custom border border-dashed mb-4">
                                <i class="bi bi-info-circle me-2"></i> Toggle the switches to update which days are considered working days
                            </div>

                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead>
                                    <tr class="border-bottom border-dashed">
                                        <th style="width: 60%" class="ps-4">Day</th>
                                        <th style="width: 40%" class="status-header">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($workingDays as $day)
                                        <tr class="{{ $loop->last ? '' : 'border-bottom border-dashed' }}">
                                            <td class="ps-4 fw-medium">
                                                <i class="bi bi-calendar-day me-2 text-muted"></i>{{ ucfirst($day->day) }}
                                            </td>
                                            <td>
                                                <div class="status-cell">
                                                    @can('updated_working_days')
                                                        <div class="form-check form-switch">
                                                            <input
                                                                class="form-check-input working-toggle"
                                                                type="checkbox"
                                                                role="switch"
                                                                data-id="{{ $day->id }}"
                                                                id="day-{{ $day->id }}"
                                                                {{ $day->is_working ? 'checked' : '' }}>
                                                            <label class="form-check-label ms-2" for="day-{{ $day->id }}">
                                                                <span class="badge bg-{{ $day->is_working ? 'success-light text-success' : 'secondary-light text-secondary' }}">
                                                                    {{ $day->is_working ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-{{ $day->is_working ? 'success-light text-success' : 'secondary-light text-secondary' }}">
                                                            {{ $day->is_working ? 'Working' : 'Non-Working' }}
                                                        </span>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 text-muted small">
                                <i class="bi bi-lightbulb me-1"></i> Changes will affect all scheduling and time tracking calculations
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
            $('.working-toggle').change(function () {
                let dayId = $(this).data('id');
                let isWorking = $(this).is(':checked') ? 1 : 0;
                let $badge = $(this).next().find('.badge');

                // Update visual immediately
                $badge.removeClass('bg-success-light text-success bg-secondary-light text-secondary')
                    .addClass(isWorking ? 'bg-success-light text-success' : 'bg-secondary-light text-secondary')
                    .text(isWorking ? 'Active' : 'Inactive');

                $.ajax({
                    url: "{{ route('working-days.toggle') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: dayId,
                        is_working: isWorking
                    },
                    success: function (res) {
                        toastr.success(res.message || 'Working day status updated successfully');
                    },
                    error: function (err) {
                        console.log(err);
                        toastr.error('Failed to update working day status');
                        // Revert visual changes
                        $badge.removeClass('bg-success-light text-success bg-secondary-light text-secondary')
                            .addClass(!isWorking ? 'bg-success-light text-success' : 'bg-secondary-light text-secondary')
                            .text(!isWorking ? 'Active' : 'Inactive');
                        $(this).prop('checked', !isWorking);
                    }
                });
            });
        });
    </script>
@endsection

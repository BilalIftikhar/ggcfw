<form method="POST" action="{{ route('attendance.daily.store') }}">
    @csrf

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Select Class for Attendance</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Academic Session</label>
                    <select name="academic_session_id" class="form-select academic-session" required>
                        <option value="">Select Academic Session</option>
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ old('academic_session_id', $selectedFilters['academic_session_id'] ?? '') == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Study Level</label>
                    <select name="study_level_id" class="form-select study-level" required disabled>
                        <option value="">Select Study Level</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Program</label>
                    <select name="program_id" class="form-select program" required disabled>
                        <option value="">Select Program</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Class</label>
                    <select name="program_class_id" class="form-select program-class" required disabled>
                        <option value="">Select Class</option>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Attendance Date</label>
                    <input type="date" name="attendance_date" class="form-control"
                           value="{{ old('attendance_date', isset($selectedDate) ? $selectedDate->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Load Class
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@if(($attendanceType ?? null) === 'daily' && isset($selectedDate) && isset($students) && count($students))
    <!-- Attendance Card -->
    <div class="card shadow-sm" id="daily-attendance-card">
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i>
                    Mark Attendance for {{ $selectedDate->format('l, j F Y') }}
                </h5>
                <div class="text-end">
                    <span class="badge bg-light text-dark fs-6">
                        {{ $selectedFilters['program_name'] ?? '' }} - {{ $selectedFilters['class_name'] ?? '' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="35%">Student Name</th>
                        <th width="60%">Attendance Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $index => $student)
                        @php
                            $attendance = $attendanceRecords[$student->id] ?? null;
                            $dayKey = 'day_' . $selectedDate->day;
                            $status = $attendance?->$dayKey;

                            $reverseMap = [
                                'present' => 'P',
                                'absent' => 'A',
                                'late' => 'L',
                                'leave' => 'H',
                            ];

                            $labelMap = [
                                'P' => 'Present',
                                'A' => 'Absent',
                                'L' => 'Late',
                                'H' => 'Leave',
                            ];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Attendance options" style="gap: 1.0rem;">
                                    @foreach(['P' => 'present', 'A' => 'absent', 'L' => 'late', 'H' => 'leave'] as $code => $label)
                                        <input type="radio"
                                               class="btn-check attendance-radio"
                                               name="attendance_{{ $student->id }}"
                                               id="attendance_{{ $student->id }}_{{ $code }}"
                                               value="{{ $code }}"
                                               data-id="{{ $attendance?->id }}"
                                               data-day="{{ $selectedDate->day }}"
                                               autocomplete="off"
                                            {{ isset($status) && ($reverseMap[$status] ?? '') === $code ? 'checked' : '' }}>
                                        <label class="btn btn-outline-{{ $label === 'present' ? 'success' : ($label === 'absent' ? 'danger' : ($label === 'late' ? 'warning' : 'info')) }}"
                                               for="attendance_{{ $student->id }}_{{ $code }}">
                                            {{ $labelMap[$code] }}
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between">
                <span class="text-muted">
                    Total Students: {{ count($students) }}
                </span>
                <button type="button" class="btn btn-secondary" id="close-daily-attendance">
                    <i class="fas fa-times me-2"></i> Close
                </button>
            </div>
        </div>
    </div>
@endif

@if(($attendanceType ?? null) === 'daily' && isset($selectedDate) && (!isset($students) || count($students) === 0))
    <div class="alert alert-warning mt-4">
        <i class="fas fa-exclamation-triangle me-2"></i> No students found for the selected class.
    </div>
@endif

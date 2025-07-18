<form method="POST" action="{{ route('attendance.subject.store') }}">
    @csrf

    <!-- Static Dropdowns -->
    <div class="row mt-3">
        <div class="col-md-3">
            <label>Academic Session</label>
            <select name="academic_session_id" class="form-select academic-session" required>
                <option value="">Select Academic Session</option>
                @foreach($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ old('academic_session_id', $selectedFilters['academic_session_id'] ?? '') == $session->id ? 'selected' : '' }}>
                        {{ $session->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Cascading Dropdowns -->
        <div class="col-md-3">
            <label>Study Level</label>
            <select name="study_level_id" class="form-select study-level" required disabled>
                <option value="">Select Study Level</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>Program</label>
            <select name="program_id" class="form-select program" required disabled>
                <option value="">Select Program</option>
            </select>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-3">
            <label>Class</label>
            <select name="program_class_id" class="form-select program-class" required disabled>
                <option value="">Select Class</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>Course</label>
            <select name="course_id" class="form-select course" required disabled>
                <option value="">Select Course</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>Course Section</label>
            <select name="course_section_id" class="form-select course-section" required disabled>
                <option value="">Select Section</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>Slot</label>
            <select name="timetable_slot_id" class="form-select timetable" required disabled>
                <option value="">Select Slot</option>
            </select>
        </div>
    </div>

    <!-- Attendance Date -->
    <div class="row mt-3">
        <div class="col-md-3">
            <label>Attendance Date</label>
            <input type="date" name="attendance_date" class="form-control"
                   value="{{ old('attendance_date', isset($selectedDate) ? $selectedDate->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        </div>

        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-search me-2"></i> Load Attendance
            </button>
        </div>
    </div>
</form>

@if(($attendanceType ?? null) === 'subject' && isset($selectedDate) && isset($students) && count($students))
    <!-- Attendance Card -->
    <div class="card shadow-sm mt-4" id="subject-attendance-card">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                    Subject Attendance for {{ $selectedDate->format('l, j F Y') }}
                </h5>
                <div class="text-end">
                    <span class="badge bg-light text-dark fs-6">
                        {{ $selectedFilters['course_name'] ?? '' }} - {{ $selectedFilters['section_name'] ?? '' }}
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
                            $status = $attendance?->status;
                            $shortStatus = $status ? array_search($status, \App\Models\SubjectAttendance::STATUS_MAP) : null;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group" style="gap: 1.0rem;" aria-label="Attendance options">
                                    @foreach(['P' => 'Present', 'A' => 'Absent', 'L' => 'Late', 'H' => 'Leave'] as $code => $label)
                                        <input type="radio"
                                               class="btn-check attendance-radio"
                                               name="attendance_{{ $student->id }}"
                                               id="attendance_{{ $student->id }}_{{ $code }}"
                                               value="{{ $code }}"
                                               data-id="{{ $attendance?->id }}"
                                               autocomplete="off"
                                            {{ $shortStatus === $code ? 'checked' : '' }}>
                                        <label class="btn btn-outline-{{ $code === 'P' ? 'success' : ($code === 'A' ? 'danger' : ($code === 'L' ? 'warning' : 'info')) }}"
                                               for="attendance_{{ $student->id }}_{{ $code }}">
                                            {{ $label }}
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
                <button type="button" class="btn btn-secondary" id="close-subject-attendance">
                    <i class="fas fa-times me-2"></i> Close
                </button>
            </div>
        </div>
    </div>
@endif

@if(($attendanceType ?? null) === 'subject' && isset($selectedDate) && (!isset($students) || count($students) === 0))
    <div class="alert alert-warning mt-4">
        <i class="fas fa-exclamation-triangle me-2"></i> No students found for the selected course section.
    </div>
@endif

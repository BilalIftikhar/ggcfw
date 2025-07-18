@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Attendance</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filter Attendance Records</h5>

                    <ul class="nav nav-tabs" id="attendanceTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $attendanceType === 'daily' ? 'active' : '' }}"
                                    id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily"
                                    type="button" role="tab">Daily Attendance</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $attendanceType === 'subject' ? 'active' : '' }}"
                                    id="subject-tab" data-bs-toggle="tab" data-bs-target="#subject"
                                    type="button" role="tab">Subject-wise Attendance</button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2" id="attendanceTypeTabsContent">
                        <!-- Daily Attendance Tab -->
                        <div class="tab-pane fade {{ $attendanceType === 'daily' ? 'show active' : '' }}"
                             id="daily" role="tabpanel">
                            @include('attendance.partials.daily-filter')
                        </div>

                        <!-- Subject-wise Attendance Tab -->
                        <div class="tab-pane fade {{ $attendanceType === 'subject' ? 'show active' : '' }}"
                             id="subject" role="tabpanel">
                            @include('attendance.partials.subject-filter')
                        </div>
                    </div>

                    @if(count($students))
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Attendance Records</h5>
                                @if($attendanceType === 'daily')
                                    <span class="badge bg-primary">Daily Attendance -
                                        {{ $month ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : "Year $year" }}
                                    </span>
                                @else
                                    <span class="badge bg-primary">Subject-wise Attendance -
                                        {{ $date ? date('d F Y', strtotime($date)) : '' }}
                                    </span>
                                @endif
                            </div>

                            @if($attendanceType === 'daily')
                                <!-- Daily Attendance Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                        <tr>
                                            <th rowspan="2">Student Name</th>
                                            <th rowspan="2">Student ID</th>
                                            <th colspan="{{ $month ? \Carbon\Carbon::create($year, $month)->daysInMonth : 12 }}" class="text-center">Days/Months</th>
                                            <th rowspan="2">Present</th>
                                            <th rowspan="2">Absent</th>
                                            <th rowspan="2">Late</th>
                                            <th rowspan="2">Percentage</th>
                                        </tr>
                                        <tr>
                                            @if($month)
                                                @foreach(range(1, \Carbon\Carbon::create($year, $month)->daysInMonth) as $day)
                                                    <th>{{ $day }}</th>
                                                @endforeach
                                            @else
                                                @foreach(range(1, 12) as $m)
                                                    <th>{{ \Carbon\Carbon::create()->month($m)->format('M') }}</th>
                                                @endforeach
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($students as $student)
                                            @php
                                                $record = $attendanceRecords[$student->id] ?? null;
                                                $summary = $record?->getSummary() ?? ['P' => 0, 'A' => 0, 'L' => 0];
                                                $percentage = $record?->getMonthlyAttendancePercentage() ?? 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->student_id }}</td>
                                                @if($month)
                                                    @foreach(range(1, \Carbon\Carbon::create($year, $month)->daysInMonth) as $day)
                                                        <td class="text-center {{ $record?->getDay($day) === 'P' ? 'bg-success-light' : ($record?->getDay($day) === 'A' ? 'bg-danger-light' : '') }}">
                                                            {{ $record?->getDay($day) ?? '-' }}
                                                        </td>
                                                    @endforeach
                                                @else
                                                    @foreach(range(1, 12) as $m)
                                                        <td class="text-center">
                                                            {{ $record && $record->month == $m ? '✔' : '-' }}
                                                        </td>
                                                    @endforeach
                                                @endif
                                                <td class="text-center">{{ $summary['P'] }}</td>
                                                <td class="text-center">{{ $summary['A'] }}</td>
                                                <td class="text-center">{{ $summary['L'] }}</td>
                                                <td class="text-center fw-bold {{ $percentage >= 75 ? 'text-success' : ($percentage >= 50 ? 'text-warning' : 'text-danger') }}">
                                                    {{ $percentage }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <!-- Subject-wise Attendance Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Time</th>
                                            <th>Marked By</th>
                                            <th>Remarks</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($students as $student)
                                            @php
                                                $record = $attendanceRecords[$student->id] ?? null;
                                            @endphp
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->student_id }}</td>
                                                <td>{{ $record?->timetable?->courseSection?->course?->name ?? '-' }}</td>
                                                <td class="text-center {{ $record?->status === 'P' ? 'bg-success-light' : ($record?->status === 'A' ? 'bg-danger-light' : '') }}">
                                                    {{ $record?->status ?? 'Not Marked' }}
                                                </td>
                                                <td>{{ $record?->created_at ? $record->created_at->format('h:i A') : '-' }}</td>
                                                <td>{{ $record?->markedBy?->name ?? '-' }}</td>
                                                <td>{{ $record?->remarks ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @elseif(request()->isMethod('post'))
                        <div class="alert alert-info mt-4">
                            No attendance records found for the selected criteria.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize dropdown states based on current selections
            if($('#academic_session_id').val()) {
                $('#study_level_id').prop('disabled', false);
            }
            if($('#study_level_id').val()) {
                $('#program_id').prop('disabled', false);
            }
            if($('#program_id').val()) {
                $('#program_class_id').prop('disabled', false);
                $('#timetable_id').prop('disabled', false);
            }

            // Academic Session → Study Level
            $('#academic_session_id').on('change', function() {
                let sessionId = $(this).val();
                $('#study_level_id, #program_id, #program_class_id, #timetable_id').prop('disabled', true).empty().append('<option value="">Select...</option>');

                if(!sessionId) return;

                $.get(`/ajax-study-levels?academic_session_id=${sessionId}`, function(data) {
                    $('#study_level_id').prop('disabled', false).empty().append('<option value="">Select Study Level</option>');
                    $.each(data, function(i, level) {
                        $('#study_level_id').append(`<option value="${level.id}">${level.name}</option>`);
                    });
                });
            });

            // Study Level → Program
            $('#study_level_id').on('change', function() {
                let levelId = $(this).val();
                $('#program_id, #program_class_id, #timetable_id').prop('disabled', true).empty().append('<option value="">Select...</option>');

                if(!levelId) return;

                $.get(`/ajax-programs?study_level_id=${levelId}`, function(data) {
                    $('#program_id').prop('disabled', false).empty().append('<option value="">Select Program</option>');
                    $.each(data, function(i, program) {
                        $('#program_id').append(`<option value="${program.id}">${program.name}</option>`);
                    });
                });
            });

            // Program → Program Class & Timetable
            $('#program_id').on('change', function() {
                let programId = $(this).val();
                $('#program_class_id, #timetable_id').prop('disabled', true).empty().append('<option value="">Select...</option>');

                if(!programId) return;

                // Load classes
                $.get(`/program-classes?program_id=${programId}`, function(data) {
                    $('#program_class_id').prop('disabled', false).empty().append('<option value="">Select Class</option>');
                    $.each(data, function(i, cls) {
                        $('#program_class_id').append(`<option value="${cls.id}">${cls.name}</option>`);
                    });
                });
            });

            // Program Class → Timetable
            $('#program_class_id').on('change', function() {
                let classId = $(this).val();
                $('#timetable_id').prop('disabled', true).empty().append('<option value="">Select...</option>');

                if(!classId) return;

                // Load timetables for subject-wise attendance
                $.get(`/ajax-timetables?program_class_id=${classId}`, function(data) {
                    $('#timetable_id').prop('disabled', false).empty().append('<option value="">Select Subject</option>');
                    $.each(data, function(i, timetable) {
                        $('#timetable_id').append(`<option value="${timetable.id}">${timetable.courseSection?.course?.name || 'Unknown'} (${timetable.day})</option>`);
                    });
                });
            });
        });
    </script>
@endsection

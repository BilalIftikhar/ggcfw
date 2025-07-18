@extends('layouts.app')

@section('title', 'Timetable')

@section('content')
    <style>
        /* Timetable Styles */
        .program-group {
            margin-bottom: 2.5rem;
            border: 1px solid #e1e4e8;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .program-group:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .program-header {
            background-color: #3b7ddd;
            color: #fff;
            padding: 14px 24px;
            font-weight: 600;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .program-header:hover {
            background-color: #2f6bc5;
        }

        .class-header {
            background-color: #f8fafd;
            padding: 12px 24px;
            font-weight: 600;
            border-bottom: 1px solid #e1e4e8;
            font-size: 1.1rem;
            color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .class-header:hover {
            background-color: #f1f5fd;
        }

        .lecture-slot-timetable {
            border-collapse: separate;
            border-spacing: 8px;
            width: 100%;
        }

        .lecture-slot-timetable th {
            background-color: #3b7ddd;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
            text-align: center;
            padding: 14px 10px;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .day-cell {
            font-weight: 600;
            background-color: #f8f9fa;
            width: 120px;
            text-align: center;
            vertical-align: middle;
            color: #2c3e50;
            font-size: 0.9rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .lecture-slot {
            min-width: 180px;
            vertical-align: middle;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            height: 120px;
            position: relative;
        }

        .lecture-slot:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .time-display {
            font-size: 0.8rem;
            color: #2c3e50;
            font-weight: 600;
            text-align: center;
            padding: 6px;
            background-color: #f1f5fd;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .course-card {
            border-radius: 8px;
            padding: 12px;
            margin: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .lecture-card {
            background-color: #f0f7ff;
            border-left: 4px solid #3b7ddd;
            color: #2c3e50;
        }

        .lab-card {
            background-color: #fff9e6;
            border-left: 4px solid #ffc107;
            color: #2c3e50;
        }

        .break-card {
            background-color: #f8f9fa;
            border-left: 4px solid #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #495057;
            font-weight: 500;
            font-size: 0.9rem;
            text-align: center;
            padding: 10px;
        }

        .break-card i {
            margin-right: 8px;
            color: #6c757d;
        }

        .free-card {
            background-color: #f0fff4;
            border-left: 4px solid #38a169;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #2f855a;
            font-weight: 500;
            font-size: 0.9rem;
            text-align: center;
            padding: 10px;
        }

        .free-card i {
            margin-right: 8px;
            color: #38a169;
        }

        .course-name {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #2c3e50;
            line-height: 1.3;
        }

        .course-meta {
            margin-bottom: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .lecture-badge {
            background-color: #3b7ddd;
            color: white;
        }

        .lab-badge {
            background-color: #ffc107;
            color: #343a40;
        }

        .instructor, .room {
            font-size: 0.8rem;
            color: #495057;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .room {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .multiple-sections {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .section-badge {
            background-color: #6c757d;
            color: white;
        }

        /* Collapsible sections */
        .collapsible-header {
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .collapsible-header::after {
            content: '\f282';
            font-family: 'bootstrap-icons';
            transition: transform 0.3s ease;
            font-size: 1rem;
        }

        .collapsible-header.collapsed::after {
            content: '\f285';
        }

        .collapsible-content {
            transition: max-height 0.3s ease, opacity 0.3s ease;
            overflow: hidden;
        }

        .collapsible-content.collapsed {
            max-height: 0 !important;
            opacity: 0;
        }

        /* Responsive styles */
        @media (max-width: 767.98px) {
            .lecture-slot-timetable {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .lecture-slot-timetable thead {
                display: none;
            }

            .lecture-slot-timetable tbody {
                display: block;
            }

            .lecture-slot-timetable tr {
                display: flex;
                flex-direction: column;
                margin-bottom: 20px;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                overflow: hidden;
            }

            .lecture-slot-timetable td {
                display: block;
                border-bottom: 1px solid #dee2e6;
                height: auto;
            }

            .lecture-slot-timetable td:last-child {
                border-bottom: none;
            }

            .day-cell {
                width: 100%;
                text-align: left;
                padding-left: 15px;
                background-color: #3b7ddd;
                color: white;
                font-size: 1rem;
            }

            .lecture-slot::before {
                content: attr(data-label);
                font-weight: bold;
                display: inline-block;
                width: 100px;
                color: #495057;
            }

            .lecture-slot {
                display: flex;
                align-items: center;
                min-height: 60px;
                padding: 12px;
                box-shadow: none;
                background-color: transparent;
            }

            .time-display {
                display: none;
            }

            .course-card, .break-card, .free-card {
                flex-grow: 1;
                height: auto;
            }

            .program-group {
                margin-bottom: 1.5rem;
            }

            .course-name {
                font-size: 0.95rem;
            }
        }

        /* Filter card styles */
        .filter-card {
            border-left: 4px solid #3b7ddd;
            background-color: #f8f9fa;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Empty state styles */
        .empty-state {
            padding: 3rem;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .empty-state-title {
            color: #343a40;
            font-size: 1.5rem;
        }

        .empty-state-text {
            color: #6c757d;
            font-size: 1.1rem;
        }

        /* Improved hover effects for cells */
        .lecture-slot-timetable td:not(.day-cell):hover {
            transform: scale(1.02);
            z-index: 5;
        }

        /* Better alignment for course content */
        .entries-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Compact layout for multiple sections */
        .multiple-sections {
            max-height: 80px;
            overflow-y: auto;
            padding-right: 4px;
        }

        /* Custom scrollbar */
        .multiple-sections::-webkit-scrollbar {
            width: 4px;
        }

        .multiple-sections::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .multiple-sections::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Timetable</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Timetable</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">View Timetable</h5>
                        <div>

                            <button class="btn btn-sm btn-outline-success me-2" id="exportExcel">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="printTimetable">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>
                    </div>

                    <!-- Filter Card -->
                    <div class="card filter-card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4 text-primary d-flex align-items-center">
                                <i class="fas fa-filter me-2"></i> Filter Timetable
                            </h5>

                            <form method="GET" action="{{ route('timetable.index') }}" class="row g-3" id="timetableFilterForm">
                                <!-- Row 1: Examination Session (Required), Academic Session, Study Level -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted mb-1">Examination Session <span class="text-danger">*</span></label>
                                    <select name="examination_session_id" class="form-select shadow-sm" required>
                                        <option value="">Select Exam</option>
                                        @foreach($examinationSessions as $exam)
                                            <option value="{{ $exam->id }}" {{ request('examination_session_id') == $exam->id ? 'selected' : '' }}>
                                                {{ $exam->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted mb-1">Academic Session</label>
                                    <select name="academic_session_id" id="academic_session_id" class="form-select shadow-sm">
                                        <option value="">Select Session</option>
                                        @foreach($academicSessions as $session)
                                            <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted mb-1">Study Level</label>
                                    <select name="study_level_id" id="study_level_id" class="form-select shadow-sm" {{ empty($studyLevels) ? 'disabled' : '' }}>
                                        <option value="">Select Study Level</option>
                                        @foreach($studyLevels ?? [] as $level)
                                            <option value="{{ $level->id }}" {{ request('study_level_id') == $level->id ? 'selected' : '' }}>
                                                {{ $level->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Row 2: Program, Program Class -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted mb-1">Program</label>
                                    <select name="program_id" id="program_id" class="form-select shadow-sm" {{ empty($programs) ? 'disabled' : '' }}>
                                        <option value="">Select Program</option>
                                        @foreach($programs ?? [] as $program)
                                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted mb-1">Program Class</label>
                                    <select name="program_class_id" id="program_class_id" class="form-select shadow-sm" {{ empty($programClasses) ? 'disabled' : '' }}>
                                        <option value="">Select Program Class</option>
                                        @foreach($programClasses ?? [] as $class)
                                            <option value="{{ $class->id }}" {{ request('program_class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-4 d-flex gap-2 align-items-end">
                                    <button type="submit" class="btn btn-primary flex-fill shadow-sm" id="filterButton">
                                        <span class="button-text">Apply Filters</span>
                                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary flex-fill" id="resetFilters">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>




                    <!-- Timetable View -->

                    <div class="timetable-view">
                        @if(!empty($structuredData))
                            @foreach($structuredData as $program)
                                <div class="program-group">
                                    <div class="program-header collapsible-header" data-bs-toggle="collapse"
                                         data-bs-target="#program-{{ $program['program_id'] }}">
                                        {{ $program['program_name'] }}
                                    </div>

                                    <div id="program-{{ $program['program_id'] }}" class="collapsible-content show">
                                        @foreach($program['classes'] as $class)
                                            <div class="class-group">
                                                <div class="class-header collapsible-header" data-bs-toggle="collapse"
                                                     data-bs-target="#class-{{ $class['class_id'] }}">
                                                    Class: {{ $class['class_name'] }}
                                                    ]
                                                </div>

                                                <div id="class-{{ $class['class_id'] }}"
                                                     class="collapsible-content show">
                                                    <div class="table-responsive">
                                                        <table class="table lecture-slot-timetable">
                                                            <thead>
                                                            <tr>
                                                                <th>Day/Lecture</th>
                                                                @php
                                                                    // Get the first day's time slots to determine column headers
                                                                    $firstDay = reset($class['timetable']);
                                                                    $timeSlotIds = array_keys($firstDay);
                                                                    $lectureNumber = 1;
                                                                @endphp
                                                                @foreach($timeSlotIds as $slotId)
                                                                    @php
                                                                        $timeSlot = \App\Models\TimeSlot::find($slotId);
                                                                    @endphp
                                                                    <th>
                                                                        <div class="time-display">
                                                                            {{ \Carbon\Carbon::parse($timeSlot->start_time ?? '00:00')->format('h:i A') }}
                                                                            -
                                                                            {{ \Carbon\Carbon::parse($timeSlot->end_time ?? '00:00')->format('h:i A') }}
                                                                        </div>
                                                                        <div>
                                                                            @if($timeSlot->is_break)
                                                                                Break Time
                                                                            @else
                                                                                Lecture {{ $lectureNumber++ }}
                                                                            @endif
                                                                        </div>
                                                                    </th>
                                                                @endforeach
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($class['timetable'] as $day => $daySlots)
                                                                <tr>
                                                                    <td class="day-cell">
                                                                        {{ ucfirst($day) }}
                                                                    </td>
                                                                    @foreach($daySlots as $slotId => $slotData)
                                                                        @php
                                                                            $timeSlot = \App\Models\TimeSlot::find($slotId);
                                                                        @endphp
                                                                        <td class="lecture-slot">
                                                                            @if($timeSlot->is_break)
                                                                                <div class="break-card">
                                                                                    <i class="bi bi-cup-hot"></i> Break
                                                                                </div>
                                                                            @elseif(!empty($slotData['entries']))
                                                                                <div class="entries-container">
                                                                                    @foreach($slotData['entries'] as $entry)
                                                                                        <div
                                                                                            class="course-card {{ $entry['type'] === 'lab' ? 'lab-card' : 'lecture-card' }} mb-2">
                                                                                            <div class="course-name">
                                                                                                {{ $entry['course_name'] }}
                                                                                            </div>
                                                                                            <div class="course-meta">
                                                                                                @if($entry['has_multiple_sections'])
                                                                                                    <span
                                                                                                        class="badge section-badge">Multiple Sections</span>
                                                                                                @endif
                                                                                                <span
                                                                                                    class="badge {{ $entry['type'] === 'lab' ? 'lab-badge' : 'lecture-badge' }}">
                                                                                    {{ ucfirst($entry['type']) }}
                                                                                </span>
                                                                                            </div>

                                                                                            @if($entry['has_multiple_sections'])
                                                                                                <div
                                                                                                    class="multiple-sections">
                                                                                                    @foreach($entry['sections'] as $section)
                                                                                                        <div>
                                                                                                            <div
                                                                                                                class="instructor">{{ $section['teacher'] }}</div>
                                                                                                            <div
                                                                                                                class="room">
                                                                                                                <i class="bi bi-geo-alt"></i>
                                                                                                                {{ $section['room'] }}
                                                                                                                (Sec: {{ $section['section_name'] }}
                                                                                                                )
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            @else
                                                                                                @php $section = $entry['sections'][0] @endphp
                                                                                                <div
                                                                                                    class="instructor">{{ $section['teacher'] }}</div>
                                                                                                <div class="room">
                                                                                                    <i class="bi bi-geo-alt"></i>
                                                                                                    {{ $section['room'] }}
                                                                                                    @if($section['section_name'] !== 'N/A')
                                                                                                        (Sec: {{ $section['section_name'] }}
                                                                                                        )
                                                                                                    @endif
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @else
                                                                                <div class="free-card">
                                                                                    <i class="bi bi-check-circle"></i>
                                                                                    Free
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon">
                                    <i class="bi bi-calendar-x"></i>
                                </div>
                                <h5 class="empty-state-title">No timetable entries found</h5>
                                <p class="empty-state-text text-muted">Try adjusting your filters or contact
                                    administration</p>
                            </div>
                        @endif
                    </div>


                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize all collapsible sections
            $('.collapsible-header').on('click', function () {
                $(this).toggleClass('collapsed');
            });

            // Print functionality
            $('#printTimetable').on('click', function () {
                // Expand all sections before printing
                $('.collapsible-content').addClass('show');
                $('.collapsible-header').removeClass('collapsed');

                // Wait a moment for the DOM to update before printing
                setTimeout(function () {
                    window.print();
                }, 300);
            });

            // Export to Excel functionality - One sheet per program
            $('#exportExcel').on('click', function() {
                // Create a new workbook
                const wb = XLSX.utils.book_new();

                // Process each program group
                $('.program-group').each(function() {
                    const programName = $(this).find('.program-header').text().trim();
                    const ws_data = [];

                    // Process each class in the program
                    $(this).find('.class-group').each(function() {
                        const className = $(this).find('.class-header').text().trim();
                        const table = $(this).find('table')[0];

                        // Add class name as header row
                        ws_data.push([`Class: ${className}`]);
                        ws_data.push([]); // empty row

                        // Convert table to array of arrays
                        const tableData = XLSX.utils.table_to_sheet(table);
                        const jsonData = XLSX.utils.sheet_to_json(tableData, {header: 1});

                        // Add table data
                        ws_data.push(...jsonData);

                        // Add spacing between tables
                        ws_data.push([], [], []);
                    });

                    // Create worksheet from combined data
                    const ws = XLSX.utils.aoa_to_sheet(ws_data);

                    // Add the worksheet to the workbook
                    const sheetName = programName.substring(0, 31) // Excel sheet name limit
                        .replace(/[\\\/\?\*\[\]:]/g, ''); // Remove invalid chars
                    XLSX.utils.book_append_sheet(wb, ws, sheetName);
                });

                // Generate Excel file
                const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
                XLSX.writeFile(wb, `Timetable_${timestamp}.xlsx`);
            });


            // Loading state for filter button
            $('#timetableFilterForm').on('submit', function () {
                const button = $('#filterButton');
                button.prop('disabled', true);
                button.find('.spinner-border').removeClass('d-none');
                button.find('.button-text').text('Loading...');
            });

            // Reset filters
            $('#resetFilters').on('click', function () {
                $('#timetableFilterForm')[0].reset();
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const examDropdown = document.querySelector('[name="examination_session_id"]');
            const academicDropdown = document.getElementById('academic_session_id');
            const studyLevelDropdown = document.getElementById('study_level_id');
            const programDropdown = document.getElementById('program_id');
            const classDropdown = document.getElementById('program_class_id');

            // Initially disable dependent dropdowns
            studyLevelDropdown.disabled = true;
            programDropdown.disabled = true;
            classDropdown.disabled = true;

            // When Academic Session changes
            academicDropdown.addEventListener('change', function () {
                const academicSessionId = this.value;
                studyLevelDropdown.innerHTML = '<option value="">Loading...</option>';
                studyLevelDropdown.disabled = true;
                programDropdown.innerHTML = '<option value="">Select Study Level First</option>';
                programDropdown.disabled = true;
                classDropdown.innerHTML = '<option value="">Select Program First</option>';
                classDropdown.disabled = true;

                if (!academicSessionId) return;

                fetch(`/ajax-study-levels?academic_session_id=${academicSessionId}`)
                    .then(res => res.json())
                    .then(data => {
                        studyLevelDropdown.innerHTML = '<option value="">Select Study Level</option>';
                        data.forEach(level => {
                            studyLevelDropdown.innerHTML += `<option value="${level.id}">${level.name}</option>`;
                        });
                        studyLevelDropdown.disabled = false;

                        const oldStudyLevel = "{{ old('study_level_id') }}";
                        if (oldStudyLevel && data.some(l => l.id == oldStudyLevel)) {
                            studyLevelDropdown.value = oldStudyLevel;
                            studyLevelDropdown.dispatchEvent(new Event('change'));
                        }
                    });
            });

            // When Study Level changes
            studyLevelDropdown.addEventListener('change', function () {
                const studyLevelId = this.value;
                programDropdown.innerHTML = '<option value="">Loading...</option>';
                programDropdown.disabled = true;
                classDropdown.innerHTML = '<option value="">Select Program First</option>';
                classDropdown.disabled = true;

                if (!studyLevelId) return;

                fetch(`/ajax-programs?study_level_id=${studyLevelId}`)
                    .then(res => res.json())
                    .then(data => {
                        programDropdown.innerHTML = '<option value="">Select Program</option>';
                        data.forEach(program => {
                            programDropdown.innerHTML += `<option value="${program.id}">${program.name}</option>`;
                        });
                        programDropdown.disabled = false;

                        const oldProgram = "{{ old('program_id') }}";
                        if (oldProgram && data.some(p => p.id == oldProgram)) {
                            programDropdown.value = oldProgram;
                            programDropdown.dispatchEvent(new Event('change'));
                        }
                    });
            });

            // When Program changes
            programDropdown.addEventListener('change', function () {
                const programId = this.value;
                classDropdown.innerHTML = '<option value="">Loading...</option>';
                classDropdown.disabled = true;

                if (!programId) return;

                fetch(`/program-classes?program_id=${programId}`)
                    .then(res => res.json())
                    .then(data => {
                        classDropdown.innerHTML = '<option value="">Select Program Class</option>';
                        data.forEach(pc => {
                            classDropdown.innerHTML += `<option value="${pc.id}">${pc.name}</option>`;
                        });
                        classDropdown.disabled = false;

                        const oldClassId = "{{ old('program_class_id') }}";
                        if (oldClassId && data.some(c => c.id == oldClassId)) {
                            classDropdown.value = oldClassId;
                        }
                    });
            });

            // If Academic Session already selected (on form revalidation), trigger change
            if (academicDropdown.value) {
                academicDropdown.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection

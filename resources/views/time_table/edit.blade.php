@extends('layouts.app')

@section('title', 'Edit Timetable')

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

        /* Drag and Drop Styles */
        .dragging {
            opacity: 0.5;
            transform: scale(0.95);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .drop-highlight {
            background-color: rgba(59, 125, 221, 0.1) !important;
            border: 2px dashed #3b7ddd !important;
        }

        .drop-over {
            background-color: rgba(59, 125, 221, 0.2) !important;
            border: 2px solid #3b7ddd !important;
        }

        .drop-zone {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .drop-zone:hover {
            background-color: rgba(59, 125, 221, 0.1);
        }

        .timetable-entry {
            cursor: grab;
            transition: all 0.2s ease;
        }

        .timetable-entry:active {
            cursor: grabbing;
        }

        .timetable-cell {
            position: relative;
            min-height: 120px;
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
            <h1>Edit Timetable</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('timetable.index') }}">Timetable</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Complete Timetable Editor</h5>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Drag any entry to reschedule or click to edit details
                    </div>

                    <!-- Session Selector Form -->
                    <form method="POST" action="{{ route('timetable.edit') }}" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Examination Session</label>
                                <select name="examination_session_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Select Examination Session</option>
                                    @foreach($examinationSessions as $exam)
                                        <option
                                            value="{{ $exam->id }}" {{ $selectedExamSessionId == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Full Timetable Grid -->
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
                                                                        <td class="lecture-slot timetable-cell"
                                                                            data-day="{{ $day }}"
                                                                            data-slot="{{ $slotId }}"
                                                                            ondragover="allowDrop(event)"
                                                                            ondrop="dropEntry(event)">

                                                                            @if($timeSlot->is_break)
                                                                                <div class="break-card">
                                                                                    <i class="bi bi-cup-hot"></i> Break
                                                                                </div>
                                                                            @elseif(!empty($slotData['entries']))
                                                                                <div class="entries-container">
                                                                                    @foreach($slotData['entries'] as $entry)
                                                                                        @php
                                                                                            $timetableId = $entry['sections'][0]['timetable_id'] ?? 0;
                                                                                            $roomId = $entry['sections'][0]['room_id'] ?? 0;
                                                                                        @endphp
                                                                                        <div
                                                                                            class="course-card {{ $entry['type'] === 'lab' ? 'lab-card' : 'lecture-card' }} mb-2 timetable-entry"
                                                                                            draggable="true"
                                                                                            ondragstart="dragStart(event, {{ $timetableId }})"
                                                                                            onclick="showEditModal({{ $timetableId }})"
                                                                                            data-entry="{{ $timetableId }}"
                                                                                            data-room="{{ $roomId }}">
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
{{--                                                                                <div class="free-card drop-zone"--}}
{{--                                                                                     onclick="showCreateModal('{{ $day }}', '{{ $slotId }}')">--}}
{{--                                                                                    <i class="bi bi-plus"></i>--}}
{{--                                                                                    Free--}}
{{--                                                                                </div>--}}
                                                                                <div class="free-card drop-zone"
                                                                                    >
                                                                                    <i class="bi bi-plus"></i>
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

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Timetable Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body" id="editModalBody">
                            <!-- Form fields will be loaded here -->
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div class="modal fade" id="createModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createForm" method="POST" action="#">
                        @csrf
                        <div class="modal-body" id="createModalBody">
                            <!-- Form fields will be loaded here -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create Entry</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        // Drag and Drop functionality
        let draggedEntryId = null;
        let currentExaminationSession = "{{ $selectedExamSessionId }}";

        function dragStart(ev, entryId) {
            draggedEntryId = entryId;
            ev.dataTransfer.setData('text/plain', entryId);
            ev.currentTarget.classList.add('dragging');

            // Highlight all empty cells
            document.querySelectorAll('.timetable-cell').forEach(cell => {
                if (!cell.querySelector('.timetable-entry')) {
                    cell.classList.add('drop-highlight');
                }
            });
        }

        function allowDrop(ev) {
            ev.preventDefault();
            if (draggedEntryId) {
                ev.currentTarget.classList.add('drop-over');
            }
        }

        function dropEntry(ev) {
            ev.preventDefault();
            const cell = ev.currentTarget;
            cell.classList.remove('drop-over');

            // Remove all highlight classes
            document.querySelectorAll('.drop-highlight, .drop-over').forEach(el => {
                el.classList.remove('drop-highlight', 'drop-over');
            });

            if (!draggedEntryId) return;

            // Check if the slot is already occupied
            if (cell.querySelector('.timetable-entry')) {
                toastr.warning('This slot is already occupied. Please choose another slot.');
                return;
            }

            const day = cell.dataset.day;
            const slotId = cell.dataset.slot;

            // Show loading indicator
            const originalContent = cell.innerHTML;
            cell.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`/timetables/${draggedEntryId}/move`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    day_of_week: day,
                    time_slot_id: slotId
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw { status: response.status, data: errData };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        cell.innerHTML = originalContent;
                        toastr.error(data.message || 'Error moving the entry.');
                    }
                })
                .catch(error => {
                    cell.innerHTML = originalContent;
                    if (error.status === 422) {
                        const errors = error.data?.errors || {};
                        const errorMsg = Object.values(errors).flat().join('<br>');
                        toastr.error('Validation errors:<br>' + errorMsg);
                    } else {
                        toastr.error('An error occurred: ' + (error.message || 'Unknown error'));
                    }
                    console.error('Error details:', error);
                });

            draggedEntryId = null;
        }

        // Handle drag end to clean up styles
        document.addEventListener('dragend', () => {
            document.querySelectorAll('.dragging, .drop-highlight, .drop-over').forEach(el => {
                el.classList.remove('dragging', 'drop-highlight', 'drop-over');
            });
            draggedEntryId = null;
        });

        function showEditModal(entryId) {
            fetch(`/timetables/${entryId}/edit-modal`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('editModalBody').innerHTML = html;
                    document.getElementById('editForm').action = `/timetables/${entryId}`;
                    new bootstrap.Modal(document.getElementById('editModal')).show();

                    // 🔹 Initialize dropdowns and form submission inside the modal
                    initializeEditModal();
                })
                .catch(() => toastr.error('Failed to load edit form.'));
        }

        function showCreateModal(day, slotId) {
            fetch(`/timetables/create-modal?day=${day}&slot=${slotId}&examination_session_id=${currentExaminationSession}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('createModalBody').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('createModal')).show();
                })
                .catch(() => toastr.error('Failed to load create form.'));
        }

        // Handle form submissions for create modal
        document.getElementById('createForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        toastr.error(data.message || 'Error creating entry');
                    }
                })
                .catch(() => toastr.error('Network error while creating'));
        });

        // 🔹 Cascading Course Section on Course Change

        function initializeEditModal() {
            const courseSelect = document.getElementById('course_id');
            const sectionSelect = document.getElementById('course_section_id');
            const teacherSelect = document.getElementById('teacher_id');

            if (!courseSelect || !sectionSelect || !teacherSelect) return;

            // Cascade: load sections on course change
            courseSelect.addEventListener('change', function () {
                const selectedCourseId = this.value;

                sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
                sectionSelect.disabled = true;

                if (!selectedCourseId) {
                    sectionSelect.innerHTML = '<option value="">Select course first</option>';
                    sectionSelect.disabled = true;
                    return;
                }

                fetch(`/ajax-course-sections?course_id=${selectedCourseId}`)
                    .then(res => res.json())
                    .then(sections => {
                        let options = '<option value="">Select Section</option>';
                        sections.forEach(section => {
                            options += `<option value="${section.id}">${section.name}</option>`;
                        });
                        sectionSelect.innerHTML = options;
                        sectionSelect.disabled = false;
                    })
                    .catch(err => {
                        console.error(err);
                        sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
                        sectionSelect.disabled = true;
                    });
            });

            // Cascade: load teacher on section change
            sectionSelect.addEventListener('change', function () {
                const selectedSectionId = this.value;

                teacherSelect.innerHTML = '<option value="">Loading teacher...</option>';
                teacherSelect.disabled = true;

                if (!selectedSectionId) {
                    teacherSelect.innerHTML = '<option value="">Select section first</option>';
                    teacherSelect.disabled = true;
                    return;
                }

                fetch(`/section-teachers?course_section_id=${selectedSectionId}`)
                    .then(res => res.json())
                    .then(teacher => {
                        let options = '<option value="">Select Teacher</option>';
                        if (teacher && teacher.id && teacher.name) {
                            options += `<option value="${teacher.id}" selected>${teacher.name}</option>`;
                        } else {
                            options += '<option value="">No teacher assigned</option>';
                        }
                        teacherSelect.innerHTML = options;
                        teacherSelect.disabled = false;
                    })
                    .catch(err => {
                        console.error(err);
                        teacherSelect.innerHTML = '<option value="">Error loading teacher</option>';
                        teacherSelect.disabled = true;
                    });
            });
        }
    </script>
@endsection


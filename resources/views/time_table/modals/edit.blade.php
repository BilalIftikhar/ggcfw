<form id="editTimetableForm" action="{{ route('timetable.update', $timetable->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <!-- Display-only fields (disabled) -->
        <div class="col-md-6">
            <label class="form-label">Academic Session</label>
            <select class="form-select" disabled>
                <option>{{ $timetable->academicSession->name ?? 'N/A' }}</option>
            </select>
            <input type="hidden" name="academic_session_id" value="{{ $timetable->academic_session_id }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Examination Session</label>
            <select class="form-select" disabled>
                <option>{{ $timetable->examinationSession->title ?? 'N/A' }}</option>
            </select>
            <input type="hidden" name="examination_session_id" value="{{ $timetable->examination_session_id }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Study Level</label>
            <select class="form-select" disabled>
                <option>{{ $timetable->program->studyLevel->name ?? 'N/A' }}</option>
            </select>
            <input type="hidden" name="study_level_id" value="{{ $timetable->program->study_level_id ?? '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Program</label>
            <select class="form-select" disabled>
                <option>{{ $timetable->program->name ?? 'N/A' }}</option>
            </select>
            <input type="hidden" name="program_id" value="{{ $timetable->program_id }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Class</label>
            <select class="form-select" disabled>
                <option>{{ $timetable->programClass->name ?? 'N/A' }}</option>
            </select>
            <input type="hidden" name="program_class_id" value="{{ $timetable->program_class_id }}">
        </div>

        <!-- Editable Course Field -->
        <div class="col-md-4">
            <label class="form-label">Course</label>
            <select name="course_id" id="course_id" class="form-select" required>
                @foreach($courses as $course)
                    @if($course->program_id == $timetable->program_id)
                        <option value="{{ $course->id }}"
                                data-sections="{{ $course->sections->pluck('id')->join(',') }}"
                            {{ $timetable->course_id == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <!-- Editable Section Field -->
        <div class="col-md-4">
            <label class="form-label">Section</label>
            <select name="course_section_id" id="course_section_id" class="form-select" required>
                @foreach($courseSections as $section)
                    @if($section->course_id == $timetable->course_id)
                        <option value="{{ $section->id }}" {{ $timetable->course_section_id == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Teacher</label>
            <select name="teacher_id" id="teacher_id" class="form-select" required>
                @if($timetable->teacher)
                    <option value="{{ $timetable->teacher_id }}" selected>
                        {{ $timetable->teacher->name }}
                    </option>
                @else
                    <option value="" selected disabled>Select Teacher</option>
                @endif
            </select>
        </div>

        <!-- Editable Room Field -->
        <div class="col-md-4">
            <label class="form-label">Room</label>
            <select name="room_id" id="room_id" class="form-select" required>
                <option value="">Select Room</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ $timetable->room_id == $room->id ? 'selected' : '' }}>
                        {{ $room->room_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Day</label>
            <select class="form-select" disabled>
                <option>{{ ucfirst($timetable->day_of_week) }}</option>
            </select>
            <input type="hidden" name="day_of_week" value="{{ $timetable->day_of_week }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Time Slot</label>
            <select class="form-select" disabled>
                <option>
                    @foreach($timeSlots as $slot)
                        @if($slot->id == $timetable->time_slot_id)
                            {{ $slot->start_time }} - {{ $slot->end_time }}
                        @endif
                    @endforeach
                </option>
            </select>
            <input type="hidden" name="time_slot_id" value="{{ $timetable->time_slot_id }}">
        </div>

        <!-- Class Type (editable) -->
        <div class="col-md-4">
            <label class="form-label">Class Type</label>
            <div class="form-check form-switch mt-2">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="is_lab"
                    name="is_lab"
                    value="1"
                    {{ $timetable->is_lab ? 'checked' : '' }}
                >
                <label class="form-check-label" for="is_lab">
                    <span id="classTypeLabel">{{ $timetable->is_lab ? 'Lab Session' : 'Regular Session' }}</span>
                </label>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>



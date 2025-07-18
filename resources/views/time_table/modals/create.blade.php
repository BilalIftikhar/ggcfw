<form id="createTimetableForm" action="{{ route('timetables.store') }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Academic Session</label>
            <select name="academic_session_id" class="form-select">
                <option value="">Select Academic Session</option>
                @foreach($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ $academic_session_id == $session->id ? 'selected' : '' }}>
                        {{ $session->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Examination Session</label>
            <select name="examination_session_id" class="form-select" required>
                @foreach($examinationSessions as $exam)
                    <option value="{{ $exam->id }}">
                        {{ $exam->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Program</label>
            <select name="program_id" id="create_program_id" class="form-select" required>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}">
                        {{ $program->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Class</label>
            <select name="program_class_id" id="create_program_class_id" class="form-select" required>
                @foreach($programClasses as $class)
                    <option value="{{ $class->id }}">
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Course</label>
            <select name="course_id" id="create_course_id" class="form-select" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Section</label>
            <select name="course_section_id" id="create_course_section_id" class="form-select" required>
                @foreach($courseSections as $section)
                    <option value="{{ $section->id }}">
                        {{ $section->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Teacher</label>
            <select name="teacher_id" class="form-select" required>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Room</label>
            <select name="room_id" class="form-select" required>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">
                        {{ $room->room_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Day</label>
            <select name="day_of_week" class="form-select" required>
                <option value="{{ $day_of_week }}" selected>{{ ucfirst($day_of_week) }}</option>
                <option value="monday">Monday</option>
                <option value="tuesday">Tuesday</option>
                <option value="wednesday">Wednesday</option>
                <option value="thursday">Thursday</option>
                <option value="friday">Friday</option>
                <option value="saturday">Saturday</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Time Slot</label>
            <select name="time_slot_id" class="form-select" required>
                <option value="{{ $time_slot_id }}" selected>
                    {{ TimeSlot::find($time_slot_id)->start_time }} - {{ TimeSlot::find($time_slot_id)->end_time }}
                </option>
                @foreach($timeSlots as $slot)
                    @if($slot->id != $time_slot_id)
                        <option value="{{ $slot->id }}">
                            {{ $slot->start_time }} - {{ $slot->end_time }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Class Type</label>
            <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="is_lab" id="create_is_lab" value="1">
                <label class="form-check-label" for="create_is_lab">Lab Session</label>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Entry</button>
    </div>
</form>

<script>
    // Dynamic dropdown population for create modal
    document.getElementById('create_program_id').addEventListener('change', function() {
        const programId = this.value;
        fetch(`/api/courses?program_id=${programId}`)
            .then(response => response.json())
            .then(data => {
                const courseSelect = document.getElementById('create_course_id');
                courseSelect.innerHTML = '';
                data.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.name;
                    courseSelect.appendChild(option);
                });
            });
    });

    document.getElementById('create_course_id').addEventListener('change', function() {
        const courseId = this.value;
        fetch(`/api/course-sections?course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                const sectionSelect = document.getElementById('create_course_section_id');
                sectionSelect.innerHTML = '';
                data.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.name;
                    sectionSelect.appendChild(option);
                });
            });
    });
</script>

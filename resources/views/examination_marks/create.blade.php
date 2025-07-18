@extends('layouts.app')

@section('title', 'Create Examination Marks')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>{{ isset($existingMarks) && count($existingMarks) > 0 ? 'Edit' : 'Create' }} Examination Marks</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination-marks.index') }}">Examination Marks</a></li>
                    <li class="breadcrumb-item active">{{ isset($existingMarks) && count($existingMarks) > 0 ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Prepare Mark Sheet</h5>

                    {{-- Prepare Form --}}
                    <form id="markSheetForm" method="POST" action="{{ route('examination-marks.prepare') }}" class="row g-3">
                        @csrf

                        {{-- All dropdowns remain unchanged --}}
                        {{-- Academic Session --}}
                        <div class="col-md-3">
                            <label class="form-label">Academic Session</label>
                            <select class="form-select select2" name="academic_session_id" id="academic_session_id" required>
                                <option value="">-- Select Academic Session --</option>
                                @foreach($academicSessions as $session)
                                    <option value="{{ $session->id }}" {{ ($academic_session_id ?? old('academic_session_id')) == $session->id ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Examination Session --}}
                        <div class="col-md-3">
                            <label class="form-label">Examination Session</label>
                            <select class="form-select select2" name="examination_session_id" id="examination_session_id" required>
                                <option value="">-- Select Examination Session --</option>
                                @foreach($examinationSessions as $exam)
                                    <option value="{{ $exam->id }}" {{ ($examination_session_id ?? old('examination_session_id')) == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Examination Term --}}
                        <div class="col-md-3">
                            <label class="form-label">Examination Term</label>
                            <select class="form-select select2" name="examination_term_id" id="examination_term_id" required>
                                <option value="">-- Select Examination Term --</option>
                                @foreach($examinationTerms as $term)
                                    <option value="{{ $term->id }}" {{ ($examination_term_id ?? old('examination_term_id')) == $term->id ? 'selected' : '' }}>
                                        {{ $term->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Study Level --}}
                        <div class="col-md-3">
                            <label class="form-label">Study Level</label>
                            <select class="form-select select2" name="study_level_id" id="study_level_id" required>
                                <option value="">-- Select Study Level --</option>
                                @foreach($studyLevels as $level)
                                    <option value="{{ $level->id }}" {{ ($study_level_id ?? old('study_level_id')) == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Program --}}
                        <div class="col-md-3">
                            <label class="form-label">Program</label>
                            <select class="form-select select2" name="program_id" id="program_id" required>
                                <option value="">-- Select Program --</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ ($program_id ?? old('program_id')) == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Class --}}
                        <div class="col-md-3">
                            <label class="form-label">Class</label>
                            <select class="form-select select2" name="program_class_id" id="program_class_id" required>
                                <option value="">-- Select Class --</option>
                                @foreach($programClasses as $class)
                                    <option value="{{ $class->id }}" {{ ($program_class_id ?? old('program_class_id')) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Course --}}
                        <div class="col-md-3">
                            <label class="form-label">Course</label>
                            <select class="form-select select2" name="course_id" id="course_id" required>
                                <option value="">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ ($course_id ?? old('course_id')) == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Section --}}
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <select class="form-select select2" name="course_section_id" id="course_section_id">
                                <option value="">-- Select Section --</option>
                                @foreach($courseSections as $section)
                                    <option value="{{ $section->id }}" {{ ($course_section_id ?? old('course_section_id')) == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-list-check me-1"></i> Prepare Mark Sheet
                            </button>
                        </div>
                    </form>

                    {{-- Mark Sheet Table --}}
                    @if(isset($enrolledStudents) && $enrolledStudents->isNotEmpty())
                        <hr>
                        <h5 class="card-title mt-4">
                            Mark Sheet for {{ $selectedCourse->name ?? '' }}
                            @if(isset($course_section_id) && $course_section_id)
                                - Section: {{ $courseSections->firstWhere('id', $course_section_id)->name ?? '' }}
                            @endif
                        </h5>

                        <form method="POST" action="{{ route('examination-marks.store') }}">
                            @csrf
                            @foreach(request()->only(['academic_session_id','examination_session_id','examination_term_id','study_level_id','program_id','program_class_id','course_id','course_section_id']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            {{-- Global Apply for Total and Passing Marks --}}
                            <div class="row mb-4">
                                <div class="col-md-4 offset-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">Total Marks for All</span>
                                        <input type="number" step="0.01" class="form-control" id="globalTotalMarks" placeholder="Enter total marks">
                                        <button type="button" class="btn btn-primary" id="applyTotalMarks">Apply</button>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-2 mt-md-0">
                                    <div class="input-group">
                                        <span class="input-group-text">Passing Marks for All</span>
                                        <input type="number" step="0.01" class="form-control" id="globalPassingMarks" placeholder="Enter passing marks">
                                        <button type="button" class="btn btn-primary" id="applyPassingMarks">Apply</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Roll Number</th>
                                        <th>Marks Obtained</th>
                                        <th>Total Marks</th>
                                        <th>Passing Marks</th>
                                        @if(isset($selectedExaminationTerm) && $selectedExaminationTerm->enable_sessional)
                                            <th>Sessional Marks</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($enrolledStudents as $index => $student)
                                        @php $studentMark = $existingMarks[$student->enrollment->student_id] ?? null; @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $student->enrollment->student->name ?? '-' }}
                                                <input type="hidden" name="students[{{ $index }}][student_id]" value="{{ $student->enrollment->student_id }}">
                                                @if($studentMark)
                                                    <input type="hidden" name="students[{{ $index }}][mark_id]" value="{{ $studentMark->id }}">
                                                @endif
                                            </td>
                                            <td>{{ $student->enrollment->student->roll_number ?? '-' }}</td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control marks-obtained" name="students[{{ $index }}][marks_obtained]" value="{{ $studentMark->marks_obtained ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control total-marks" name="students[{{ $index }}][total_marks]" value="{{ $studentMark->total_marks ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control passing-marks" name="students[{{ $index }}][passing_marks]" value="{{ $studentMark->passing_marks ?? '' }}">
                                            </td>
                                            @if(isset($selectedExaminationTerm) && $selectedExaminationTerm->enable_sessional)
                                                <td>
                                                    <input type="number" step="0.01" class="form-control sessional-marks" name="students[{{ $index }}][sessional_marks]" value="{{ $studentMark->sessional_marks ?? '' }}">
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i> {{ isset($existingMarks) && count($existingMarks) > 0 ? 'Update' : 'Save' }} Marks
                                </button>
                            </div>
                        </form>
                    @elseif(isset($enrolledStudents))
                        <div class="alert alert-warning mt-4">No enrolled students found for the selected criteria.</div>
                    @endif

                </div>
            </div>
        </section>
    </main>
@endsection


@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize select2
            $('.select2').select2({ width: '100%' });

            // Disable all dependent dropdowns initially
            $('#study_level_id, #program_id, #program_class_id, #course_id, #course_section_id, #examination_term_id')
                .prop('disabled', true);

            function resetSelect(selector, placeholder) {
                $(selector).prop('disabled', true).html(`<option value="">${placeholder}</option>`);
            }

            // Academic Session change handler
            $('#academic_session_id').on('change', function () {
                let id = $(this).val();
                resetSelect('#study_level_id', 'Please select Academic Session first');
                resetSelect('#program_id', 'Please select Study Level first');
                resetSelect('#program_class_id', 'Please select Program first');
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');

                if (!id) return;
                $.get(`/ajax-study-levels?academic_session_id=${id}`, data => {
                    $('#study_level_id').prop('disabled', false).html('<option value="">Select Study Level</option>');
                    data.forEach(row => {
                        $('#study_level_id').append(`<option value="${row.id}">${row.name}</option>`);
                    });
                    $('#study_level_id').val('{{ $study_level_id ?? old('study_level_id') }}').trigger('change');
                });
            });

            // Study Level change handler
            $('#study_level_id').on('change', function () {
                let id = $(this).val();
                resetSelect('#program_id', 'Please select Study Level first');
                resetSelect('#program_class_id', 'Please select Program first');
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');

                if (!id) return;
                $.get(`/ajax-programs?study_level_id=${id}`, data => {
                    $('#program_id').prop('disabled', false).html('<option value="">Select Program</option>');
                    data.forEach(row => {
                        $('#program_id').append(`<option value="${row.id}">${row.name}</option>`);
                    });
                    $('#program_id').val('{{ $program_id ?? old('program_id') }}').trigger('change');
                });
            });

            // Program change handler
            $('#program_id').on('change', function () {
                let id = $(this).val();
                resetSelect('#program_class_id', 'Please select Program first');
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');

                if (!id) return;
                $.get(`/program-classes?program_id=${id}`, data => {
                    $('#program_class_id').prop('disabled', false).html('<option value="">Select Class</option>');
                    data.forEach(row => {
                        $('#program_class_id').append(`<option value="${row.id}">${row.name}</option>`);
                    });
                    $('#program_class_id').val('{{ $program_class_id ?? old('program_class_id') }}').trigger('change');
                });
            });

            // Program Class change handler
            $('#program_class_id').on('change', function () {
                let id = $(this).val();
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');

                if (!id) return;
                $.get(`/ajax-courses?program_class_id=${id}`, data => {
                    $('#course_id').prop('disabled', false).html('<option value="">Select Course</option>');
                    data.forEach(row => {
                        $('#course_id').append(`<option value="${row.id}">${row.name}</option>`);
                    });
                    $('#course_id').val('{{ $course_id ?? old('course_id') }}').trigger('change');
                });
            });

            // Course change handler
            $('#course_id').on('change', function () {
                let id = $(this).val();
                resetSelect('#course_section_id', 'Please select Course first');

                if (!id) return;
                $.get(`/ajax-course-sections?course_id=${id}`, data => {
                    $('#course_section_id').prop('disabled', false).html('<option value="">Select Section</option>');
                    data.forEach(row => {
                        $('#course_section_id').append(`<option value="${row.id}">${row.name}</option>`);
                    });
                    $('#course_section_id').val('{{ $course_section_id ?? old('course_section_id') }}');
                });
            });

            // Examination Session change handler
            $('#examination_session_id').on('change', function () {
                let id = $(this).val();
                resetSelect('#examination_term_id', 'Please select Examination Session first');

                if (!id) return;
                $.get(`/ajax-examination-terms?examination_session_id=${id}`, data => {
                    $('#examination_term_id').prop('disabled', false).html('<option value="">Select Examination Term</option>');
                    data.forEach(row => {
                        $('#examination_term_id').append(`<option value="${row.id}">${row.title}</option>`);
                    });
                    $('#examination_term_id').val('{{ $examination_term_id ?? old('examination_term_id') }}');
                });
            });

            // Apply global total marks to all students
            $('#applyTotalMarks').on('click', function() {
                const globalTotal = $('#globalTotalMarks').val();
                if (!globalTotal) {
                    alert('Please enter total marks first');
                    return;
                }

                $('.total-marks').each(function() {
                    $(this).val(globalTotal);
                });
            });


            // Apply global passing marks to all students
            $('#applyPassingMarks').on('click', function() {
                const globalPassing = $('#globalPassingMarks').val();
                if (!globalPassing) {
                    alert('Please enter passing marks first');
                    return;
                }

                $('.passing-marks').each(function() {
                    $(this).val(globalPassing);
                });
            });

            $('form').on('submit', function (e) {
                let isValid = true;
                $('.marks-obtained').each(function (index) {
                    const obtained = parseFloat($(this).val()) || 0;
                    const total = parseFloat($('.total-marks').eq(index).val()) || 0;

                    if (obtained > total) {
                        isValid = false;
                        alert(`Obtained marks (${obtained}) cannot be greater than total marks (${total}) for student #${index + 1}.`);
                        $(this).focus();
                        return false; // break each loop
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Initialize form with existing values
            @if(isset($academic_session_id) || old('academic_session_id'))
            $('#academic_session_id').trigger('change');
            @endif
            @if(isset($examination_session_id) || old('examination_session_id'))
            $('#examination_session_id').trigger('change');
            @endif
        });
    </script>
@endsection

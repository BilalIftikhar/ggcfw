@extends('layouts.app')

@section('title', 'Edit Assignment')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Edit Assignment</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Assignments</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i> <strong>Important:</strong> To change any section (Course, Program, etc.),
                you must first reselect the Academic Session. Changing the Academic Session will reset all dependent fields.
            </div>
            <form method="POST" action="{{ route('assignments.update', $assignment) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Assignment Details</h5>
                        <div class="row">
                            <!-- Academic Session -->
                            <div class="col-md-3">
                                <label>Academic Session</label>
                                <select id="academic_session_id" class="form-select" required>
                                    <option value="">Select Academic Session</option>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}"
                                                @if($session->id == $assignment->program->academicSession->id) selected @endif>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Study Level -->
                            <div class="col-md-3">
                                <label>Study Level</label>
                                <select id="study_level_id" class="form-select" required>
                                    <option value="{{ $assignment->program->studyLevel->id }}" selected>
                                        {{ $assignment->program->studyLevel->name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Program -->
                            <div class="col-md-3">
                                <label>Program</label>
                                <select name="program_id" id="program_id" class="form-select" required>
                                    <option value="{{ $assignment->program_id }}" selected>
                                        {{ $assignment->program->name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Program Class -->
                            <div class="col-md-3">
                                <label>Program Class</label>
                                <select id="program_class_id" name="program_class_id" class="form-select" required>
                                    <option value="{{ $assignment->course->class->id }}" selected>
                                        {{ $assignment->course->class->name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Course -->
                            <div class="col-md-3">
                                <label>Course</label>
                                <select name="course_id" id="course_id" class="form-select" required>
                                    <option value="{{ $assignment->course_id }}" selected>
                                        {{ $assignment->course->name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Section -->
                            <div class="col-md-3">
                                <label>Course Section</label>
                                <select name="course_section_id" id="course_section_id" class="form-select" required>
                                    <option value="{{ $assignment->course_section_id }}" selected>
                                        {{ $assignment->section->name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Teacher -->
                            <div class="col-md-3">
                                <label>Teacher</label>
                                <select name="teacher_id" id="teacher_id" class="form-select" required>
                                    <option value="{{ $assignment->teacher_id }}" selected>
                                        {{ $assignment->teacher->name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-3">
                                <label>Due Date</label>
                                <input type="datetime-local" name="due_date" class="form-control"
                                       value="{{ \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control"
                                       value="{{ old('title', $assignment->title) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>Attachment</label>
                                <input type="file" name="attachment" class="form-control"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                @if($assignment->getFirstMedia('attachment'))
                                    <div class="mt-2">
                                        <small class="text-muted">Current file: </small>
                                        <a href="{{ $assignment->getFirstMediaUrl('attachment') }}" target="_blank">
                                            {{ $assignment->getFirstMedia('attachment')->name }}
                                        </a>
                                        <small class="text-muted d-block">
                                            ({{ strtoupper($assignment->getFirstMedia('attachment')->mime_type) }} •
                                            {{ round($assignment->getFirstMedia('attachment')->size / 1024) }} KB)
                                        </small>
                                    </div>
                                @endif
                                <small class="form-text text-muted">
                                    Allowed file types: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Max size: 10MB.
                                </small>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <label>Description</label>
                                <textarea name="description" rows="3" class="form-control">{{ old('description', $assignment->description) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Assignment</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Store initial values
            const initialValues = {
                academicSession: $('#academic_session_id').val(),
                studyLevel: $('#study_level_id').val(),
                program: $('#program_id').val(),
                programClass: $('#program_class_id').val(),
                course: $('#course_id').val(),
                section: $('#course_section_id').val(),
                teacher: $('#teacher_id').val()
            };

            // Enable all dropdowns for editing
            $('#study_level_id, #program_id, #program_class_id, #course_id, #course_section_id, #teacher_id')
                .prop('disabled', false);

            // Function to reset specific dependent dropdowns
            function resetDependentFields(fromField) {
                const resetMap = {
                    'academic_session_id': ['study_level_id', 'program_id', 'program_class_id', 'course_id', 'course_section_id', 'teacher_id'],
                    'study_level_id': ['program_id', 'program_class_id', 'course_id', 'course_section_id', 'teacher_id'],
                    'program_id': ['program_class_id', 'course_id', 'course_section_id', 'teacher_id'],
                    'program_class_id': ['course_id', 'course_section_id', 'teacher_id'],
                    'course_id': ['course_section_id', 'teacher_id'],
                    'course_section_id': ['teacher_id']
                };

                if (resetMap[fromField]) {
                    resetMap[fromField].forEach(field => {
                        $(`#${field}`).prop('disabled', true).empty().append(`<option value="">Select ${field.replace('_id', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</option>`);
                    });
                }
            }

            // Academic Session change handler
            $('#academic_session_id').on('change', function() {
                const newSessionId = $(this).val();
                if (newSessionId) {
                    resetDependentFields('academic_session_id');
                    loadStudyLevels(newSessionId);
                } else {
                    resetDependentFields('academic_session_id');
                }
            });

            // Function to load study levels
            function loadStudyLevels(sessionId) {
                $('#study_level_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                $.get(`/ajax-study-levels?academic_session_id=${sessionId}`, function(data) {
                    $('#study_level_id').prop('disabled', false).empty().append('<option value="">Select Study Level</option>');
                    $.each(data, function(i, level) {
                        const selected = level.id == initialValues.studyLevel ? 'selected' : '';
                        $('#study_level_id').append(`<option value="${level.id}" ${selected}>${level.name}</option>`);
                    });
                    $('#study_level_id').trigger('change');
                });
            }

            // Study Level → Program
            $('#study_level_id').on('change', function() {
                let levelId = $(this).val();
                if (levelId) {
                    resetDependentFields('study_level_id');
                    loadPrograms(levelId);
                } else {
                    resetDependentFields('study_level_id');
                }
            });

            function loadPrograms(levelId) {
                $('#program_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                $.get(`/ajax-programs?study_level_id=${levelId}`, function(data) {
                    $('#program_id').prop('disabled', false).empty().append('<option value="">Select Program</option>');
                    $.each(data, function(i, program) {
                        const selected = program.id == initialValues.program ? 'selected' : '';
                        $('#program_id').append(`<option value="${program.id}" ${selected}>${program.name}</option>`);
                    });
                    $('#program_id').trigger('change');
                });
            }

            // Program → Program Class
            $('#program_id').on('change', function() {
                let programId = $(this).val();
                if (programId) {
                    resetDependentFields('program_id');
                    loadProgramClasses(programId);
                } else {
                    resetDependentFields('program_id');
                }
            });

            function loadProgramClasses(programId) {
                $('#program_class_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');
                console.log(initialValues.programClass);
                $.get(`/program-classes?program_id=${programId}`, function(data) {
                    $('#program_class_id').prop('disabled', false).empty().append('<option value="">Select Class</option>');
                    $.each(data, function(i, cls) {
                        const selected = cls.id == initialValues.programClass ? 'selected' : '';
                        $('#program_class_id').append(`<option value="${cls.id}" ${selected}>${cls.name}</option>`);
                    });
                    $('#program_class_id').trigger('change');
                });
            }

            // Program Class → Courses
            $('#program_class_id').on('change', function() {
                let classId = $(this).val();
                if (classId) {
                    resetDependentFields('program_class_id');
                    loadCourses(classId);
                } else {
                    resetDependentFields('program_class_id');
                }
            });

            function loadCourses(classId) {
                $('#course_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                $.get(`/ajax-courses?program_class_id=${classId}`, function(data) {
                    $('#course_id').prop('disabled', false).empty().append('<option value="">Select Course</option>');
                    $.each(data, function(i, course) {
                        const selected = course.id == initialValues.course ? 'selected' : '';
                        $('#course_id').append(`<option value="${course.id}" ${selected}>${course.name}</option>`);
                    });
                    $('#course_id').trigger('change');
                });
            }

            // Course → Sections
            $('#course_id').on('change', function() {
                let courseId = $(this).val();
                if (courseId) {
                    resetDependentFields('course_id');
                    loadCourseSections(courseId);
                } else {
                    resetDependentFields('course_id');
                }
            });

            function loadCourseSections(courseId) {
                $('#course_section_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                $.get(`/ajax-course-sections?course_id=${courseId}`, function(data) {
                    $('#course_section_id').prop('disabled', false).empty().append('<option value="">Select Section</option>');
                    $.each(data, function(i, sec) {
                        const selected = sec.id == initialValues.section ? 'selected' : '';
                        $('#course_section_id').append(`<option value="${sec.id}" ${selected}>${sec.name}</option>`);
                    });
                    $('#course_section_id').trigger('change');
                });
            }

            // Section → Teacher
            $('#course_section_id').on('change', function() {
                let sectionId = $(this).val();
                if (sectionId) {
                    resetDependentFields('course_section_id');
                    loadSectionTeachers(sectionId);
                } else {
                    resetDependentFields('course_section_id');
                }
            });

            function loadSectionTeachers(sectionId) {
                $('#teacher_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                $.get(`/section-teachers?course_section_id=${sectionId}`, function(data) {
                    $('#teacher_id').prop('disabled', false).empty().append('<option value="">Select Teacher</option>');
                    if (data && data.id && data.name) {
                        const selected = data.id == initialValues.teacher ? 'selected' : '';
                        $('#teacher_id').append(`<option value="${data.id}" ${selected}>${data.name}</option>`);
                    }
                });
            }

            // Initialize all dropdowns based on initial values
            function initializeDropdowns() {
                if (initialValues.academicSession) {
                    loadStudyLevels(initialValues.academicSession);
                }
            }

            initializeDropdowns();
        });
    </script>
@endsection

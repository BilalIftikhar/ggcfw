@extends('layouts.app')

@section('title', 'Create Assignment')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Create Assignment</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Assignments</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <form method="POST" action="{{ route('assignments.store') }}" enctype="multipart/form-data">
                @csrf
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
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Study Level -->
                            <div class="col-md-3">
                                <label>Study Level</label>
                                <select id="study_level_id" class="form-select" disabled required></select>
                            </div>

                            <!-- Program -->
                            <div class="col-md-3">
                                <label>Program</label>
                                <select name="program_id" id="program_id" class="form-select" disabled required></select>
                            </div>

                            <!-- Program Class -->
                            <div class="col-md-3">
                                <label>Program Class</label>
                                <select id="program_class_id" name="program_class_id" class="form-select" disabled required></select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Course -->
                            <div class="col-md-3">
                                <label>Course</label>
                                <select name="course_id" id="course_id" class="form-select" disabled required></select>
                            </div>

                            <!-- Section -->
                            <div class="col-md-3">
                                <label>Course Section</label>
                                <select name="course_section_id" id="course_section_id" class="form-select" disabled required></select>
                            </div>

                            <!-- Teacher -->
                            <div class="col-md-3">
                                <label>Teacher</label>
                                <select name="teacher_id" id="teacher_id" class="form-select" disabled required></select>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-3">
                                <label>Due Date</label>
                                <input type="date" name="due_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label>Attachment</label>
                                <input type="file" name="attachment" class="form-control"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg">
                                <small class="form-text text-muted">
                                    Allowed file types: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG. Max size: 10MB.
                                </small>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <label>Description</label>
                                <textarea name="description" rows="3" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Save Assignment</button>
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

            $('#study_level_id').prop('disabled', true).empty().append('<option value="">Please select Academic Session first</option>');
            $('#program_id').prop('disabled', true).empty().append('<option value="">Please select Study Level first</option>');
            $('#program_class_id').prop('disabled', true).empty().append('<option value="">Please select Program first</option>');
            $('#course_id').prop('disabled', true).empty().append('<option value="">Please select Program Class first</option>');
            $('#course_section_id').prop('disabled', true).empty().append('<option value="">Please select Course first</option>');
            $('#teacher_id').prop('disabled', true).empty().append('<option value="">Please select Course Section first</option>');
            // Academic Session → Study Level
            $('#academic_session_id').on('change', function () {
                let sessionId = $(this).val();
                $('#study_level_id').prop('disabled', true).empty().append('<option value="">Please select Academic Session first</option>');
                $('#program_id').prop('disabled', true).empty().append('<option value="">Please select Study Level first</option>');
                $('#program_class_id').prop('disabled', true).empty().append('<option value="">Please select Program first</option>');
                $('#course_id').prop('disabled', true).empty().append('<option value="">Please select Program Class first</option>');
                $('#course_section_id').prop('disabled', true).empty().append('<option value="">Please select Course first</option>');
                $('#teacher_id').prop('disabled', true).empty().append('<option value="">Please select Course Section first</option>');

                if (!sessionId) return;

                $.get(`/ajax-study-levels?academic_session_id=${sessionId}`, function (data) {
                    $('#study_level_id').prop('disabled', false).empty().append('<option value="">Select Study Level</option>');
                    $.each(data, function (i, level) {
                        $('#study_level_id').append(`<option value="${level.id}">${level.name}</option>`);
                    });
                });
            });

            // Study Level → Program
            $('#study_level_id').on('change', function () {
                let levelId = $(this).val();
                $('#program_id').prop('disabled', true).empty().append('<option value="">Please select Study Level first</option>');
                $('#program_class_id').prop('disabled', true).empty().append('<option value="">Please select Program first</option>');
                $('#course_id').prop('disabled', true).empty().append('<option value="">Please select Program Class first</option>');
                $('#course_section_id').prop('disabled', true).empty().append('<option value="">Please select Course first</option>');
                $('#teacher_id').prop('disabled', true).empty().append('<option value="">Please select Course Section first</option>');

                if (!levelId) return;

                $.get(`/ajax-programs?study_level_id=${levelId}`, function (data) {
                    $('#program_id').prop('disabled', false).empty().append('<option value="">Select Program</option>');
                    $.each(data, function (i, program) {
                        $('#program_id').append(`<option value="${program.id}">${program.name}</option>`);
                    });
                });
            });

            // Program → Program Class
            $('#program_id').on('change', function () {
                let programId = $(this).val();
                $('#program_class_id').prop('disabled', true).empty().append('<option value="">Please select Program first</option>');
                $('#course_id').prop('disabled', true).empty().append('<option value="">Please select Program Class first</option>');
                $('#course_section_id').prop('disabled', true).empty().append('<option value="">Please select Course first</option>');
                $('#teacher_id').prop('disabled', true).empty().append('<option value="">Please select Course Section first</option>');

                if (!programId) return;

                $.get(`/program-classes?program_id=${programId}`, function (data) {
                    $('#program_class_id').prop('disabled', false).empty().append('<option value="">Select Class</option>');
                    $.each(data, function (i, cls) {
                        $('#program_class_id').append(`<option value="${cls.id}">${cls.name}</option>`);
                    });
                });
            });

            // Program Class → Courses
            $('#program_class_id').on('change', function () {
                let classId = $(this).val();
                $('#course_id').prop('disabled', true).empty().append('<option value="">Please select Program Class first</option>');
                $('#course_section_id').prop('disabled', true).empty().append('<option value="">Please select Course first</option>');
                $('#teacher_id').prop('disabled', true).empty().append('<option value="">Please select Course Section first</option>');

                if (!classId) return;

                $.get(`/ajax-courses?program_class_id=${classId}`, function (data) {
                    $('#course_id').prop('disabled', false).empty().append('<option value="">Select Course</option>');
                    $.each(data, function (i, course) {
                        $('#course_id').append(`<option value="${course.id}">${course.name}</option>`);
                    });
                });
            });

            // Course → Sections
            $('#course_id').on('change', function () {
                let courseId = $(this).val();
                $('#course_section_id').prop('disabled', true).empty().append('<option value="">Please select Course first</option>');
                $('#teacher_id').prop('disabled', true).empty().append('<option value="">Please select Course Section first</option>');

                if (!courseId) return;

                $.get(`/ajax-course-sections?course_id=${courseId}`, function (data) {
                    $('#course_section_id').prop('disabled', false).empty().append('<option value="">Select Section</option>');
                    $.each(data, function (i, sec) {
                        $('#course_section_id').append(`<option value="${sec.id}">${sec.name}</option>`);
                    });
                });
            });

            // Section → Teacher
            $('#course_section_id').on('change', function () {
                let sectionId = $(this).val();
                $('#teacher_id').prop('disabled', true).empty().append('<option value="">Please select Course Section first</option>');

                if (!sectionId) return;

                $.get(`/section-teachers?course_section_id=${sectionId}`, function (data) {
                    $('#teacher_id').empty().append('<option value="">Select Teacher</option>');
                    if (data && data.id && data.name) {
                        $('#teacher_id').append(`<option value="${data.id}" selected>${data.name}</option>`);
                    }
                    $('#teacher_id').prop('disabled', false);
                });
            });
        });

    </script>
@endsection

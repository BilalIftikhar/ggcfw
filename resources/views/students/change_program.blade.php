@extends('layouts.app')

@section('title', 'Change Program')

@section('content')
    <style>
        .form-label {
            font-weight: 600;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Change Program</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Change Program</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="row">
                <!-- Current Enrollment Info -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header fw-bold">Current Enrollment</div>
                        <div class="card-body">
                            <p><strong>Academic Session:</strong> {{ $enrollment->academicSession->name ?? 'N/A' }}</p>
                            <p><strong>Study Level:</strong> {{ $enrollment->program->studyLevel->name ?? 'N/A' }}</p>
                            <p><strong>Program:</strong> {{ $enrollment->program->name ?? 'N/A' }}</p>
                            <p><strong>Class:</strong> {{ $enrollment->programClass->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Program Change Form -->
                <div class="col-md-6 mb-4">
                    <form action="{{ route('students.changeProgram.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header fw-bold">Change Program</div>
                            <div class="card-body">

                                <!-- Examination Session -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Examination Session</label>
                                    <select name="examination_session_id" class="form-select @error('examination_session_id') is-invalid @enderror">
                                        <option value="">Select Examination Session</option>
                                        @foreach($examinationSessions as $examSession)
                                            <option value="{{ $examSession->id }}" {{ old('examination_session_id') == $examSession->id ? 'selected' : '' }}>
                                                {{ $examSession->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('examination_session_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Academic Session -->
                                <div class="mb-3">
                                    <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                    <select name="academic_session_id" id="academic_session_id" class="form-select" required>
                                        <option value="">Select Session</option>
                                        @foreach($sessions as $session)
                                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Study Level -->
                                <div class="mb-3">
                                    <label class="form-label">Study Level <span class="text-danger">*</span></label>
                                    <select name="study_level_id" id="study_level_id" class="form-select" required disabled>
                                        <option value="">Select Academic Session First</option>
                                    </select>
                                </div>

                                <!-- Program -->
                                <div class="mb-3">
                                    <label class="form-label">Program <span class="text-danger">*</span></label>
                                    <select name="program_id" id="program_id" class="form-select" required disabled>
                                        <option value="">Select Study Level First</option>
                                    </select>
                                </div>

                                <!-- Program Class -->
                                <div class="mb-3">
                                    <label class="form-label">Program Class <span class="text-danger">*</span></label>
                                    <select name="program_class_id" id="program_class_id" class="form-select" required disabled>
                                        <option value="">Select Program First</option>
                                    </select>
                                </div>

                                <!-- Courses -->
                                <div id="coursesContainer" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label text-primary">Mandatory Courses</label>
                                        <div id="mandatoryCoursesList" class="d-flex flex-wrap gap-3"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-success">Optional Courses</label>
                                        <div id="optionalCoursesList" class="d-flex flex-wrap gap-3"></div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Confirm Program Change
                                    </button>
                                    <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const academicSessionDropdown = document.getElementById('academic_session_id');
            const studyLevelDropdown = document.getElementById('study_level_id');
            const programDropdown = document.getElementById('program_id');
            const programClassDropdown = document.getElementById('program_class_id');
            const mandatoryCoursesList = document.getElementById('mandatoryCoursesList');
            const optionalCoursesList = document.getElementById('optionalCoursesList');
            const coursesContainer = document.getElementById('coursesContainer');

            academicSessionDropdown.addEventListener('change', function () {
                const sessionId = this.value;
                studyLevelDropdown.innerHTML = '<option value="">Loading...</option>';
                studyLevelDropdown.disabled = true;
                programDropdown.innerHTML = '<option value="">Select Study Level First</option>';
                programDropdown.disabled = true;
                programClassDropdown.innerHTML = '<option value="">Select Program First</option>';
                programClassDropdown.disabled = true;
                coursesContainer.style.display = 'none';

                if (sessionId) {
                    fetch(`/ajax-study-levels?academic_session_id=${sessionId}`)
                        .then(res => res.json())
                        .then(data => {
                            studyLevelDropdown.innerHTML = '<option value="">Select Study Level</option>';
                            data.forEach(level => {
                                studyLevelDropdown.innerHTML += `<option value="${level.id}">${level.name}</option>`;
                            });
                            studyLevelDropdown.disabled = false;
                        });
                }
            });

            studyLevelDropdown.addEventListener('change', function () {
                const levelId = this.value;
                programDropdown.innerHTML = '<option value="">Loading...</option>';
                programDropdown.disabled = true;
                programClassDropdown.innerHTML = '<option value="">Select Program First</option>';
                programClassDropdown.disabled = true;
                coursesContainer.style.display = 'none';

                if (levelId) {
                    fetch(`/ajax-programs?study_level_id=${levelId}`)
                        .then(res => res.json())
                        .then(data => {
                            programDropdown.innerHTML = '<option value="">Select Program</option>';
                            data.forEach(program => {
                                programDropdown.innerHTML += `<option value="${program.id}">${program.name}</option>`;
                            });
                            programDropdown.disabled = false;
                        });
                }
            });

            programDropdown.addEventListener('change', function () {
                const programId = this.value;
                programClassDropdown.innerHTML = '<option value="">Loading...</option>';
                programClassDropdown.disabled = true;
                coursesContainer.style.display = 'none';
                mandatoryCoursesList.innerHTML = '';
                optionalCoursesList.innerHTML = '';

                if (!programId) return;

                fetch(`/program-classes?program_id=${programId}`)
                    .then(res => res.json())
                    .then(data => {
                        programClassDropdown.innerHTML = '<option value="">Select Program Class</option>';

                        // Filter classes that contain "first" or "1st" (case-insensitive)
                        const filteredClasses = data.filter(cls =>
                            cls.name.toLowerCase().includes('first') ||
                            cls.name.includes('1st')
                        );

                        filteredClasses.forEach(cls => {
                            programClassDropdown.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                        });
                        programClassDropdown.disabled = false;
                    });
            });

            programClassDropdown.addEventListener('change', function () {
                const programId = programDropdown.value;
                const classId = this.value;
                mandatoryCoursesList.innerHTML = '';
                optionalCoursesList.innerHTML = '';
                coursesContainer.style.display = 'none';

                if (!programId || !classId) return;

                fetch(`/ajax-courses?program_id=${programId}&program_class_id=${classId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length) {
                            coursesContainer.style.display = 'block';
                            data.forEach(course => {
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.name = 'courses[]';
                                checkbox.value = course.id;
                                checkbox.id = `course_${course.id}`;

                                const label = document.createElement('label');
                                label.htmlFor = checkbox.id;
                                label.classList.add('me-3', 'form-check-label');
                                label.style.display = 'flex';
                                label.style.alignItems = 'center';
                                label.style.gap = '5px';
                                label.appendChild(checkbox);
                                label.appendChild(document.createTextNode(course.name));

                                if (course.is_mandatory) {
                                    checkbox.checked = true;
                                    checkbox.disabled = true;
                                    mandatoryCoursesList.appendChild(label);
                                } else {
                                    optionalCoursesList.appendChild(label);
                                }
                            });
                        }
                    });
            });
        });
    </script>
@endsection

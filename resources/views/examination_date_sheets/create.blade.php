@extends('layouts.app')

@section('title', 'Create Examination Date Sheet')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Create Examination Date Sheet</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination-date-sheet.index') }}">Date Sheet</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body pt-4">
                    <form id="filterForm" method="POST" action="{{ route('examination-date-sheet.create') }}">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Academic Session</label>
                                <select class="form-select select2" name="academic_session_id" id="academic_session_id" required>
                                    <option value="">-- Select Academic Session --</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id', request('academic_session_id')) == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Examination Session</label>
                                <select class="form-select select2" name="examination_session_id" id="examination_session_id" required>
                                    <option value="">-- Select Examination Session --</option>
                                    @foreach($examSessions as $exam)
                                        <option value="{{ $exam->id }}" {{ old('examination_session_id', request('examination_session_id')) == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Examination Term</label>
                                <select class="form-select select2" name="examination_term_id" id="examination_term_id" required {{ request('examination_session_id') ? '' : 'disabled' }}>
                                    <option value="">-- Select Examination Term --</option>
                                    @if(request('examination_session_id') && isset($examinationTerms))
                                        @foreach($examinationTerms as $term)
                                            <option value="{{ $term->id }}" {{ old('examination_term_id', request('examination_term_id')) == $term->id ? 'selected' : '' }}>{{ $term->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Study Level</label>
                                <select class="form-select select2" name="study_level_id" id="study_level_id" required {{ request('academic_session_id') ? '' : 'disabled' }}>
                                    <option value="">-- Select Study Level --</option>
                                    @foreach($studyLevels ?? [] as $level)
                                        <option value="{{ $level->id }}" {{ old('study_level_id', request('study_level_id')) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Program</label>
                                <select class="form-select select2" name="program_id" id="program_id" required {{ request('study_level_id') ? '' : 'disabled' }}>
                                    <option value="">-- Select Program --</option>
                                    @foreach($programs ?? [] as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id', request('program_id')) == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Class</label>
                                <select class="form-select select2" name="program_class_id" id="program_class_id" required {{ request('program_id') ? '' : 'disabled' }}>
                                    <option value="">-- Select Class --</option>
                                    @foreach($programClasses ?? [] as $class)
                                        <option value="{{ $class->id }}" {{ old('program_class_id', request('program_class_id')) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 align-self-end">
                                <button type="submit" class="btn btn-primary w-100" id="prepareBtn">
                                    <i class="bi bi-gear-fill me-1"></i> Prepare Date Sheet
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(isset($courses) && $courses->count() > 0)
                        <form id="dateSheetForm" method="POST" action="{{ route('examination-date-sheet.store') }}">
                            @csrf
                            <input type="hidden" name="academic_session_id" value="{{ $academicSessionId }}">
                            <input type="hidden" name="examination_session_id" value="{{ $examinationSessionId }}">
                            <input type="hidden" name="examination_term_id" value="{{ $examinationTermId }}">
                            <input type="hidden" name="study_level_id" value="{{ $studyLevelId }}">
                            <input type="hidden" name="program_id" value="{{ $programId }}">
                            <input type="hidden" name="program_class_id" value="{{ $classId }}">

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Course</th>
                                        <th>Section</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Room</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($courses as $course)
                                        @foreach($course->sections as $section)
                                            @php
                                                $key = $course->id . '_' . $section->id;
                                                $existing = $existingSheets[$key] ?? null;
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $course->name }}
                                                    <input type="hidden" name="course_id[]" value="{{ $course->id }}">
                                                    <input type="hidden" name="existing_id[]" value="{{ $existing?->id }}">
                                                </td>
                                                <td>
                                                    {{ $section->name }}
                                                    <input type="hidden" name="course_section_id[]" value="{{ $section->id }}">
                                                </td>
                                                <td><input type="date" name="exam_date[]" class="form-control" value="{{ $existing?->exam_date }}"></td>
                                                <td><input type="time" name="start_time[]" class="form-control" value="{{ $existing?->start_time ? \Carbon\Carbon::parse($existing->start_time)->format('H:i') : '' }}"></td>
                                                <td><input type="time" name="end_time[]" class="form-control" value="{{ $existing?->end_time ? \Carbon\Carbon::parse($existing->end_time)->format('H:i') : '' }}"></td>
                                                <td>
                                                    <select name="room_id[]" class="form-select">
                                                        <option value="">-- Select Room --</option>
                                                        @foreach($rooms as $room)
                                                            <option value="{{ $room->id }}" {{ $existing?->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-save-fill me-1"></i> Save Date Sheet
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });

            $('#academic_session_id').on('change', function () {
                let sessionId = $(this).val();
                $('#study_level_id, #program_id, #program_class_id').prop('disabled', true).empty().append('<option value="">-- Select --</option>');
                if (!sessionId) return;

                $.get(`/ajax-study-levels?academic_session_id=${sessionId}`, function (data) {
                    $('#study_level_id').prop('disabled', false);
                    data.forEach(level => $('#study_level_id').append(`<option value="${level.id}">${level.name}</option>`));
                });
            });

            $('#study_level_id').on('change', function () {
                let levelId = $(this).val();
                $('#program_id, #program_class_id').prop('disabled', true).empty().append('<option value="">-- Select --</option>');
                if (!levelId) return;

                $.get(`/ajax-programs?study_level_id=${levelId}`, function (data) {
                    $('#program_id').prop('disabled', false);
                    data.forEach(p => $('#program_id').append(`<option value="${p.id}">${p.name}</option>`));
                });
            });

            $('#program_id').on('change', function () {
                let programId = $(this).val();
                $('#program_class_id').prop('disabled', true).empty().append('<option value="">-- Select --</option>');
                if (!programId) return;

                $.get(`/program-classes?program_id=${programId}`, function (data) {
                    $('#program_class_id').prop('disabled', false);
                    data.forEach(cls => $('#program_class_id').append(`<option value="${cls.id}">${cls.name}</option>`));
                });
            });

            $('#examination_session_id').on('change', function () {
                let sessionId = $(this).val();
                $('#examination_term_id').prop('disabled', true).empty().append('<option value="">-- Select Examination Term --</option>');
                if (!sessionId) return;

                $.get(`/ajax-examination-terms?examination_session_id=${sessionId}`, function (data) {
                    $('#examination_term_id').prop('disabled', false);
                    data.forEach(term => $('#examination_term_id').append(`<option value="${term.id}">${term.title}</option>`));
                });
            });
        });
    </script>
@endsection

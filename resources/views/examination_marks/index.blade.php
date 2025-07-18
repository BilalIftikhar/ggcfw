@extends('layouts.app')

@section('title', 'Examination Marks')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Examination Marks</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Marks</li>
                </ol>
            </nav>
        </div>

        @php
            $user = auth()->user();
            $isAdmin = $user->roles()->where('is_admin', true)->exists();
        @endphp

        <section class="section">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Filter Marks</h5>
                </div>
                <div class="card-body pt-3">
                    <form method="GET" action="{{ route('examination-marks.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Academic Session</label>
                            <select class="form-select select2" name="academic_session_id" id="academic_session_id" required>
                                <option value="">-- Select Academic Session --</option>
                                @foreach($academicSessions as $session)
                                    <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Examination Session</label>
                            <select class="form-select select2" name="examination_session_id" id="examination_session_id" required>
                                <option value="">-- Select Examination Session --</option>
                                @foreach($examinationSessions as $exam)
                                    <option value="{{ $exam->id }}" {{ request('examination_session_id') == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Examination Term</label>
                            <select class="form-select select2" name="examination_term_id" id="examination_term_id" required {{ request('examination_session_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Examination Term --</option>
                                @foreach($examinationTerms as $term)
                                    <option value="{{ $term->id }}" {{ request('examination_term_id') == $term->id ? 'selected' : '' }}>{{ $term->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Study Level</label>
                            <select class="form-select select2" name="study_level_id" id="study_level_id" required {{ request('academic_session_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Study Level --</option>
                                @foreach($studyLevels as $level)
                                    <option value="{{ $level->id }}" {{ request('study_level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Program</label>
                            <select class="form-select select2" name="program_id" id="program_id" required {{ request('study_level_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Program --</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Class</label>
                            <select class="form-select select2" name="program_class_id" id="program_class_id" required {{ request('program_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Class --</option>
                                @foreach($programClasses as $class)
                                    <option value="{{ $class->id }}" {{ request('program_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Course (Optional)</label>
                            <select class="form-select select2" name="course_id" id="course_id" {{ request('program_class_id') ? '' : 'disabled' }}>
                                <option value="">-- Optional --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Section (Optional)</label>
                            <select class="form-select select2" name="course_section_id" id="course_section_id" {{ request('course_id') ? '' : 'disabled' }}>
                                <option value="">-- Optional --</option>
                                @foreach($courseSections as $section)
                                    <option value="{{ $section->id }}" {{ request('course_section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 align-self-end">
                            <button class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                        </div>

                        @can('create_examination_marks')
                            <div class="text-end mb-3">
                                <a href="{{ route('examination-marks.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Add New Marks
                                </a>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>

            @if($examMarks->isNotEmpty())
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="marks-table" class="table table-hover table-sm mb-0" style="font-size: 0.875rem;">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Class</th>
                                    <th>Course</th>
                                    <th>Section</th>
                                    <th>Exam Date</th>
                                    <th class="text-end">Obtained</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Passing</th>
                                    <th class="text-end">Sessional</th>
                                    <th class="text-end">Total Obtained</th>

                                @if($isAdmin)
                                        <th>Marked By</th>
                                        <th>Updated</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($examMarks as $index => $mark)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td class="text-truncate" style="max-width: 150px;">{{ $mark->student->name ?? 'N/A' }}</td>
                                        <td class="text-truncate" style="max-width: 120px;">{{ $mark->program->name ?? 'N/A' }}</td>
                                        <td class="text-truncate" style="max-width: 100px;">{{ $mark->class->name ?? 'N/A' }}</td>
                                        <td class="text-truncate" style="max-width: 150px;">{{ $mark->course->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($mark->courseSection)
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $mark->courseSection->name }}</span>
                                            @else N/A @endif
                                        </td>
                                        <td>
                                            @if($mark->dateSheet?->exam_date)
                                                <span class="text-muted">{{ \Carbon\Carbon::parse($mark->dateSheet->exam_date)->format('d M Y') }}</span>
                                            @else N/A @endif
                                        </td>
                                        <td class="text-end fw-bold {{ ($mark->marks_obtained >= ($mark->passing_marks ?? ($mark->total_marks * 0.5))) ? 'text-success' : 'text-danger' }}">
                                            {{ $mark->marks_obtained }}
                                        </td>
                                        <td class="text-end fw-bold">{{ $mark->total_marks }}</td>
                                        <td class="text-end fw-bold">{{ $mark->passing_marks ?? '-' }}</td>
                                        <td class="text-end fw-bold">{{ $mark->sessional_marks ?? '-' }}</td>
                                        <td class="text-end fw-bold">
                                            {{ ($mark->marks_obtained ?? 0) + ($mark->sessional_marks ?? 0) }}
                                        </td>
                                        @if($isAdmin)
                                            <td class="text-truncate" style="max-width: 120px;">{{ $mark->markedBy?->name ?? 'N/A' }}</td>
                                            <td class="text-muted">{{ $mark->updated_at->format('d M Y h:i A') }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });

            @if($examMarks->isNotEmpty())
            $('#marks-table').DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                ordering: true,
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="bi bi-eye-fill"></i> Columns',
                        className: 'btn btn-light btn-sm',
                        columns: ':not(.no-colvis)'
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search marks..."
                },
                responsive: true,
                columnDefs: [
                    { targets: [0], className: 'no-colvis' },
                    { targets: '_all', className: 'dt-body-nowrap' }
                ]
            });
            @endif

            function resetSelect(selector, placeholder) {
                $(selector).prop('disabled', true).html(`<option value="">${placeholder}</option>`);
            }

            $('#academic_session_id').change(function () {
                let id = $(this).val();
                resetSelect('#study_level_id', 'Please select Academic Session first');
                resetSelect('#program_id', 'Please select Study Level first');
                resetSelect('#program_class_id', 'Please select Program first');
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');
                if (!id) return;
                $.get(`/ajax-study-levels?academic_session_id=${id}`, function(data) {
                    $('#study_level_id').prop('disabled', false).html('<option value="">Select Study Level</option>');
                    data.forEach(row => $('#study_level_id').append(`<option value="${row.id}">${row.name}</option>`));
                });
            });

            $('#study_level_id').change(function () {
                let id = $(this).val();
                resetSelect('#program_id', 'Please select Study Level first');
                resetSelect('#program_class_id', 'Please select Program first');
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');
                if (!id) return;
                $.get(`/ajax-programs?study_level_id=${id}`, function(data) {
                    $('#program_id').prop('disabled', false).html('<option value="">Select Program</option>');
                    data.forEach(row => $('#program_id').append(`<option value="${row.id}">${row.name}</option>`));
                });
            });

            $('#program_id').change(function () {
                let id = $(this).val();
                resetSelect('#program_class_id', 'Please select Program first');
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');
                if (!id) return;
                $.get(`/program-classes?program_id=${id}`, function(data) {
                    $('#program_class_id').prop('disabled', false).html('<option value="">Select Class</option>');
                    data.forEach(row => $('#program_class_id').append(`<option value="${row.id}">${row.name}</option>`));
                });
            });

            $('#program_class_id').change(function () {
                let id = $(this).val();
                resetSelect('#course_id', 'Please select Program Class first');
                resetSelect('#course_section_id', 'Please select Course first');
                if (!id) return;
                $.get(`/ajax-courses?program_class_id=${id}`, function(data) {
                    $('#course_id').prop('disabled', false).html('<option value="">Select Course</option>');
                    data.forEach(row => $('#course_id').append(`<option value="${row.id}">${row.name}</option>`));
                });
            });

            $('#course_id').change(function () {
                let id = $(this).val();
                resetSelect('#course_section_id', 'Please select Course first');
                if (!id) return;
                $.get(`/ajax-course-sections?course_id=${id}`, function(data) {
                    $('#course_section_id').prop('disabled', false).html('<option value="">Select Section</option>');
                    data.forEach(row => $('#course_section_id').append(`<option value="${row.id}">${row.name}</option>`));
                });
            });

            $('#examination_session_id').change(function () {
                let id = $(this).val();
                resetSelect('#examination_term_id', 'Please select Examination Session first');
                if (!id) return;
                $.get(`/ajax-examination-terms?examination_session_id=${id}`, function(data) {
                    $('#examination_term_id').prop('disabled', false).html('<option value="">Select Examination Term</option>');
                    data.forEach(row => $('#examination_term_id').append(`<option value="${row.id}">${row.title}</option>`));
                });
            });
        });
    </script>
@endsection

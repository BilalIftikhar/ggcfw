@extends('layouts.app')

@section('title', 'Examination Date Sheet')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Examination Date Sheet</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Date Sheet</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Filter Options</h5>
                </div>
                <div class="card-body pt-3">
                    <form id="filterForm" class="row g-3" method="GET" action="{{ route('examination-date-sheet.index') }}">
                        <div class="col-md-3">
                            <label class="form-label">Academic Session</label>
                            <select class="form-select select2" name="academic_session_id" id="academic_session_id">
                                <option value="">-- Select Academic Session --</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Examination Session</label>
                            <select class="form-select select2" name="examination_session_id" id="examination_session_id">
                                <option value="">-- Select Examination Session --</option>
                                @foreach($examSessions as $exam)
                                    <option value="{{ $exam->id }}" {{ request('examination_session_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Examination Term</label>
                            <select class="form-select select2" name="examination_term_id" id="examination_term_id" {{ request('examination_session_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Examination Term --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ request('examination_term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Study Level</label>
                            <select class="form-select select2" name="study_level_id" id="study_level_id" {{ request('academic_session_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Study Level --</option>
                                @foreach($studyLevels as $level)
                                    <option value="{{ $level->id }}" {{ request('study_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Program</label>
                            <select class="form-select select2" name="program_id" id="program_id" {{ request('study_level_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Program --</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Class</label>
                            <select class="form-select select2" name="program_class_id" id="program_class_id" {{ request('program_id') ? '' : 'disabled' }}>
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('program_class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                        </div>
                        @if(request()->hasAny(['academic_session_id', 'examination_session_id', 'examination_term_id', 'study_level_id', 'program_id', 'program_class_id']))
                            <div class="col-md-3 align-self-end">
                                <a href="{{ route('examination-date-sheet.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title m-0">Date Sheet Entries</h5>
                        @can('create_date_sheet')
                            <a href="{{ route('examination-date-sheet.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i> Create/Update Date Sheet
                            </a>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        @if($groupedSheets->count() > 0)
                            @foreach($groupedSheets as $programName => $entries)
                                <div class="program-section mb-5">
                                    <div class="program-header bg-primary text-white p-3 rounded-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 fw-bold">
                                                <i class="bi bi-journal-bookmark-fill me-2"></i>{{ $programName }}
                                            </h6>
                                            <span class="badge bg-light text-primary">{{ count($entries) }} Exams</span>
                                        </div>
                                    </div>
                                    <div class="table-container">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="text-center" width="5%">#</th>
                                                <th width="25%">Course</th>
                                                <th class="text-center" width="10%">Section</th>
                                                <th class="text-center" width="12%">Date</th>
                                                <th class="text-center" width="12%">Time</th>
                                                <th class="text-center" width="10%">Room No.</th>
                                                @if(auth()->user()->can('delete_date_sheet'))
                                                    <th class="text-center" width="15%">Actions</th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($entries as $index => $sheet)
                                                <tr>
                                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                                    <td class="align-middle">
                                                        <div class="fw-semibold">{{ $sheet->course->name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $sheet->course->code ?? '' }}</small>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge bg-info text-dark">{{ $sheet->courseSection->name ?? 'N/A' }}</span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <span class="fw-medium">{{ \Carbon\Carbon::parse($sheet->exam_date)->format('D, d M Y') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <span class="fw-medium">{{ \Carbon\Carbon::parse($sheet->start_time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($sheet->end_time)->format('h:i A') }} </span>

                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge bg-secondary">{{ $sheet->room->room_number ?? 'N/A' }}</span>
                                                    </td>
                                                    @if(auth()->user()->can('delete_date_sheet'))
                                                        <td class="text-center align-middle">
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-form-id="delete-form-{{ $sheet->id }}">
                                                                <i class="bi bi-trash me-1"></i> Delete
                                                            </button>
                                                            <form id="delete-form-{{ $sheet->id }}"
                                                                  action="{{ route('examination-date-sheet.destroy', $sheet->id) }}"
                                                                  method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 5rem;"></i>
                                    <h4 class="mt-3">No date sheet entries found</h4>
                                    <p class="text-muted">Please adjust your filters or create new entries</p>
                                    @can('create_date_sheet'))
                                    <a href="{{ route('examination-date-sheet.create') }}" class="btn btn-primary mt-3">
                                        <i class="bi bi-plus-circle me-1"></i> Create New Entry
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('styles')
    <style>
        /* Table styling */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.04);
        }

        /* Perfect alignment */
        .table th, .table td {
            vertical-align: middle !important;
        }

        .table th.text-center, .table td.text-center {
            text-align: center !important;
        }

        /* Consistent cell padding */
        .table > :not(:first-child) {
            border-top: none;
        }

        .table td, .table th {
            padding: 12px 8px;
        }

        /* Program section styling */
        .program-section {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .program-header {
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        /* Badge styling */
        .badge {
            font-weight: 500;
            padding: 5px 10px;
            min-width: 50px;
            display: inline-block;
        }

        /* Table container */
        .table-container {
            overflow-x: auto;
            border-radius: 0 0 8px 8px;
        }

        /* Button styling */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 6px 12px;
        }

        /* Empty state styling */
        .empty-state {
            max-width: 500px;
            margin: 0 auto;
        }

        /* Select2 styling */
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding-top: 4px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                placeholder: $(this).data('placeholder')
            });

            // Academic Session change
            $('#academic_session_id').on('change', function () {
                let sessionId = $(this).val();
                $('#study_level_id, #program_id, #program_class_id').prop('disabled', true).empty().append('<option value="">-- Select --</option>');
                if (!sessionId) return;

                $.get(`/ajax-study-levels?academic_session_id=${sessionId}`, function (data) {
                    $('#study_level_id').prop('disabled', false);
                    $.each(data, function (i, level) {
                        $('#study_level_id').append(`<option value="${level.id}">${level.name}</option>`);
                    });

                    @if(request('study_level_id'))
                    $('#study_level_id').val('{{ request('study_level_id') }}').trigger('change');
                    @endif
                });
            });

            // Study Level change
            $('#study_level_id').on('change', function () {
                let levelId = $(this).val();
                $('#program_id, #program_class_id').prop('disabled', true).empty().append('<option value="">-- Select --</option>');
                if (!levelId) return;

                $.get(`/ajax-programs?study_level_id=${levelId}`, function (data) {
                    $('#program_id').prop('disabled', false);
                    $.each(data, function (i, program) {
                        $('#program_id').append(`<option value="${program.id}">${program.name}</option>`);
                    });

                    @if(request('program_id'))
                    $('#program_id').val('{{ request('program_id') }}').trigger('change');
                    @endif
                });
            });

            // Program change
            $('#program_id').on('change', function () {
                let programId = $(this).val();
                $('#program_class_id').prop('disabled', true).empty().append('<option value="">-- Select --</option>');
                if (!programId) return;

                $.get(`/program-classes?program_id=${programId}`, function (data) {
                    $('#program_class_id').prop('disabled', false);
                    $.each(data, function (i, cls) {
                        $('#program_class_id').append(`<option value="${cls.id}">${cls.name}</option>`);
                    });

                    @if(request('program_class_id'))
                    $('#program_class_id').val('{{ request('program_class_id') }}');
                    @endif
                });
            });

            // Examination Session change
            $('#examination_session_id').on('change', function () {
                let sessionId = $(this).val();
                $('#examination_term_id').prop('disabled', true).empty().append('<option value="">-- Select --</option>');
                if (!sessionId) return;

                $.get(`/ajax-examination-terms?examination_session_id=${sessionId}`, function (data) {
                    $('#examination_term_id').prop('disabled', false);
                    $.each(data, function (i, term) {
                        $('#examination_term_id').append(`<option value="${term.id}">${term.title}</option>`);
                    });

                    @if(request('examination_term_id'))
                    $('#examination_term_id').val('{{ request('examination_term_id') }}');
                    @endif
                });
            });

            // Delete confirmation
            $(document).on('click', '.delete-btn', function () {
                const formId = $(this).data('form-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            });

            // Trigger initial changes if filters are set
            @if(request('academic_session_id'))
            $('#academic_session_id').trigger('change');
            @endif

            @if(request('examination_session_id'))
            $('#examination_session_id').trigger('change');
            @endif
        });
    </script>
@endsection

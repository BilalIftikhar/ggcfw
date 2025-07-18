@extends('layouts.app')

@section('title', 'Programs')

@section('content')
    <style>
        /* Reuse your styles exactly as in study levels */
        .table th, .table td {
            vertical-align: middle !important;
        }
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 1px solid #c8e6c9;
        }
        .dataTables_scrollHead {
            border-top: 1px solid #c8e6c9 !important;
            background-color: #e8f5e9;
        }
        .badge {
            font-size: 0.85em;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dt-buttons {
            float: left;
            margin-right: 15px;
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_info {
            margin-top: 15px;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px;
        }
        .btn i {
            margin-right: 5px;
        }
        .dt-buttons .btn {
            margin-right: 5px;
            border: 1px solid #dee2e6;
        }
        .dataTables_wrapper .table {
            margin-top: 20px;
        }
        .dataTables_filter label,
        .dataTables_length label {
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
        .dataTables_wrapper .dataTables_scrollBody table {
            border-collapse: separate !important;
            border-spacing: 0;
        }
        .dataTables_scrollHeadInner,
        .dataTables_scrollHead table {
            width: 100% !important;
        }
        .dataTables_scrollHead thead th {
            background-color: #e8f5e9 !important;
            border-bottom: 1px solid #c8e6c9 !important;
        }

        .btn-program-classes {
            background-color: #5c6bc0;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-program-classes:hover {
            background-color: #3f51b5;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .btn-course-path {
            background-color: #26a69a;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-course-path:hover {
            background-color: #00897b;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .action-buttons {
            min-width: 200px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .action-buttons {
                min-width: auto;
            }

            .btn-program-classes,
            .btn-course-path {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Programs</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Programs</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_program')
                <div class="mb-3">
                    <button type="button" class="btn btn-success" id="openModalBtn">
                        <i class="bi bi-plus-circle"></i> Add Program
                    </button>
                </div>
            @endcan

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        List of Programs
                        @isset($studyLevel)
                            - {{ $studyLevel->name }}
                        @endisset
                    </h5>

                    <div class="table-responsive">
                        <table id="programsTable" class="table table-bordered table-hover align-middle w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Program Name</th>
                                <th>Study Level</th>
                                <th>Academic Session</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Active</th>
                                <th>Admission</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($programs as $index => $program)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->studyLevel->name ?? 'N/A' }}</td>
                                    <td>{{ $program->academicSession->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $program->is_semester ? 'Semester Based' : 'Year Based' }}
                                    </td>
                                    <td>
                                        {{ $program->is_semester ? ($program->number_of_semesters . ' Semesters') : ($program->number_of_years . ' Years') }}
                                    </td>
                                    <td>
                    <span class="badge bg-{{ $program->is_active ? 'success' : 'secondary' }}">
                        {{ $program->is_active ? 'Yes' : 'No' }}
                    </span>
                                    </td>
                                    <td>
                    <span class="badge bg-{{ $program->admission_enabled ? 'info' : 'secondary' }}">
                        {{ $program->admission_enabled ? 'Yes' : 'No' }}
                    </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2 action-buttons">
                                            @can('update_program')
                                                <button type="button" class="btn btn-sm btn-outline-primary editBtn"
                                                        data-id="{{ $program->id }}"
                                                        data-name="{{ $program->name }}"
                                                        data-study_level="{{ $program->study_level_id }}"
                                                        data-academic_session="{{ $program->academic_session_id }}"
                                                        data-is_semester="{{ $program->is_semester }}"
                                                        data-number_of_years="{{ $program->number_of_years }}"
                                                        data-number_of_semesters="{{ $program->number_of_semesters }}"
                                                        data-is_active="{{ $program->is_active }}"
                                                        data-admission_enabled="{{ $program->admission_enabled }}"
                                                        data-credit_hour_system="{{ $program->credit_hour_system }}"
                                                        data-teaching_days="{{ $program->teaching_days_per_week }}"
                                                        data-period_duration="{{ $program->period_duration }}"
                                                        data-max_periods="{{ $program->max_periods_per_day }}"
                                                        data-labs_separate_days="{{ $program->labs_on_separate_days }}"
                                                        data-preferred_lab_days="{{ $program->preferred_lab_days }}"
                                                        data-attendance_threshold="{{ $program->attendance_threshold }}"
                                                        data-bs-toggle="tooltip" title="Edit Program">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            @endcan

                                            @can('delete_program')
                                                <form action="{{ route('programs.destroy', $program->id) }}" method="POST" class="d-inline deleteForm">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="tooltip" title="Delete Program">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('update_program')
                                                <a href="{{ route('programs.classes', ['program_id' => $program->id]) }}"
                                                   class="btn btn-sm btn-program-classes"
                                                   data-bs-toggle="tooltip" title="Show Program Classes">
                                                    <i class="bi bi-layout-text-sidebar-reverse"></i> Classes
                                                </a>
                                            @endcan

                                            @can('view_course')
                                                <a href="{{ route('programs.course_path', ['program_id' => $program->id]) }}"
                                                   class="btn btn-sm btn-course-path"
                                                   data-bs-toggle="tooltip" title="Show Course Path">
                                                    <i class="bi bi-diagram-3"></i> Courses
                                                </a>
                                            @endcan
                                        </div>
                                    </td>


                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>



                </div>
            </div>
        </section>

        <!-- Modal -->
        <div class="modal fade" id="programModal" tabindex="-1" role="dialog" aria-labelledby="programModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <form id="programForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="_method" value="POST">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="programModalLabel">Add Program</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row g-3">
                                    <!-- Program Name -->
                                    <div class="col-12 col-md-4">
                                        <label for="name" class="form-label">Program Name</label>
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>

                                    <!-- Study Level -->
                                    <div class="col-12 col-md-4">
                                        <label for="study_level_id" class="form-label">Study Level</label>
                                        <select name="study_level_id" id="study_level_id" class="form-select" required>
                                            @if(isset($studyLevel))
                                                <option value="{{ $studyLevel->id }}" selected>{{ $studyLevel->name }}</option>
                                            @else
                                                @foreach(\App\Models\StudyLevel::with('academicSession')->orderBy('name')->get() as $level)
                                                    <option value="{{ $level->id }}">
                                                        {{ $level->name }} ({{ $level->academicSession->name ?? 'N/A' }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Academic Session -->
                                    <div class="col-12 col-md-4">
                                        <label for="academic_session_id" class="form-label">Academic Session</label>
                                        <select name="academic_session_id" id="academic_session_id" class="form-select" required>
                                            @if(isset($studyLevel) && $studyLevel->academicSession)
                                                <option value="{{ $studyLevel->academicSession->id }}" selected>
                                                    {{ $studyLevel->academicSession->name }}
                                                </option>
                                            @else
                                                <option disabled>No Academic Session available</option>
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Is Semester -->
                                    <div class="col-12 col-md-4 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_semester" name="is_semester" value="1">
                                            <label class="form-check-label" for="is_semester">Is Semester Based?</label>
                                        </div>
                                    </div>

                                    <!-- Number of Years (hidden if semester) -->
                                    <div class="col-12 col-md-6" id="yearsContainer">
                                        <label for="number_of_years" class="form-label">Number of Years</label>
                                        <input type="number" min="0" class="form-control" name="number_of_years" id="number_of_years" value="0" required>
                                    </div>

                                    <!-- Number of Semesters (hidden if not semester) -->
                                    <div class="col-12 col-md-6 d-none" id="semestersContainer">
                                        <label for="number_of_semesters" class="form-label">Number of Semesters</label>
                                        <input type="number" min="0" class="form-control" name="number_of_semesters" id="number_of_semesters" value="0" required>
                                    </div>

                                    <!-- Is Active Toggle -->
                                    <div class="col-12 col-md-6 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                            <label class="form-check-label" for="is_active">Is Active?</label>
                                        </div>
                                    </div>

                                    <!-- Admission Enabled Toggle -->
                                    <div class="col-12 col-md-6 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="admission_enabled" name="admission_enabled" value="1">
                                            <label class="form-check-label" for="admission_enabled">Admission Enabled?</label>
                                        </div>
                                    </div>

                                    <!-- Timetable Configuration Section -->
                                    <div class="col-12">
                                        <hr>
                                        <h5 class="mb-3">Timetable Configuration</h5>
                                    </div>

                                    <!-- Credit Hour System -->
                                    <div class="col-12 col-md-4 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="credit_hour_system" name="credit_hour_system" value="1">
                                            <label class="form-check-label" for="credit_hour_system">Credit Hour System?</label>
                                        </div>
                                    </div>

                                    <!-- Teaching Days Per Week -->
                                    <div class="col-12 col-md-4">
                                        <label for="teaching_days_per_week" class="form-label">Teaching Days Per Week</label>
                                        <input type="number" min="1" max="7" class="form-control" name="teaching_days_per_week" id="teaching_days_per_week" value="5" required>
                                    </div>

                                    <!-- Period Duration -->
                                    <div class="col-12 col-md-4">
                                        <label for="period_duration" class="form-label">Period Duration (minutes)</label>
                                        <input type="number" min="30" max="120" class="form-control" name="period_duration" id="period_duration" value="45" required>
                                    </div>

                                    <!-- Max Periods Per Day -->
                                    <div class="col-12 col-md-4">
                                        <label for="max_periods_per_day" class="form-label">Max Periods Per Day</label>
                                        <input type="number" min="1" max="10" class="form-control" name="max_periods_per_day" id="max_periods_per_day" value="6" required>
                                    </div>

                                    <!-- Labs on Separate Days -->
                                    <div class="col-12 col-md-4 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="labs_on_separate_days" name="labs_on_separate_days" value="1">
                                            <label class="form-check-label" for="labs_on_separate_days">Labs on Separate Days?</label>
                                        </div>
                                    </div>

                                    <!-- Attendance Threshold -->
                                    <div class="col-12 col-md-4">
                                        <label for="attendance_threshold" class="form-label">Attendance Threshold (%)</label>
                                        <input type="number" min="1" max="100" class="form-control" name="attendance_threshold" id="attendance_threshold" value="75" required>
                                    </div>

                                    <!-- Preferred Lab Days (Checkboxes) -->
                                    <div class="col-12">
                                        <label class="form-label">Preferred Lab Days</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @php
                                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                            @endphp
                                            @foreach($days as $day)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="preferred_lab_days[]"
                                                           id="lab_day_{{ strtolower($day) }}"
                                                           value="{{ $day }}"
                                                           @if(in_array($day, $activeWorkingDays)) checked @endif>
                                                    <label class="form-check-label" for="lab_day_{{ strtolower($day) }}">{{ $day }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="cancelModalBtn" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#programsTable').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-files me-1"></i> Copy',
                        titleAttr: 'Copy to clipboard',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                        titleAttr: 'Export to CSV',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                        titleAttr: 'Print table',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Search programs...",
                    lengthMenu: "Show _MENU_ programs",
                    info: "Showing _START_ to _END_ of _TOTAL_ programs",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                },
                columnDefs: [
                    {
                        orderable: false,
                        targets: -1,
                        className: 'text-center'
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            function toggleYearSemesterFields() {
                if ($('#is_semester').is(':checked')) {
                    $('#semestersContainer').removeClass('d-none');
                    $('#number_of_semesters').prop('required', true).val('8'); // Default to 2 semesters
                    $('#yearsContainer').addClass('d-none');
                    $('#number_of_years').prop('required', false).val('0');
                } else {
                    $('#yearsContainer').removeClass('d-none');
                    $('#number_of_years').prop('required', true).val('2'); // Default to 1 year
                    $('#semestersContainer').addClass('d-none');
                    $('#number_of_semesters').prop('required', false).val('0');
                }
            }

            // Initialize with semester shown by default
            toggleYearSemesterFields();

            // Toggle on checkbox change
            $('#is_semester').change(toggleYearSemesterFields);

            // Open Create Modal
            $('#openModalBtn').click(function () {
                $('#programForm').attr('action', '{{ route('programs.store') }}');
                $('#_method').val('POST');
                $('#programForm')[0].reset();

                // Set default checkbox states explicitly after reset
                $('#is_semester').prop('checked', true);
                $('#is_active').prop('checked', true);
                $('#admission_enabled').prop('checked', true);
                $('#credit_hour_system').prop('checked', true);

                // Set preferred lab days
                @foreach($activeWorkingDays as $day)
                $(`#lab_day_{{ strtolower($day) }}`).prop('checked', true);
                @endforeach

                // Ensure correct fields are shown
                toggleYearSemesterFields();

                $('#programModalLabel').text('Add Program');
                $('#programModal').modal('show');
            });

            // Open Edit Modal
            $('.editBtn').click(function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const studyLevel = $(this).data('study_level');
                const academicSession = $(this).data('academic_session');
                const isSemester = $(this).data('is_semester');
                const numberOfYears = $(this).data('number_of_years');
                const numberOfSemesters = $(this).data('number_of_semesters');
                const isActive = $(this).data('is_active');
                const admissionEnabled = $(this).data('admission_enabled');
                const creditHourSystem = $(this).data('credit_hour_system');
                const teachingDays = $(this).data('teaching_days');
                const periodDuration = $(this).data('period_duration');
                const maxPeriods = $(this).data('max_periods');
                const labsSeparateDays = $(this).data('labs_separate_days');
                const preferredLabDays = $(this).data('preferred_lab_days');
                const attendanceThreshold = $(this).data('attendance_threshold');

                // Basic fields
                $('#name').val(name);
                $('#study_level_id').val(studyLevel);
                $('#academic_session_id').val(academicSession);

                // Semester/year fields
                $('#is_semester').prop('checked', isSemester);
                $('#number_of_years').val(numberOfYears);
                $('#number_of_semesters').val(numberOfSemesters);

                // Toggle fields
                $('#is_active').prop('checked', isActive);
                $('#admission_enabled').prop('checked', admissionEnabled);

                // Timetable configuration
                $('#credit_hour_system').prop('checked', creditHourSystem);
                $('#teaching_days_per_week').val(teachingDays);
                $('#period_duration').val(periodDuration);
                $('#max_periods_per_day').val(maxPeriods);
                $('#labs_on_separate_days').prop('checked', labsSeparateDays);
                $('#attendance_threshold').val(attendanceThreshold);

                // Preferred lab days checkboxes
                if (preferredLabDays) {
                    const days = preferredLabDays.split(',');
                    $('input[name="preferred_lab_days[]"]').prop('checked', false);
                    days.forEach(day => {
                        $(`#lab_day_${day.toLowerCase()}`).prop('checked', true);
                    });
                } else {
                    $('input[name="preferred_lab_days[]"]').prop('checked', false);
                }

                // Update form method and action
                $('#_method').val('PUT');
                $('#programForm').attr('action', `/programs/${id}`);
                $('#programModalLabel').text('Edit Program');

                // Show/hide semester/year fields based on is_semester
                toggleYearSemesterFields();

                $('#programModal').modal('show');
            });

            // Cancel Modal
            $('#cancelModalBtn').click(function () {
                $('#programModal').modal('hide');
            });

            // SweetAlert Delete Confirmation
            $('.deleteForm').submit(function(e) {
                e.preventDefault();
                let form = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $(document).on('click', '.delete-program-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the program.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

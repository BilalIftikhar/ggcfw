@extends('layouts.app')

@section('title', 'Course Sections')

@section('content')
    <style>

        /* Select2 Bootstrap Integration */
        .select2-container--bootstrap .select2-selection {
            height: auto;
            min-height: 38px;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
        }

        .select2-container--open {
            z-index: 1060 !important;
        }

        .select2-dropdown {
            border: 1px solid #ced4da !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            border-radius: 0.25rem !important;
        }

        .select2-results__option {
            padding: 0.375rem 0.75rem !important;
        }

        .select2-container--bootstrap .select2-selection__arrow {
            height: 36px !important;
        }
        /* Reuse the same styles as Courses view */
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
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Course Sections</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active">Sections</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of Sections for {{ $course->name }}</h5>

                    @can('create_course_section')
                        <div class="mb-3">
                            <button type="button"
                                    class="btn btn-success"
                                    id="addSectionBtn"
                                    data-course-id="{{ $course->id }}">
                                <i class="bi bi-plus-circle"></i> Add Section
                            </button>
                        </div>
                    @endcan

                    <div class="table-responsive">
                        <table id="sectionsTable" class="table table-bordered table-hover align-middle w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Section Name</th>
                                <th>Course</th>
                                <th>Program</th>
                                <th>Teacher</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sections as $index => $section)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $section->name }}</td>
                                    <td>{{ $section->course->name ?? 'N/A' }}</td>
                                    <td>{{ $section->program->name ?? 'N/A' }}</td>
                                    <td>{{ $section->teachers->name ?? 'Not Assigned' }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $section->no_of_students_enrolled }} / {{ $section->no_of_students_allowed }}
                                        </span>
                                    </td>
                                    <td>{!! $section->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</td>
                                    <td>
                                        @can('update_course_section')
                                            <button class="btn btn-outline-primary btn-sm editSectionBtn"
                                                    data-id="{{ $section->id }}"
                                                    data-name="{{ $section->name }}"
                                                    data-program_id="{{ $section->program_id }}"
                                                    data-program_name="{{ $section->program->name ?? 'N/A' }}"
                                                    data-course_id="{{ $section->course_id }}"
                                                    data-course_name="{{ $section->course->name ?? 'N/A' }}"
                                                    data-teacher_id="{{ $section->teacher_id }}"
                                                    data-is_active="{{ $section->is_active ? '1' : '0' }}"
                                                    data-no_of_students_allowed="{{ $section->no_of_students_allowed }}"
                                                    data-has_lab="{{ $section->has_lab ? '1' : '0' }}"
                                                    data-requires_continuous_slots="{{ $section->requires_continuous_slots ? '1' : '0' }}"
                                                    data-credit_hours="{{ $section->credit_hours }}"
                                                    data-lab_credit_hours="{{ $section->lab_credit_hours }}"
                                                    data-required_minutes_theory_weekly="{{ $section->required_minutes_theory_weekly }}"
                                                    data-required_minutes_lab_weekly="{{ $section->required_minutes_lab_weekly }}"
                                                    title="Edit Section">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan

                                    @can('delete_course_section')
                                            <button class="btn btn-outline-danger btn-sm deleteSectionBtn"
                                                    data-id="{{ $section->id }}"
                                                    data-name="{{ $section->name }}"
                                                    title="Delete Section">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Section Modal -->
        <div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="addSectionForm" method="POST" action="{{ route('course_sections.store') }}">
                    @csrf
                    <input type="hidden" name="course_id" id="add_course_id" value="{{ $course->id }}">
                    <!-- Hidden fields for course-level settings -->
                    <input type="hidden" name="program_id" value="{{ $course->program_id }}">
                    <input type="hidden" name="no_of_students_allowed" value="{{ $course->students_per_section ?? 0 }}">
                    <input type="hidden" name="has_lab" value="{{ $course->has_lab ? 1 : 0 }}">
                    <input type="hidden" name="requires_continuous_slots" value="{{ $course->requires_continuous_slots ? 1 : 0 }}">
                    <input type="hidden" name="credit_hours" value="{{ $course->credit_hours ?? 0 }}">
                    <input type="hidden" name="lab_credit_hours" value="{{ $course->lab_credit_hours ?? 0 }}">
                    <input type="hidden" name="required_minutes_theory_weekly" value="{{ $course->required_minutes_theory_weekly ?? 0 }}">
                    <input type="hidden" name="required_minutes_lab_weekly" value="{{ $course->required_minutes_lab_weekly ?? 0 }}">

                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-success text-white py-3">
                            <h5 class="modal-title fs-5 fw-semibold" id="addSectionModalLabel">
                                <i class="fas fa-layer-group me-2"></i>Add New Section
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- First Row -->
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-medium">Section Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-tag text-muted"></i></span>
                                        <input type="text" name="name" id="add_name" class="form-control" placeholder="Section A" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Program</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-graduation-cap text-muted"></i></span>
                                        <input type="text" class="form-control" value="{{ $course->program->name ?? 'N/A' }}" readonly>
                                    </div>
                                    <small class="text-muted">Program is determined by the course</small>
                                </div>

                                <!-- Second Row -->
                                <div class="col-md-6">
                                    <label for="add_teacher_id" class="form-label fw-medium">Assigned Teacher</label>
                                    <div class="input-group">
                                        <select name="teacher_id" id="add_teacher_id" class="form-select select2-teacher ">
                                            <option value="">-- Select Teacher --</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Student Capacity</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-users text-muted"></i></span>
                                        <input type="text" class="form-control"
                                               value="{{ $course->students_per_section ?? 0 }} ({{ $course->students_per_section == 0 ? 'Unlimited' : 'Max' }})" readonly>
                                    </div>
                                    <small class="text-muted">Capacity is set at the course level</small>
                                </div>

                                <!-- Third Row - Lab Configuration (display only) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lab Configuration</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                        <input type="text" class="form-control"
                                               value="{{ $course->has_lab ? 'Has Lab Component' : 'No Lab Component' }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lab Slot Requirement</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-link text-muted"></i></span>
                                        <input type="text" class="form-control"
                                               value="{{ $course->requires_continuous_slots ? 'Requires Continuous Slots' : 'No Continuous Slot Requirement' }}" readonly>
                                    </div>
                                    <small class="text-muted">For labs only</small>
                                </div>

                                <!-- Fourth Row - Credit Hours (display only) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lecture Credit Hours</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-clock text-muted"></i></span>
                                        <input type="text" class="form-control"
                                               value="{{ $course->credit_hours ?? 0 }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lab Credit Hours</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                        <input type="text" class="form-control"
                                               value="{{ $course->lab_credit_hours ?? 0 }}" readonly>
                                    </div>
                                </div>

                                <!-- Fifth Row - Required Minutes (display only) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Weekly Theory Minutes</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hourglass-half text-muted"></i></span>
                                        <input type="text" class="form-control"
                                               value="{{ $course->required_minutes_theory_weekly ?? 0 }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Weekly Lab Minutes</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hourglass-half text-muted"></i></span>
                                        <input type="text" class="form-control"
                                               value="{{ $course->required_minutes_lab_weekly ?? 0 }}" readonly>
                                    </div>
                                </div>

                                <!-- Status - Only field that can be changed -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Section Status</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" checked>
                                            <label class="form-check-label ms-2" for="add_is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light py-3">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i>Save Section
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Section Modal -->
        <div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="editSectionForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section_id" id="edit_section_id">
                    <input type="hidden" name="program_id" id="edit_program_id">
                    <input type="hidden" name="course_id" id="edit_course_id">
                    <input type="hidden" name="no_of_students_allowed" id="edit_no_of_students_allowed">
                    <input type="hidden" name="has_lab" id="edit_has_lab">
                    <input type="hidden" name="requires_continuous_slots" id="edit_requires_continuous_slots">
                    <input type="hidden" name="credit_hours" id="edit_credit_hours">
                    <input type="hidden" name="lab_credit_hours" id="edit_lab_credit_hours">
                    <input type="hidden" name="required_minutes_theory_weekly" id="edit_required_minutes_theory_weekly">
                    <input type="hidden" name="required_minutes_lab_weekly" id="edit_required_minutes_lab_weekly">

                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-success text-white py-3">
                            <h5 class="modal-title fs-5 fw-semibold" id="editSectionModalLabel">
                                <i class="fas fa-edit me-2"></i>Edit Section
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label fw-medium">Section Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-tag text-muted"></i></span>
                                        <input type="text" name="name" id="edit_name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Program</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-graduation-cap text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_program_display" readonly>
                                    </div>
                                    <small class="text-muted">Program is determined by the course</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_teacher_id" class="form-label fw-medium">Assigned Teacher</label>
                                    <div class="input-group">

                                        <select name="teacher_id" id="edit_teacher_id" class="form-select select2-teacher">
                                            <option value="">-- Select Teacher --</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Student Capacity</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-users text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_capacity_display" readonly>
                                    </div>
                                    <small class="text-muted">Capacity is set at the course level</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lab Configuration</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_has_lab_display" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lab Slot Requirement</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-link text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_requires_continuous_slots_display" readonly>
                                    </div>
                                    <small class="text-muted">For labs only</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lecture Credit Hours</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-clock text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_credit_hours_display" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Lab Credit Hours</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_lab_credit_hours_display" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Weekly Theory Minutes</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hourglass-half text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_required_minutes_theory_weekly_display" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Weekly Lab Minutes</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hourglass-half text-muted"></i></span>
                                        <input type="text" class="form-control" id="edit_required_minutes_lab_weekly_display" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Section Status</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                            <label class="form-check-label ms-2" for="edit_is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light py-3">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i>Update Section
                            </button>
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
            // Initialize DataTable
            $('#sectionsTable').DataTable({
                dom: '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-end"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-files me-1"></i> Copy',
                        titleAttr: 'Copy to clipboard',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                        titleAttr: 'Export to CSV',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                        titleAttr: 'Print table',
                        exportOptions: { columns: ':not(:last-child)' }
                    }
                ],
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Search sections...",
                    lengthMenu: "Show _MENU_ sections",
                    info: "Showing _START_ to _END_ of _TOTAL_ sections",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                },
                initComplete: function () {
                    $('.dataTables_filter input')
                        .addClass('form-control form-control-sm')
                        .css({ 'max-width': '250px', 'width': '100%' });
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Initialize Select2 for teacher dropdowns
            function initSelect2(selector, parentModal) {
                $(selector).select2({
                    placeholder: '-- Select Teacher --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: parentModal,
                    theme: 'bootstrap',
                    dropdownCssClass: 'border-0 shadow-lg',
                    minimumResultsForSearch: 3
                }).addClass('form-control');
            }

            // Add Section Modal
            $('#addSectionBtn').click(function () {
                $('#addSectionForm')[0].reset();
                $('#add_is_active').prop('checked', true);
                $('#add_course_id').val($(this).data('course-id'));
                $('#addSectionModal').modal('show');

                setTimeout(function() {
                    initSelect2('#add_teacher_id', $('#addSectionModal'));
                }, 100);
            });

            // Edit Section Modal
            $('.editSectionBtn').click(function () {
                let button = $(this);
                let sectionId = button.data('id');
                let name = button.data('name');
                let programId = button.data('program_id');
                let programName = button.data('program_name');
                let courseId = button.data('course_id');
                let courseName = button.data('course_name');
                let teacherId = button.data('teacher_id');
                let isActive = button.data('is_active') == 1;
                let studentsAllowed = button.data('no_of_students_allowed');
                let hasLab = button.data('has_lab') == 1;
                let requiresContinuousSlots = button.data('requires_continuous_slots') == 1;
                let creditHours = button.data('credit_hours');
                let labCreditHours = button.data('lab_credit_hours');
                let theoryMinutes = button.data('required_minutes_theory_weekly');
                let labMinutes = button.data('required_minutes_lab_weekly');

                $('#edit_section_id').val(sectionId);
                $('#edit_name').val(name);
                $('#edit_program_display').val(programName);
                $('#edit_program_id').val(programId);
                $('#edit_course_display').val(courseName);
                $('#edit_course_id').val(courseId);
                $('#edit_teacher_id').val(teacherId);
                $('#edit_is_active').prop('checked', isActive);

                $('#edit_capacity_display').val(studentsAllowed + (studentsAllowed == 0 ? ' (Unlimited)' : ' (Max)'));
                $('#edit_no_of_students_allowed').val(studentsAllowed);
                $('#edit_has_lab').val(hasLab ? 1 : 0);
                $('#edit_requires_continuous_slots').val(requiresContinuousSlots ? 1 : 0);
                $('#edit_credit_hours').val(creditHours);
                $('#edit_lab_credit_hours').val(labCreditHours);
                $('#edit_required_minutes_theory_weekly').val(theoryMinutes);
                $('#edit_required_minutes_lab_weekly').val(labMinutes);

                $('#edit_has_lab_display').val(hasLab ? 'Has Lab Component' : 'No Lab Component');
                $('#edit_requires_continuous_slots_display').val(requiresContinuousSlots ? 'Requires Continuous Slots' : 'No Continuous Slot Requirement');
                $('#edit_credit_hours_display').val(creditHours);
                $('#edit_lab_credit_hours_display').val(labCreditHours);
                $('#edit_required_minutes_theory_weekly_display').val(theoryMinutes);
                $('#edit_required_minutes_lab_weekly_display').val(labMinutes);

                $('#editSectionForm').attr('action', '/course-sections/' + sectionId);
                $('#editSectionModal').modal('show');

                setTimeout(function() {
                    initSelect2('#edit_teacher_id', $('#editSectionModal'));
                }, 100);
            });

            // Form submission handling
            $('#addSectionForm, #editSectionForm').on('submit', function () {
                $(this).find('button[type="submit"]').prop('disabled', true);
            });

            // Clean up Select2 when modals close
            $('#addSectionModal, #editSectionModal').on('hidden.bs.modal', function () {
                $('.select2-teacher').select2('destroy');
            });

            // Delete Section with SweetAlert2 confirmation
            $('.deleteSectionBtn').click(function () {
                let sectionId = $(this).data('id');
                let sectionName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete section: "${sectionName}". This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = $('<form>', {
                            'method': 'POST',
                            'action': '/course-sections/' + sectionId
                        });
                        let token = $('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        });
                        let method = $('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        });
                        form.append(token, method);
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

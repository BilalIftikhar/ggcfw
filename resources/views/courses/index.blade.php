@extends('layouts.app')

@section('title', 'Courses')

@section('content')
    <style>
        /* Highlighted Show Sections button */
        .btn-show-sections {
            background-color: #1e88e5;
            color: #fff;
            font-weight: 500;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-show-sections:hover {
            background-color: #1565c0;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .table th, .table td {
            vertical-align: middle !important;
        }
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 2px solid #a5d6a7 !important;
            color: #2e7d32;
            font-weight: 600;
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
            max-width: 100%;
        }
        .dataTables_wrapper .dataTables_filter input {
            width: auto !important;
            max-width: 250px;
            display: inline-block;
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
        .dataTables_wrapper .dataTables_info,
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

        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.4);
        }

        @media (max-width: 576px) {
            .dataTables_wrapper .dataTables_filter input {
                max-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>


    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Courses</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Courses</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of Courses</h5>

                    @can('create_course')
                        <div class="mb-3">
                            <button type="button"
                                    class="btn btn-success"
                                    id="addCourseBtn"
                                    data-program-id="{{ $program->id }}"
                                    data-class-id="{{ $class->id }}">
                                Add Course
                            </button>

                        </div>
                    @endcan

                    <div class="table-responsive">
                        <table id="coursesTable" class="table table-bordered table-hover align-middle w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Course Name</th>
                                <th>Code</th>
                                <th>Credit Hours</th>
                                <th>Has Lab</th>
                                <th>Lab Credit Hours</th>
                                <th>Class</th>
                                <th>Program</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($courses as $index => $course)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->code }}</td>
                                    <td>{{ $course->credit_hours }}</td>
                                    <td>{!! $course->has_lab ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                                    <td>{{ $course->lab_credit_hours ?? '-' }}</td>
                                    <td>{{ $course->class->name ?? 'N/A' }}</td>
                                    <td>{{ $course->program->name ?? 'N/A' }} </td>
                                    <td>{!! $course->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</td>
                                    <td>
                                        @can('update_course')
                                            <button class="btn btn-outline-primary btn-sm editCourseBtn"
                                                    data-id="{{ $course->id }}"
                                                    data-name="{{ $course->name }}"
                                                    data-code="{{ $course->code }}"
                                                    data-credit_hours="{{ $course->credit_hours }}"
                                                    data-has_lab="{{ $course->has_lab ? '1' : '0' }}"
                                                    data-lab_credit_hours="{{ $course->lab_credit_hours ?? '' }}"
                                                    data-class_id="{{ $course->class_id }}"
                                                    data-program_id="{{ $course->program_id }}"
                                                    data-is_active="{{ $course->is_active ? '1' : '0' }}"
                                                    data-is_mandatory="{{ $course->is_mandatory ? '1' : '0' }}"
                                                    data-no_of_sections="{{ $course->no_of_sections ?? '' }}"
                                                    data-students_per_section="{{ $course->students_per_section }}"
                                                    data-requires_continuous_slots="{{ $course->requires_continuous_slots ? '1' : '0' }}"
                                                    data-required_minutes_theory_weekly="{{ $course->required_minutes_theory_weekly }}"
                                                    data-required_minutes_lab_weekly="{{ $course->required_minutes_lab_weekly }}"
                                                    data-weekly_lectures="{{ $course->weekly_lectures }}"
                                                    data-weekly_labs="{{ $course->weekly_labs }}"
                                                    data-program_is_semester_based="{{ $course->program->is_semester_based ? '1' : '0' }}"
                                                    title="Edit Course">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan

                                        @can('view_course_section')
                                            <a href="{{ route('course_sections.index', ['course_id' => $course->id]) }}"
                                               class="btn btn-sm btn-show-sections"
                                               title="Show Sections">
                                                <i class="bi bi-layers me-1"></i> Sections
                                            </a>
                                        @endcan

                                        @can('delete_course')
                                            <button class="btn btn-outline-danger btn-sm deleteCourseBtn"
                                                    data-id="{{ $course->id }}"
                                                    data-name="{{ $course->name }}"
                                                    title="Delete Course">
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

        <!-- Add Course Modal -->
        <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="addCourseForm" method="POST" action="{{ route('courses.store') }}">
                    @csrf
                    <input type="hidden" name="class_id" id="add_class_id" value="">
                    <input type="hidden" name="program_id" id="add_program_id" value="">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-success text-white py-3">
                            <h5 class="modal-title fs-5 fw-semibold" id="addCourseModalLabel">
                                <i class="fas fa-book me-2"></i>Add New Course
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- First Row -->
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-medium">Course Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-book text-muted"></i></span>
                                        <input type="text" name="name" id="add_name" class="form-control" placeholder="Introduction to Computer Science" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="add_code" class="form-label fw-medium">Course Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                        <input type="text" name="code" id="add_code" class="form-control" placeholder="CS101">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="add_credit_hours" class="form-label fw-medium">Credit Hours <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-clock text-muted"></i></span>
                                        <input type="number" name="credit_hours" id="add_credit_hours" class="form-control" min="0" value="0" required>
                                    </div>
                                </div>

                                <!-- Second Row -->
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Lab Settings</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="add_has_lab" name="has_lab">
                                            <label class="form-check-label ms-2" for="add_has_lab">Enable Lab</label>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="add_labHoursGroup" style="display: none;">
                                        <label for="add_lab_credit_hours" class="form-label small">Lab Credit Hours</label>
                                        <input type="number" name="lab_credit_hours" id="add_lab_credit_hours" class="form-control form-control-sm" min="0" value="0">
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Course Type</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="add_is_mandatory" name="is_mandatory">
                                            <label class="form-check-label ms-2" for="add_is_mandatory">Mandatory</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Third Row -->
                                <div class="col-md-4">
                                    <label for="no_of_sections" class="form-label fw-medium">Number of Sections</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-layer-group text-muted"></i></span>
                                        <input type="number" name="no_of_sections" id="no_of_sections" class="form-control" min="1" value="1">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="students_per_section" class="form-label fw-medium">Students Per Section</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-users text-muted"></i></span>
                                        <input type="number" name="students_per_section" id="students_per_section" class="form-control" min="0" value="0">
                                        <span class="input-group-text bg-light small">(0 = Unlimited)</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Course Status</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" checked>
                                            <label class="form-check-label ms-2" for="add_is_active">Active</label>
                                        </div>
                                    </div>
                                </div>

                                @if(!$program->is_semester)
                                    <!-- Timetable Configuration Section -->
                                    <div class="col-12 mt-4">
                                        <div class="border-top pt-3">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="fas fa-calendar-alt me-2"></i>Timetable Configuration
                                            </h6>

                                            <div class="col-md-4">
                                                <label for="add_weekly_lectures" class="form-label fw-medium">Weekly Lectures</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-chalkboard-teacher text-muted"></i></span>
                                                    <input type="number" name="weekly_lectures" id="add_weekly_lectures" class="form-control" min="0" value="0">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="add_weekly_labs" class="form-label fw-medium">Weekly Labs</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                                    <input type="number" name="weekly_labs" id="add_weekly_labs" class="form-control" min="0" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($program->is_semester)
                                <!-- Timetable Configuration Section -->
                                <div class="col-12 mt-4">
                                    <div class="border-top pt-3">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="fas fa-calendar-alt me-2"></i>Timetable Configuration
                                        </h6>

                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium">Lab Slot Requirement</label>
                                                <div class="d-flex align-items-center" style="height: 38px">
                                                    <div class="form-check form-switch m-0">
                                                        <input class="form-check-input" type="checkbox" id="add_requires_continuous_slots" name="requires_continuous_slots">
                                                        <label class="form-check-label ms-2" for="add_requires_continuous_slots">Continuous Slots Required</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="add_required_minutes_theory_weekly" class="form-label fw-medium">Weekly Theory Minutes</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-clock text-muted"></i></span>
                                                    <input type="number" name="required_minutes_theory_weekly" id="add_required_minutes_theory_weekly" class="form-control" min="0" value="0">
                                                </div>
                                                <small class="text-muted">Total minutes per week for theory</small>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="add_required_minutes_lab_weekly" class="form-label fw-medium">Weekly Lab Minutes</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                                    <input type="number" name="required_minutes_lab_weekly" id="add_required_minutes_lab_weekly" class="form-control" min="0" value="0">
                                                </div>
                                                <small class="text-muted">Total minutes per week for lab</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light py-3">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i>Save Course
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Edit Course Modal -->
        <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="editCourseForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="course_id" id="edit_course_id" value="">
                    <input type="hidden" name="class_id" id="edit_class_id" value="">
                    <input type="hidden" name="program_id" id="edit_program_id" value="">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-success text-white py-3">
                            <h5 class="modal-title fs-5 fw-semibold" id="editCourseModalLabel">
                                <i class="fas fa-edit me-2"></i>Edit Course
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- First Row -->
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label fw-medium">Course Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-book text-muted"></i></span>
                                        <input type="text" name="name" id="edit_name" class="form-control" placeholder="Introduction to Computer Science" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="edit_code" class="form-label fw-medium">Course Code </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                        <input type="text" name="code" id="edit_code" class="form-control" placeholder="CS101" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="edit_credit_hours" class="form-label fw-medium">Credit Hours <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-clock text-muted"></i></span>
                                        <input type="number" name="credit_hours" id="edit_credit_hours" class="form-control" min="0" value="0" required>
                                    </div>
                                </div>

                                <!-- Second Row -->
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Lab Settings</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="edit_has_lab" name="has_lab">
                                            <label class="form-check-label ms-2" for="edit_has_lab">Enable Lab</label>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="edit_labHoursGroup" style="display: none;">
                                        <label for="edit_lab_credit_hours" class="form-label small">Lab Credit Hours</label>
                                        <input type="number" name="lab_credit_hours" id="edit_lab_credit_hours" class="form-control form-control-sm" min="0" value="0">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Course Type</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="edit_is_mandatory" name="is_mandatory">
                                            <label class="form-check-label ms-2" for="edit_is_mandatory">Mandatory</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Course Status</label>
                                    <div class="d-flex align-items-center" style="height: 38px">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" checked>
                                            <label class="form-check-label ms-2" for="edit_is_active">Active</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Third Row -->
                                <div class="col-md-4">
                                    <label for="edit_no_of_sections" class="form-label fw-medium">Number of Sections</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-layer-group text-muted"></i></span>
                                        <input type="number" name="no_of_sections" id="edit_no_of_sections" class="form-control" min="1" value="1">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_students_per_section" class="form-label fw-medium">Students Per Section</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-users text-muted"></i></span>
                                        <input type="number" name="students_per_section" id="edit_students_per_section" class="form-control" min="0" value="0">
                                        <span class="input-group-text bg-light small">(0 = Unlimited)</span>
                                    </div>
                                </div>

                                @if(!$program->is_semester)
                                    <!-- Timetable Configuration Section -->
                                    <div class="col-12 mt-4">
                                        <div class="border-top pt-3">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="fas fa-calendar-alt me-2"></i>Timetable Configuration
                                            </h6>

                                            <div class="col-md-4">
                                                <label for="edit_weekly_lectures" class="form-label fw-medium">Weekly Lectures</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-chalkboard-teacher text-muted"></i></span>
                                                    <input type="number" name="weekly_lectures" id="edit_weekly_lectures" class="form-control" min="0" value="0">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="edit_weekly_labs" class="form-label fw-medium">Weekly Labs</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                                    <input type="number" name="weekly_labs" id="edit_weekly_labs" class="form-control" min="0" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($program->is_semester)
                                <!-- Timetable Configuration Section -->
                                <div class="col-12 mt-4">
                                    <div class="border-top pt-3">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="fas fa-calendar-alt me-2"></i>Timetable Configuration
                                        </h6>

                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium">Lab Slot Requirement</label>
                                                <div class="d-flex align-items-center" style="height: 38px">
                                                    <div class="form-check form-switch m-0">
                                                        <input class="form-check-input" type="checkbox" id="edit_requires_continuous_slots" name="requires_continuous_slots">
                                                        <label class="form-check-label ms-2" for="edit_requires_continuous_slots">Continuous Slots Required</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="edit_required_minutes_theory_weekly" class="form-label fw-medium">Weekly Theory Minutes</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-clock text-muted"></i></span>
                                                    <input type="number" name="required_minutes_theory_weekly" id="edit_required_minutes_theory_weekly" class="form-control" min="0" value="0">
                                                </div>
                                                <small class="text-muted">Total minutes per week for theory</small>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="edit_required_minutes_lab_weekly" class="form-label fw-medium">Weekly Lab Minutes</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-flask text-muted"></i></span>
                                                    <input type="number" name="required_minutes_lab_weekly" id="edit_required_minutes_lab_weekly" class="form-control" min="0" value="0">
                                                </div>
                                                <small class="text-muted">Total minutes per week for lab</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light py-3">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i>Update Course
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
            $('#coursesTable').DataTable({
                dom:
                    '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-end"f>>' +
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
                    searchPlaceholder: "Search courses...",
                    lengthMenu: "Show _MENU_ courses",
                    info: "Showing _START_ to _END_ of _TOTAL_ courses",
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
                        .css({ 'max-width': '250px', 'width': '100%' }); // ensures it does not cut off
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });




            // Toggle lab hours in Add Modal
            $('#add_has_lab').change(function () {
                if ($(this).is(':checked')) {
                    $('#add_labHoursGroup').show();
                } else {
                    $('#add_labHoursGroup').hide();
                    $('#add_lab_credit_hours').val('');
                }
            });

            // Toggle lab hours in Edit Modal
            $('#edit_has_lab').change(function () {
                if ($(this).is(':checked')) {
                    $('#edit_labHoursGroup').show();
                } else {
                    $('#edit_labHoursGroup').hide();
                    $('#edit_lab_credit_hours').val('');
                }
            });

            // Open Add Course Modal
            $('#addCourseBtn').click(function () {
                // Reset form
                $('#addCourseForm')[0].reset();
                $('#add_labHoursGroup').hide();
                $('#add_is_active').prop('checked', true);

                // Inject program_id and class_id
                let programId = $(this).data('program-id');
                let classId = $(this).data('class-id');
                $('#add_program_id').val(programId);
                $('#add_class_id').val(classId);

                $('#addCourseModal').modal('show');
            });



            // Open Edit Course Modal
            $('.editCourseBtn').click(function () {
                let button = $(this);

                // Extract all data attributes
                let courseId = button.data('id');
                let name = button.data('name');
                let code = button.data('code');
                let credit_hours = button.data('credit_hours');
                let has_lab = button.data('has_lab') == 1;
                let lab_credit_hours = button.data('lab_credit_hours') || '';
                let class_id = button.data('class_id');
                let program_id = button.data('program_id');
                let is_active = button.data('is_active') == 1;
                let is_mandatory = button.data('is_mandatory') == 1;
                let no_of_sections = button.data('no_of_sections') || 1;
                let students_per_section = button.data('students_per_section') || 0;
                let requires_continuous_slots = button.data('requires_continuous_slots') == 1;
                let required_minutes_theory_weekly = button.data('required_minutes_theory_weekly') || 0;
                let required_minutes_lab_weekly = button.data('required_minutes_lab_weekly') || 0;
                let weekly_lectures = button.data('weekly_lectures') || 0;
                let weekly_labs = button.data('weekly_labs') || 0;
                let is_semester_based = button.data('program_is_semester_based') == 1;

                // Fill basic course info
                $('#edit_course_id').val(courseId);
                $('#edit_name').val(name);
                $('#edit_code').val(code);
                $('#edit_credit_hours').val(credit_hours);
                $('#edit_class_id').val(class_id);
                $('#edit_program_id').val(program_id);

                // Lab settings
                $('#edit_has_lab').prop('checked', has_lab);
                $('#edit_labHoursGroup').toggle(has_lab);
                $('#edit_lab_credit_hours').val(has_lab ? lab_credit_hours : '');

                // Status and type
                $('#edit_is_active').prop('checked', is_active);
                $('#edit_is_mandatory').prop('checked', is_mandatory);

                // Sections info
                $('#edit_no_of_sections').val(no_of_sections);
                $('#edit_students_per_section').val(students_per_section);

                // Timetable configuration
                if (is_semester_based) {
                    $('#edit_requires_continuous_slots').prop('checked', requires_continuous_slots);
                    $('#edit_required_minutes_theory_weekly').val(required_minutes_theory_weekly);
                    $('#edit_required_minutes_lab_weekly').val(required_minutes_lab_weekly);
                } else {
                    $('#edit_weekly_lectures').val(weekly_lectures);
                    $('#edit_weekly_labs').val(weekly_labs);
                }

                // Update form action
                $('#editCourseForm').attr('action', '/courses/' + courseId);

                // Show modal
                $('#editCourseModal').modal('show');
            });


            $('#addCourseForm, #editCourseForm').on('submit', function () {
                $(this).find('button[type="submit"]').prop('disabled', true);
            });

            // Delete Course with SweetAlert2 confirmation
            $('.deleteCourseBtn').click(function () {
                let courseId = $(this).data('id');
                let courseName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete course: "${courseName}". This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create form dynamically to submit DELETE request
                        let form = $('<form>', {
                            'method': 'POST',
                            'action': '/courses/' + courseId
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

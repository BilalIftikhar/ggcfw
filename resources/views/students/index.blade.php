@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <style>
        .table th, .table td {
            vertical-align: middle !important;
        }
        table.dataTable thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
            font-weight: 600;
        }
        .dataTables_scrollHead {
            border-top: 1px solid #e9ecef !important;
            background-color: #f8f9fa;
        }
        .badge {
            font-size: 0.85em;
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dt-buttons {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dt-buttons {
            margin-right: 15px;
        }
        .dataTables_wrapper .table {
            margin-top: 20px !important;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.03);
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #e9ecef !important;
        }
        .img-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50% !important;
            border: 2px solid #e9ecef;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .btn-view {
            background-color: #17a2b8;
            color: white;
        }
        .btn-edit {
            background-color: #007bff;
            color: white;
        }
        .btn-courses {
            background-color: #6f42c1;
            color: white;
        }
        .btn-program {
            background-color: #fd7e14;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-sm-rounded {
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .status-badge {
            min-width: 70px;
            display: inline-block;
            text-align: center;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        .card-body {
            padding: 1.5rem;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .filter-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Students</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Students</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_student')
                <div class="mb-4">
                    <a href="{{ route('students.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Add Student
                    </a>
                </div>
            @endcan

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of Students</h5>

                    <div class="filter-section mb-4">
                        <form method="GET" action="{{ route('students.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="filter-label">Academic Session</label>
                                <select name="academic_session_id" id="academic_session_id" class="form-select">
                                    <option value="">All Sessions</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="filter-label">Study Level</label>
                                <select name="study_level_id" id="study_level_id" class="form-select" disabled>
                                    <option value="">Select Academic Session First</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="filter-label">Program</label>
                                <select name="program_id" id="program_id" class="form-select" disabled>
                                    <option value="">Select Study Level First</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="filter-label">Program Class</label>
                                <select name="program_class_id" id="program_class_id" class="form-select" {{ request('program_id') ? '' : 'disabled' }}>
                                    <option value="">Select Program First</option>
                                </select>
                            </div>

                            <div class="col-md-3 align-self-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-bordered table-hover w-100 align-middle">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="8%">Image</th>
                                <th>Name</th>
                                <th>CNIC</th>
                                <th>Reg. No</th>
                                <th>Roll No</th>
                                <th width="10%">Status</th>
                                <th>Contact</th>
                                <th width="18%">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($student->hasMedia('student'))
                                            <img src="{{ $student->getFirstMediaUrl('student') }}"
                                                 alt="{{ $student->name }}"
                                                 class="img-thumbnail">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}"
                                                 alt="Default Avatar"
                                                 class="img-thumbnail">
                                        @endif
                                    </td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->cnic }}</td>
                                    <td>{{ $student->registration_number }}</td>
                                    <td>{{ $student->roll_number }}</td>
                                    <td>
                                        <span class="badge rounded-pill status-badge bg-{{ $student->is_active ? 'success' : 'secondary' }}">
                                            {{ $student->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $student->student_contact ?? $student->parent_contact }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            @can('view_student')
                                                <a href="{{ route('students.show', $student->id) }}"
                                                   class="btn btn-view btn-sm btn-sm-rounded"
                                                   title="View Student">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endcan
                                            @can('update_student')
                                                <a href="{{ route('students.edit', $student->id) }}"
                                                   class="btn btn-edit btn-sm btn-sm-rounded"
                                                   title="Edit Student">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('students.courses.edit', $student->id) }}"
                                                   class="btn btn-courses btn-sm btn-sm-rounded"
                                                   title="Manage Courses">
                                                    <i class="bi bi-journal-bookmark"></i>
                                                </a>
                                                <a href="{{ route('students.changeProgram', $student->id) }}"
                                                   class="btn btn-program btn-sm btn-sm-rounded"
                                                   title="Change Program">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </a>
                                            @endcan
                                            @can('delete_student')
                                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="deleteForm d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-delete btn-sm btn-sm-rounded" title="Delete Student">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
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
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#studentsTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"B><"d-flex align-items-center"l>f>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-light border me-2',
                        text: '<i class="bi bi-files me-1"></i> Copy',
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-light border me-2',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                    }
                ],
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [-1, 1] }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search students...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                }
            });

            $('.deleteForm').submit(function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the student record!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            const academicSession = document.getElementById('academic_session_id');
            const studyLevel = document.getElementById('study_level_id');
            const program = document.getElementById('program_id');

            academicSession.addEventListener('change', function () {
                const sessionId = this.value;
                studyLevel.innerHTML = '<option value="">Loading...</option>';
                studyLevel.disabled = true;
                program.innerHTML = '<option value="">Select Study Level First</option>';
                program.disabled = true;

                if (sessionId) {
                    fetch(`/ajax-study-levels?academic_session_id=${sessionId}`)
                        .then(res => res.json())
                        .then(data => {
                            studyLevel.innerHTML = '<option value="">All Study Levels</option>';
                            data.forEach(level => {
                                studyLevel.innerHTML += `<option value="${level.id}" ${'{{ request('study_level_id') }}' == level.id ? 'selected' : ''}>${level.name}</option>`;
                            });
                            studyLevel.disabled = false;

                            // If there was a previously selected value, trigger change
                            if ('{{ request('study_level_id') }}') {
                                studyLevel.value = '{{ request('study_level_id') }}';
                                studyLevel.dispatchEvent(new Event('change'));
                            }
                        });
                } else {
                    studyLevel.innerHTML = '<option value="">Select Academic Session First</option>';
                }
            });

            studyLevel.addEventListener('change', function () {
                const levelId = this.value;
                program.innerHTML = '<option value="">Loading...</option>';
                program.disabled = true;

                if (levelId) {
                    fetch(`/ajax-programs?study_level_id=${levelId}`)
                        .then(res => res.json())
                        .then(data => {
                            program.innerHTML = '<option value="">All Programs</option>';
                            data.forEach(prog => {
                                program.innerHTML += `<option value="${prog.id}" ${'{{ request('program_id') }}' == prog.id ? 'selected' : ''}>${prog.name}</option>`;
                            });
                            program.disabled = false;

                            // If there was a previously selected value, trigger change
                            if ('{{ request('program_id') }}') {
                                program.value = '{{ request('program_id') }}';
                                program.dispatchEvent(new Event('change'));
                            }
                        });
                } else {
                    program.innerHTML = '<option value="">Select Study Level First</option>';
                }
            });

            program.addEventListener('change', function () {
                const programId = this.value;
                const programClass = document.getElementById('program_class_id');
                programClass.innerHTML = '<option value="">Loading...</option>';
                programClass.disabled = true;

                if (programId) {
                    fetch(`/program-classes?program_id=${programId}`)
                        .then(res => res.json())
                        .then(data => {
                            programClass.innerHTML = '<option value="">All Classes</option>';
                            data.forEach(cls => {
                                programClass.innerHTML += `<option value="${cls.id}" ${'{{ request('program_class_id') }}' == cls.id ? 'selected' : ''}>${cls.name}</option>`;
                            });
                            programClass.disabled = false;
                        });
                } else {
                    programClass.innerHTML = '<option value="">Select Program First</option>';
                }
            });

            // Initialize the form if there are existing values
            if (academicSession.value) {
                academicSession.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection

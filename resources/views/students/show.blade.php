@extends('layouts.app')

@section('title', $student->name . ' - Student Profile')

@section('content')
    <style>
        .profile-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
        }

        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #495057;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
            border-radius: 0.25rem 0.25rem 0 0;
            margin-right: 0.25rem;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .nav-tabs .nav-link.active {
            border-bottom: 3px solid;
            background-color: transparent !important;
            color: inherit;
        }

        .tab-primary.active {
            color: #3a7bd5;
            border-bottom-color: #3a7bd5;
        }

        .tab-info.active {
            color: #00b4db;
            border-bottom-color: #00b4db;
        }

        .tab-success.active {
            color: #11998e;
            border-bottom-color: #11998e;
        }

        .tab-warning.active {
            color: #f46b45;
            border-bottom-color: #f46b45;
        }

        .tab-secondary.active {
            color: #8e2de2;
            border-bottom-color: #8e2de2;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .section-title {
            font-size: 1.25rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
            margin-top: 0.5rem;
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #3a7bd5, #00b4db);
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed #e9ecef;
            font-size: 0.95rem;
        }

        .social-links .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .tab-content {
            padding: 1.5rem 0;
        }

        .credentials-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .credentials-col {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
        }
    </style>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Student Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">{{ $student->name }}</li>
                </ol>
            </nav>
        </div>

        <section class="section profile">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            @if($student->getFirstMediaUrl('student'))
                                <img src="{{ $student->getFirstMediaUrl('student') }}"
                                     alt="{{ $student->name }}"
                                     class="rounded-circle profile-img">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}"
                                     alt="Default Avatar"
                                     class="rounded-circle profile-img">
                            @endif
                            <h2 class="mt-3 mb-1">{{ $student->name }}</h2>
                            <h5 class="text-muted">{{ $student->program->name ?? 'No Program' }}</h5>
                            <div class="social-links mt-2">
                                <span class="badge bg-{{ $student->is_active ? 'success' : 'secondary' }}">
                                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="badge bg-info">
                                    {{ ucfirst($student->status) }}
                                </span>
                                <span class="badge bg-primary">
                                    {{ $student->studyLevel->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Login Credentials Section -->
                    @if($student->user)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="section-title"><i class="fas fa-key me-2"></i>Login Credentials</h5>
                                <div class="credentials-row">
                                    <div class="credentials-col">
                                        <div class="info-label">Username</div>
                                        <div class="info-value">{{ $student->user->username }}</div>
                                    </div>
                                    <div class="credentials-col">
                                        <div class="info-label">Role</div>
                                        <div class="info-value">{{ $student->user->roles->first()->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body pt-3">
                            <!-- Bordered Tabs -->
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <button class="nav-link active tab-primary" data-bs-toggle="tab" data-bs-target="#personal-info">
                                        <i class="fas fa-user me-1"></i> Personal
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link tab-info" data-bs-toggle="tab" data-bs-target="#contact-details">
                                        <i class="fas fa-address-book me-1"></i> Contact
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link tab-success" data-bs-toggle="tab" data-bs-target="#academic-details">
                                        <i class="fas fa-graduation-cap me-1"></i> Academic
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link tab-warning" data-bs-toggle="tab" data-bs-target="#education-history">
                                        <i class="fas fa-history me-1"></i> Education
                                    </button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#enrollment-history">
                                        <i class="fas fa-user-graduate me-1"></i> Enrollments
                                    </button>
                                </li>

                            </ul>

                            <div class="tab-content pt-3">

                                <!-- Personal Info Tab -->
                                <div class="tab-pane fade show active" id="personal-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Full Name</div>
                                            <div class="info-value">{{ $student->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Father's Name</div>
                                            <div class="info-value">{{ $student->father_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">CNIC/B-Form</div>
                                            <div class="info-value">{{ $student->cnic }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Father's CNIC</div>
                                            <div class="info-value">{{ $student->father_cnic ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date of Birth</div>
                                            <div class="info-value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Gender</div>
                                            <div class="info-value">{{ ucfirst($student->gender) }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Religion</div>
                                            <div class="info-value">{{ $student->religion ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Blood Group</div>
                                            <div class="info-value">{{ $student->blood_group ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Hafiz-e-Quran</div>
                                            <div class="info-value">{{ $student->is_hafiz ? 'Yes' : 'No' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Father Employed</div>
                                            <div class="info-value">{{ $student->father_job ? 'Yes' : 'No' }}</div>
                                        </div>
                                        @if($student->father_job)
                                            <div class="col-md-6">
                                                <div class="info-label">Father's Department</div>
                                                <div class="info-value">{{ $student->father_department ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Father's Designation</div>
                                                <div class="info-value">{{ $student->father_designation ?? 'N/A' }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Contact Details Tab -->
                                <div class="tab-pane fade" id="contact-details">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-label">Address</div>
                                            <div class="info-value">{{ $student->address ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Student Contact</div>
                                            <div class="info-value">{{ $student->student_contact ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Parent Contact</div>
                                            <div class="info-value">{{ $student->parent_contact ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">WhatsApp Number</div>
                                            <div class="info-value">{{ $student->whatsapp_no ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Email</div>
                                            <div class="info-value">{{ $student->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Details Tab -->
                                <div class="tab-pane fade" id="academic-details">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Registration Number</div>
                                            <div class="info-value">{{ $student->registration_number }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Roll Number</div>
                                            <div class="info-value">{{ $student->roll_number }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Academic Session</div>
                                            <div class="info-value">{{ $student->academicSession->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Study Level</div>
                                            <div class="info-value">{{ $student->studyLevel->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Program</div>
                                            <div class="info-value">{{ $student->program->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Status</div>
                                            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $student->status)) }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Is Active</div>
                                            <div class="info-value">{{ $student->is_active ? 'Yes' : 'No' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Education History Tab -->
                                <div class="tab-pane fade" id="education-history">
                                    <h6 class="fw-bold">Matriculation (Secondary School)</h6>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="info-label">Passing Year</div>
                                            <div class="info-value">{{ $student->matric_passing_year ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Roll Number</div>
                                            <div class="info-value">{{ $student->matric_roll_no ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Board</div>
                                            <div class="info-value">{{ $student->matric_board ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Group</div>
                                            <div class="info-value">{{ $student->matric_group ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Obtained Marks</div>
                                            <div class="info-value">{{ $student->matric_obtained_marks ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Total Marks</div>
                                            <div class="info-value">{{ $student->matric_total_marks ?? 'N/A' }}</div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold">Intermediate (Higher Secondary)</h6>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="info-label">Passing Year</div>
                                            <div class="info-value">{{ $student->inter_passing_year ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Roll Number</div>
                                            <div class="info-value">{{ $student->inter_roll_no ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Board</div>
                                            <div class="info-value">{{ $student->inter_board ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Group</div>
                                            <div class="info-value">{{ $student->inter_group ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Obtained Marks</div>
                                            <div class="info-value">{{ $student->inter_obtained_marks ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Total Marks</div>
                                            <div class="info-value">{{ $student->inter_total_marks ?? 'N/A' }}</div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold">Graduation</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-label">Passing Year</div>
                                            <div class="info-value">{{ $student->grad_passing_year ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Registration Number</div>
                                            <div class="info-value">{{ $student->grad_reg_no ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Board/University</div>
                                            <div class="info-value">{{ $student->grad_board ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Group/Subject</div>
                                            <div class="info-value">{{ $student->grad_group ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Obtained Marks</div>
                                            <div class="info-value">{{ $student->grad_obtained_marks ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Total Marks</div>
                                            <div class="info-value">{{ $student->grad_total_marks ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="enrollment-history">
                                    <h6 class="fw-bold">Enrollment History</h6>

                                    <!-- Active Enrollments Section -->
                                    <div class="mb-4">
                                        <h5 class="text-success mb-3">Active Enrollments</h5>
                                        @forelse ($student->enrollments->where('status', 'enrolled') as $enrollment)
                                            @include('students.partials.enrollment-accordion', ['enrollment' => $enrollment])
                                        @empty
                                            <p class="text-muted">No active enrollments found.</p>
                                        @endforelse
                                    </div>

                                    <!-- Completed Enrollments Section -->
                                    <div class="mb-4">
                                        <h5 class="text-primary mb-3">Completed Enrollments</h5>
                                        @forelse ($student->enrollments->where('status', 'completed') as $enrollment)
                                            @include('students.partials.enrollment-accordion', ['enrollment' => $enrollment])
                                        @empty
                                            <p class="text-muted">No completed enrollments found.</p>
                                        @endforelse
                                    </div>

                                    <!-- Cancelled Enrollments Section -->
                                    <div class="mb-4">
                                        <h5 class="text-danger mb-3">Cancelled Enrollments</h5>
                                        @forelse ($student->enrollments->where('status', 'cancelled') as $enrollment)
                                            @include('students.partials.enrollment-accordion', ['enrollment' => $enrollment])
                                        @empty
                                            <p class="text-muted">No cancelled enrollments found.</p>
                                        @endforelse
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

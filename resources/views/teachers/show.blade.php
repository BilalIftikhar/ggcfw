@extends('layouts.app')

@section('title', $teacher->name . ' - Teacher Profile')

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
            <h1>Teacher Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
                    <li class="breadcrumb-item active">{{ $teacher->name }}</li>
                </ol>
            </nav>
        </div>

        <section class="section profile">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            @if($teacher->photo)
                                <img src="{{ asset('storage/'.$teacher->photo) }}"
                                     alt="{{ $teacher->name }}"
                                     class="rounded-circle profile-img">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}"
                                     alt="Default Avatar"
                                     class="rounded-circle profile-img">
                            @endif
                            <h2 class="mt-3 mb-1">{{ $teacher->name }}</h2>
                            <h5 class="text-muted">{{ $teacher->designation ?? 'No Designation' }}</h5>
                            <div class="social-links mt-2">
                                <span class="badge bg-{{ $teacher->working_status == 'working' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($teacher->working_status) }}
                                </span>
                                <span class="badge bg-info">
                                    {{ $teacher->employee_mode ? ucfirst($teacher->employee_mode) : 'N/A' }}
                                </span>
                                <span class="badge bg-primary">
                                    BPS-{{ $teacher->bps ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Login Credentials Section -->
                    @if($teacher->user)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="section-title"><i class="fas fa-key me-2"></i>Login Credentials</h5>
                                <div class="credentials-row">
                                    <div class="credentials-col">
                                        <div class="info-label">Username</div>
                                        <div class="info-value">{{ $teacher->user->username }}</div>
                                    </div>
                                    <div class="credentials-col">
                                        <div class="info-label">Role</div>
                                        <div class="info-value">{{ $teacher->user->roles->first()->name ?? 'N/A' }}</div>
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
                                    <button class="nav-link tab-success" data-bs-toggle="tab" data-bs-target="#professional-details">
                                        <i class="fas fa-briefcase me-1"></i> Professional
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link tab-warning" data-bs-toggle="tab" data-bs-target="#career-timeline">
                                        <i class="fas fa-history me-1"></i> Timeline
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content pt-3">

                                <!-- Personal Info Tab -->
                                <div class="tab-pane fade show active" id="personal-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Full Name</div>
                                            <div class="info-value">{{ $teacher->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Father's Name</div>
                                            <div class="info-value">{{ $teacher->father_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">CNIC No</div>
                                            <div class="info-value">{{ $teacher->cnic }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Seniority No</div>
                                            <div class="info-value">{{ $teacher->seniority_no ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date of Birth</div>
                                            <div class="info-value">{{ $teacher->dob ? \Carbon\Carbon::parse($teacher->dob)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Domicile</div>
                                            <div class="info-value">{{ $teacher->domicile ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Details Tab -->
                                <div class="tab-pane fade" id="contact-details">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-label">Home Address</div>
                                            <div class="info-value">{{ $teacher->home_address ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Work Contact</div>
                                            <div class="info-value">{{ $teacher->work_contact ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Home Contact</div>
                                            <div class="info-value">{{ $teacher->home_contact ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Details Tab -->
                                <div class="tab-pane fade" id="professional-details">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Designation</div>
                                            <div class="info-value">{{ $teacher->designation ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">BPS Scale</div>
                                            <div class="info-value">{{ $teacher->bps ? 'BPS-'.$teacher->bps : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Subject</div>
                                            <div class="info-value">{{ $teacher->subject ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Qualification</div>
                                            <div class="info-value">{{ $teacher->qualification ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Employee Mode</div>
                                            <div class="info-value">{{ $teacher->employee_mode ? ucfirst($teacher->employee_mode) : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Quota</div>
                                            <div class="info-value">{{ $teacher->quota ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Cadre</div>
                                            <div class="info-value">{{ $teacher->cadre ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Working Status</div>
                                            <div class="info-value">{{ ucfirst($teacher->working_status) }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Retirement Date</div>
                                            <div class="info-value">{{ $teacher->retirement_date ? \Carbon\Carbon::parse($teacher->retirement_date)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Career Timeline Tab -->
                                <div class="tab-pane fade" id="career-timeline">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Govt. Entry Date</div>
                                            <div class="info-value">{{ $teacher->govt_entry_date ? \Carbon\Carbon::parse($teacher->govt_entry_date)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Adhoc Lecturer Joining Date</div>
                                            <div class="info-value">{{ $teacher->joining_date_adhoc_lecturer ? \Carbon\Carbon::parse($teacher->joining_date_adhoc_lecturer)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Regular Lecturer Joining Date</div>
                                            <div class="info-value">{{ $teacher->joining_date_regular_lecturer ? \Carbon\Carbon::parse($teacher->joining_date_regular_lecturer)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Assistant Professor Joining Date</div>
                                            <div class="info-value">{{ $teacher->joining_date_assistant_prof ? \Carbon\Carbon::parse($teacher->joining_date_assistant_prof)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Associate Professor Joining Date</div>
                                            <div class="info-value">{{ $teacher->joining_date_associate_prof ? \Carbon\Carbon::parse($teacher->joining_date_associate_prof)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Professor Joining Date</div>
                                            <div class="info-value">{{ $teacher->joining_date_professor ? \Carbon\Carbon::parse($teacher->joining_date_professor)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Principal Joining Date</div>
                                            <div class="info-value">{{ $teacher->joining_date_principal ? \Carbon\Carbon::parse($teacher->joining_date_principal)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Present Station Joining Date</div>
                                            <div class="info-value">{{ $teacher->joining_date_present_station ? \Carbon\Carbon::parse($teacher->joining_date_present_station)->format('d M, Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Qualifying Service</div>
                                            <div class="info-value">{{ $teacher->qualifying_service ?? 'N/A' }}</div>
                                        </div>
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

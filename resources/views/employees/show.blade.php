@extends('layouts.app')

@section('title', $employee->name . ' - Employee Profile')

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
            <h1>Employee Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item active">{{ $employee->name }}</li>
                </ol>
            </nav>
        </div>

        <section class="section profile">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            @if($employee->hasMedia('employee'))
                                <img src="{{ $employee->getFirstMediaUrl('employee') }}"
                                     alt="{{ $employee->name }}"
                                     class="rounded-circle profile-img">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}"
                                     alt="Default Avatar"
                                     class="rounded-circle profile-img">
                            @endif
                            <h2 class="mt-3 mb-1">{{ $employee->name }}</h2>
                            <h5 class="text-muted">{{ $employee->designation ?? 'No Designation' }}</h5>
                            <div class="social-links mt-2">
                                <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="badge bg-info">
                                    {{ $employee->status ?? 'N/A' }}
                                </span>
                                <span class="badge bg-primary">
                                    BPS-{{ $employee->bps ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Login Credentials Section -->
                    @if($employee->user)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="section-title"><i class="fas fa-key me-2"></i>Login Credentials</h5>
                                <div class="credentials-row">
                                    <div class="credentials-col">
                                        <div class="info-label">Username</div>
                                        <div class="info-value">{{ $employee->user->username }}</div>
                                    </div>
                                    <div class="credentials-col">
                                        <div class="info-label">Role</div>
                                        <div class="info-value">{{ $employee->user->roles->first()->name ?? 'N/A' }}</div>
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
                                    <button class="nav-link tab-success" data-bs-toggle="tab" data-bs-target="#employment-details">
                                        <i class="fas fa-briefcase me-1"></i> Employment
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link tab-warning" data-bs-toggle="tab" data-bs-target="#career-timeline">
                                        <i class="fas fa-history me-1"></i> Timeline
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link tab-secondary" data-bs-toggle="tab" data-bs-target="#position-history">
                                        <i class="fas fa-user-tie me-1"></i> Positions
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content pt-3">

                                <!-- Personal Info Tab -->
                                <div class="tab-pane fade show active" id="personal-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Full Name</div>
                                            <div class="info-value">{{ $employee->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Father's Name</div>
                                            <div class="info-value">{{ $employee->father_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">CNIC No</div>
                                            <div class="info-value">{{ $employee->cnic_no }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date of Birth</div>
                                            <div class="info-value">{{ $employee->date_of_birth?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Domicile</div>
                                            <div class="info-value">{{ $employee->domicile ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Qualification</div>
                                            <div class="info-value">{{ $employee->qualification ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Details Tab -->
                                <div class="tab-pane fade" id="contact-details">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-label">Home Address</div>
                                            <div class="info-value">{{ $employee->home_address ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Work Contact</div>
                                            <div class="info-value">{{ $employee->work_contact ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Home Contact</div>
                                            <div class="info-value">{{ $employee->home_contact ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employment Details Tab -->
                                <div class="tab-pane fade" id="employment-details">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Designation</div>
                                            <div class="info-value">{{ $employee->designation ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Cadre</div>
                                            <div class="info-value">{{ $employee->cadre ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">BPS Scale</div>
                                            <div class="info-value">{{ $employee->bps ? 'BPS-'.$employee->bps : 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Status</div>
                                            <div class="info-value">{{ $employee->status ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Working Status</div>
                                            <div class="info-value">{{ $employee->working_status ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Quota</div>
                                            <div class="info-value">{{ $employee->quota ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Career Timeline Tab -->
                                <div class="tab-pane fade" id="career-timeline">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">First Govt. Entry Date</div>
                                            <div class="info-value">{{ $employee->date_of_first_entry?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Contract Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_contract?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Regular Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_regular?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Current Station Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_current_station?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Retirement Date</div>
                                            <div class="info-value">{{ $employee->date_of_retirement?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Qualifying Service</div>
                                            <div class="info-value">{{ $employee->qualifying_service ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Position History Tab -->
                                <div class="tab-pane fade" id="position-history">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Junior Clerk Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_junior_clerk?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Senior Clerk Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_senior_clerk?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Lab Supervisor Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_lab_supervisor?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Head Clerk Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_head_clerk?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Superintendent Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_superintendent?->format('d M, Y') ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Senior Bursar Joining Date</div>
                                            <div class="info-value">{{ $employee->date_of_joining_senior_bursar?->format('d M, Y') ?? 'N/A' }}</div>
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

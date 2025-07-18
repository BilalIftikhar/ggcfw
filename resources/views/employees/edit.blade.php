@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Employee: {{ $employee->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div id="employeeFields" class="row" style="margin-top: 20px;">

                        <!-- Personal Information Section -->
                        <div class="col-12">
                            <div class="section-header bg-primary text-white p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ old('name', $employee->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Father's Name</label>
                                        <input type="text" name="father_name" value="{{ old('father_name', $employee->father_name) }}" class="form-control @error('father_name') is-invalid @enderror" placeholder="Father's Full Name">
                                        @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">CNIC <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="cnic_no"
                                            value="{{ old('cnic_no', $employee->cnic_no) }}"
                                            class="form-control @error('cnic_no') is-invalid @enderror"
                                            placeholder="13 digits, no dashes"
                                            maxlength="13"
                                            inputmode="numeric"
                                            pattern="\d*"
                                            oninput="this.value = this.value.replace(/\D/g, '')"
                                            id="cnicInput"
                                        >
                                        @error('cnic_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Date of Birth</label>
                                        <input
                                            type="date"
                                            name="date_of_birth"
                                            value="{{ old('date_of_birth', optional($employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            min="1900-01-01"
                                        >
                                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Domicile</label>
                                        <input type="text" name="domicile" value="{{ old('domicile', $employee->domicile) }}" class="form-control @error('domicile') is-invalid @enderror" placeholder="e.g., Punjab">
                                        @error('domicile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Qualification</label>
                                        <input type="text" name="qualification" value="{{ old('qualification', $employee->qualification) }}" class="form-control @error('qualification') is-invalid @enderror" placeholder="Highest qualification">
                                        @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Employee Photo</label>
                                        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        @if($employee->getFirstMedia('employee'))
                                            <div class="mt-2">
                                                <img src="{{ $employee->getFirstMediaUrl('employee') }}" alt="Employee Photo" class="img-thumbnail" width="100">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="remove_photo" id="remove_photo" value="1">
                                                    <label class="form-check-label" for="remove_photo">
                                                        Remove current photo
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details Section -->
                        <div class="col-12">
                            <div class="section-header bg-info text-white p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-address-book me-2"></i>Contact Details</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Home Address</label>
                                        <input type="text" name="home_address" value="{{ old('home_address', $employee->home_address) }}" class="form-control @error('home_address') is-invalid @enderror" placeholder="Full Address">
                                        @error('home_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Work Contact</label>
                                        <input
                                            type="text"
                                            name="work_contact"
                                            value="{{ old('work_contact', $employee->work_contact) }}"
                                            class="form-control @error('work_contact') is-invalid @enderror"
                                            placeholder="Office number"
                                            maxlength="12"
                                            inputmode="numeric"
                                            pattern="\d*"
                                            oninput="this.value = this.value.replace(/\D/g, '')"
                                        >
                                        @error('work_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Home Contact</label>
                                        <input
                                            type="text"
                                            name="home_contact"
                                            value="{{ old('home_contact', $employee->home_contact) }}"
                                            class="form-control @error('home_contact') is-invalid @enderror"
                                            placeholder="Personal number"
                                            maxlength="12"
                                            inputmode="numeric"
                                            pattern="\d*"
                                            oninput="this.value = this.value.replace(/\D/g, '')"
                                        >
                                        @error('home_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Details Section -->
                        <div class="col-12">
                            <div class="section-header bg-success text-white p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Employment Details</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Designation</label>
                                        <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" class="form-control @error('designation') is-invalid @enderror" placeholder="Current Designation">
                                        @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Cadre</label>
                                        <input type="text" name="cadre" value="{{ old('cadre', $employee->cadre) }}" class="form-control @error('cadre') is-invalid @enderror" placeholder="Cadre">
                                        @error('cadre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">BPS</label>
                                        <select name="bps" class="form-select @error('bps') is-invalid @enderror">
                                            <option value="">Select BPS</option>
                                            @for($i = 1; $i <= 22; $i++)
                                                <option value="{{ $i }}" {{ old('bps', $employee->bps) == $i ? 'selected' : '' }}>BPS-{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('bps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="Regular" {{ old('status', $employee->status) == 'Regular' ? 'selected' : '' }}>Regular</option>
                                            <option value="Contract" {{ old('status', $employee->status) == 'Contract' ? 'selected' : '' }}>Contract</option>
                                        </select>
                                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Working Status</label>
                                        <select name="working_status" class="form-select @error('working_status') is-invalid @enderror">
                                            <option value="working" {{ old('working_status', $employee->working_status) == 'working' ? 'selected' : '' }}>Working</option>
                                            <option value="retired" {{ old('working_status', $employee->working_status) == 'retired' ? 'selected' : '' }}>Retired</option>
                                            <option value="fired" {{ old('working_status', $employee->working_status) == 'fired' ? 'selected' : '' }}>Fired</option>
                                            <option value="other" {{ old('working_status', $employee->working_status) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('working_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Quota</label>
                                        <select name="quota" class="form-select @error('quota') is-invalid @enderror">
                                            <option value="">Select Quota</option>
                                            <option value="Open Merit" {{ old('quota', $employee->quota) == 'Open Merit' ? 'selected' : '' }}>Open Merit</option>
                                            <option value="Provincial" {{ old('quota', $employee->quota) == 'Provincial' ? 'selected' : '' }}>Provincial</option>
                                            <option value="Disabled" {{ old('quota', $employee->quota) == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                            <option value="Minority" {{ old('quota', $employee->quota) == 'Minority' ? 'selected' : '' }}>Minority</option>
                                        </select>
                                        @error('quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Active Status</label>
                                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                            <option value="1" {{ old('is_active', $employee->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('is_active', $employee->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Career Timeline Section -->
                        <div class="col-12">
                            <div class="section-header bg-warning text-dark p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Career Timeline</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">First Govt. Entry Date</label>
                                        <input
                                            type="date"
                                            name="date_of_first_entry"
                                            value="{{ old('date_of_first_entry', optional($employee->date_of_first_entry ? \Carbon\Carbon::parse($employee->date_of_first_entry) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_first_entry') is-invalid @enderror"
                                        >
                                        @error('date_of_first_entry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Contract Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_contract"
                                            value="{{ old('date_of_joining_contract', optional($employee->date_of_joining_contract ? \Carbon\Carbon::parse($employee->date_of_joining_contract) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_contract') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_contract') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Regular Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_regular"
                                            value="{{ old('date_of_joining_regular', optional($employee->date_of_joining_regular ? \Carbon\Carbon::parse($employee->date_of_joining_regular) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_regular') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_regular') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Current Station Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_current_station"
                                            value="{{ old('date_of_joining_current_station', optional($employee->date_of_joining_current_station ? \Carbon\Carbon::parse($employee->date_of_joining_current_station) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_current_station') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_current_station') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Retirement Date</label>
                                        <input
                                            type="date"
                                            name="date_of_retirement"
                                            value="{{ old('date_of_retirement', optional($employee->date_of_retirement ? \Carbon\Carbon::parse($employee->date_of_retirement) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_retirement') is-invalid @enderror"
                                        >
                                        @error('date_of_retirement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Qualifying Service</label>
                                        <input
                                            type="text"
                                            name="qualifying_service"
                                            value="{{ old('qualifying_service', $employee->qualifying_service) }}"
                                            class="form-control @error('qualifying_service') is-invalid @enderror"
                                            placeholder="Format: 25.3.10 (years.months.days)"
                                        >
                                        @error('qualifying_service') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Position History Section -->
                        <div class="col-12">
                            <div class="section-header bg-secondary text-white p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Position History</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Junior Clerk Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_junior_clerk"
                                            value="{{ old('date_of_joining_junior_clerk', optional($employee->date_of_joining_junior_clerk ? \Carbon\Carbon::parse($employee->date_of_joining_junior_clerk) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_junior_clerk') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_junior_clerk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Senior Clerk Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_senior_clerk"
                                            value="{{ old('date_of_joining_senior_clerk', optional($employee->date_of_joining_senior_clerk ? \Carbon\Carbon::parse($employee->date_of_joining_senior_clerk) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_senior_clerk') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_senior_clerk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Lab Supervisor Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_lab_supervisor"
                                            value="{{ old('date_of_joining_lab_supervisor', optional($employee->date_of_joining_lab_supervisor ? \Carbon\Carbon::parse($employee->date_of_joining_lab_supervisor) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_lab_supervisor') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_lab_supervisor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Head Clerk Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_head_clerk"
                                            value="{{ old('date_of_joining_head_clerk', optional($employee->date_of_joining_head_clerk ? \Carbon\Carbon::parse($employee->date_of_joining_head_clerk) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_head_clerk') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_head_clerk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Superintendent Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_superintendent"
                                            value="{{ old('date_of_joining_superintendent', optional($employee->date_of_joining_superintendent ? \Carbon\Carbon::parse($employee->date_of_joining_superintendent) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_superintendent') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_superintendent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Senior Bursar Joining Date</label>
                                        <input
                                            type="date"
                                            name="date_of_joining_senior_bursar"
                                            value="{{ old('date_of_joining_senior_bursar', optional($employee->date_of_joining_senior_bursar ? \Carbon\Carbon::parse($employee->date_of_joining_senior_bursar) : null)->format('Y-m-d')) }}"
                                            class="form-control @error('date_of_joining_senior_bursar') is-invalid @enderror"
                                        >
                                        @error('date_of_joining_senior_bursar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 mt-4">
                            <div class="d-flex gap-2 justify-content-start">
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Update Employee
                                </button>
                                <a href="{{ route('employees.index') }}" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

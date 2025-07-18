@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Teacher: {{ $teacher->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div id="teachingFields" class="row" style="margin-top: 20px;">

                    <!-- Personal Information Section -->
                    <div class="col-12">
                        <div class="section-header bg-primary text-white p-2 rounded-top">
                            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Personal Information</h5>
                        </div>
                        <div class="border p-3 rounded-bottom mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Father's Name</label>
                                    <input type="text" name="father_name" value="{{ old('father_name', $teacher->father_name) }}" class="form-control @error('father_name') is-invalid @enderror" placeholder="Father's Full Name">
                                    @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">CNIC <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="cnic"
                                        value="{{ old('cnic', $teacher->cnic) }}"
                                        class="form-control @error('cnic') is-invalid @enderror"
                                        placeholder="13 digits, no dashes"
                                        maxlength="13"
                                        inputmode="numeric"
                                        pattern="\d*"
                                        oninput="this.value = this.value.replace(/\D/g, '')"
                                        id="cnicInput"
                                    >
                                    @error('cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Seniority No</label>
                                    <input
                                        type="text"
                                        name="seniority_no"
                                        value="{{ old('seniority_no', $teacher->seniority_no) }}"
                                        class="form-control @error('seniority_no') is-invalid @enderror"
                                        placeholder="Seniority Number"
                                    >
                                    @error('seniority_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Date of Birth</label>
                                    <input
                                        type="date"
                                        name="dob"
                                        value="{{ old('dob', optional($teacher->dob ? \Carbon\Carbon::parse($teacher->dob) : null)->format('Y-m-d')) }}"
                                        class="form-control @error('dob') is-invalid @enderror"
                                        min="1900-01-01"
                                    >
                                    @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Domicile</label>
                                    <input type="text" name="domicile" value="{{ old('domicile', $teacher->domicile) }}" class="form-control @error('domicile') is-invalid @enderror" placeholder="e.g., Punjab">
                                    @error('domicile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Teacher Photo</label>
                                    <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @if($teacher->getFirstMediaUrl('employee'))
                                    <div class="mt-2">
                                        <img src="{{ $teacher->getFirstMediaUrl('employee') }}" alt="Current Photo" class="img-thumbnail" style="max-height: 100px;">
{{--                                        <div class="form-check mt-2">--}}
{{--                                            <input class="form-check-input" type="checkbox" name="remove_photo" id="remove_photo">--}}
{{--                                            <label class="form-check-label text-danger" for="remove_photo">--}}
{{--                                                Remove current photo--}}
{{--                                            </label>--}}
{{--                                        </div>--}}
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
                                    <input type="text" name="home_address" value="{{ old('home_address', $teacher->home_address) }}" class="form-control @error('home_address') is-invalid @enderror" placeholder="Full Address">
                                    @error('home_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold">WhatsApp Number</label>
                                    <input
                                        type="text"
                                        name="work_contact"
                                        value="{{ old('work_contact', $teacher->work_contact) }}"
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
                                        value="{{ old('home_contact', $teacher->home_contact) }}"
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

                    <!-- Professional Details Section -->
                    <div class="col-12">
                        <div class="section-header bg-success text-white p-2 rounded-top">
                            <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Professional Details</h5>
                        </div>
                        <div class="border p-3 rounded-bottom mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Designation <span class="text-danger">*</span></label>
                                    <select name="designation" class="form-select @error('designation') is-invalid @enderror">
                                        <option value="">Select Designation</option>
                                        <option value="Lecturer" {{ old('designation', $teacher->designation) == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                                        <option value="Assistant Professor" {{ old('designation', $teacher->designation) == 'Assistant Professor' ? 'selected' : '' }}>Assistant Professor</option>
                                        <option value="Associate Professor" {{ old('designation', $teacher->designation) == 'Associate Professor' ? 'selected' : '' }}>Associate Professor</option>
                                        <option value="Professor" {{ old('designation', $teacher->designation) == 'Professor' ? 'selected' : '' }}>Professor</option>
                                        <option value="Principal" {{ old('designation', $teacher->designation) == 'Principal' ? 'selected' : '' }}>Principal</option>
                                    </select>
                                    @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">BPS</label>
                                    <select name="bps" class="form-select @error('bps') is-invalid @enderror">
                                        <option value="">Select BPS</option>
                                        @for($i = 1; $i <= 22; $i++)
                                        <option value="{{ $i }}" {{ old('bps', $teacher->bps) == $i ? 'selected' : '' }}>BPS-{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('bps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Subject</label>
                                    <input type="text" name="subject" value="{{ old('subject', $teacher->subject) }}" class="form-control @error('subject') is-invalid @enderror" placeholder="Subject specialization">
                                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Qualification</label>
                                    <input type="text" name="qualification" value="{{ old('qualification', $teacher->qualification) }}" class="form-control @error('qualification') is-invalid @enderror" placeholder="Highest qualification">
                                    @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Employee Mode</label>
                                    <select name="employee_mode" class="form-select @error('employee_mode') is-invalid @enderror">
                                        <option value="">Select Mode</option>
                                        <option value="regular" {{ old('employee_mode', $teacher->employee_mode) == 'regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="contract" {{ old('employee_mode', $teacher->employee_mode) == 'contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="adhoc" {{ old('employee_mode', $teacher->employee_mode) == 'adhoc' ? 'selected' : '' }}>Adhoc</option>
                                    </select>
                                    @error('employee_mode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Quota</label>
                                    <select name="quota" class="form-select @error('quota') is-invalid @enderror">
                                        <option value="">Select Quota</option>
                                        <option value="Open Merit" {{ old('quota', $teacher->quota) == 'Open Merit' ? 'selected' : '' }}>Open Merit</option>
                                        <option value="Provincial" {{ old('quota', $teacher->quota) == 'Provincial' ? 'selected' : '' }}>Provincial</option>
                                        <option value="Disabled" {{ old('quota', $teacher->quota) == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                        <option value="Minority" {{ old('quota', $teacher->quota) == 'Minority' ? 'selected' : '' }}>Minority</option>
                                    </select>
                                    @error('quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cadre</label>
                                    <input type="text" name="cadre" value="{{ old('cadre', $teacher->cadre) }}" class="form-control @error('cadre') is-invalid @enderror" placeholder="Cadre">
                                    @error('cadre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Retirement Date</label>
                                    <input type="date" name="retirement_date"
                                           value="{{ old('retirement_date', $teacher->retirement_date ? \Carbon\Carbon::parse($teacher->retirement_date)->format('Y-m-d') : '') }}"
                                           class="form-control @error('retirement_date') is-invalid @enderror">

                                    @error('retirement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Working Status</label>
                                    <select name="working_status" class="form-select @error('working_status') is-invalid @enderror">
                                        <option value="working" {{ old('working_status', $teacher->working_status) == 'working' ? 'selected' : '' }}>Working</option>
                                        <option value="retired" {{ old('working_status', $teacher->working_status) == 'retired' ? 'selected' : '' }}>Retired</option>
                                        <option value="fired" {{ old('working_status', $teacher->working_status) == 'fired' ? 'selected' : '' }}>Fired</option>
                                        <option value="other" {{ old('working_status', $teacher->working_status) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('working_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                    <label class="form-label fw-bold">Govt. Entry Date</label>
                                    <input type="date" name="govt_entry_date"
                                           value="{{ old('govt_entry_date', $teacher->govt_entry_date ? \Carbon\Carbon::parse($teacher->govt_entry_date)->format('Y-m-d') : '') }}"
                                           class="form-control @error('govt_entry_date') is-invalid @enderror">
                                    @error('govt_entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Adhoc Lecturer Joining Date</label>
                                    <input type="date" name="joining_date_adhoc_lecturer"
                                           value="{{ old('joining_date_adhoc_lecturer', $teacher->joining_date_adhoc_lecturer ? \Carbon\Carbon::parse($teacher->joining_date_adhoc_lecturer)->format('Y-m-d') : '') }}"
                                           class="form-control @error('joining_date_adhoc_lecturer') is-invalid @enderror">

                                    @error('joining_date_adhoc_lecturer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Regular Lecturer Joining Date</label>
                                    <input type="date" name="joining_date_regular_lecturer"
                                           value="{{ old('joining_date_regular_lecturer', $teacher->joining_date_regular_lecturer ? \Carbon\Carbon::parse($teacher->joining_date_regular_lecturer)->format('Y-m-d') : '') }}"
                                           class="form-control @error('joining_date_regular_lecturer') is-invalid @enderror">
                                    @error('joining_date_regular_lecturer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Assistant Professor Joining Date</label>
                                    <input type="date" name="joining_date_assistant_prof"
                                           value="{{ old('joining_date_assistant_prof', $teacher->joining_date_assistant_prof ? \Carbon\Carbon::parse($teacher->joining_date_assistant_prof)->format('Y-m-d') : '') }}"
                                           class="form-control @error('joining_date_assistant_prof') is-invalid @enderror">
                                    @error('joining_date_assistant_prof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Associate Professor Joining Date</label>
                                    <input type="date" name="joining_date_associate_prof"
                                           value="{{ old('joining_date_associate_prof', $teacher->joining_date_associate_prof ? \Carbon\Carbon::parse($teacher->joining_date_associate_prof)->format('Y-m-d') : '') }}"
                                           class="form-control @error('joining_date_associate_prof') is-invalid @enderror">
                                    @error('joining_date_associate_prof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Professor Joining Date</label>
                                    <input type="date" name="joining_date_professor"
                                           value="{{ old('joining_date_professor', $teacher->joining_date_professor ? \Carbon\Carbon::parse($teacher->joining_date_professor)->format('Y-m-d') : '') }}"
                                           class="form-control @error('joining_date_professor') is-invalid @enderror">
                                    @error('joining_date_professor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Principal Joining Date</label>
                                    <input type="date" name="joining_date_principal"
                                           value="{{ old('joining_date_principal', $teacher->joining_date_principal ? \Carbon\Carbon::parse($teacher->joining_date_principal)->format('Y-m-d') : '') }}"
                                           class="form-control @error('joining_date_principal') is-invalid @enderror">
                                    @error('joining_date_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Present Station Joining Date</label>
                                    <input type="date" name="joining_date_present_station"
                                           value="{{ old('joining_date_present_station', $teacher->joining_date_present_station ? \Carbon\Carbon::parse($teacher->joining_date_present_station)->format('Y-m-d') : '') }}"
                                           class="form-control @error('joining_date_present_station') is-invalid @enderror">
                                    @error('joining_date_present_station') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Qualifying Service</label>
                                    <input type="text" name="qualifying_service" value="{{ old('qualifying_service', $teacher->qualifying_service) }}" class="form-control @error('qualifying_service') is-invalid @enderror" placeholder="Format: 25.3.10 (years.months.days)">
                                    @error('qualifying_service') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timetable Configuration Section -->
                    <div class="col-12">
                        <div class="section-header bg-dark text-white p-2 rounded-top">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Timetable Configuration</h5>
                        </div>
                        <div class="border p-3 rounded-bottom mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="can_teach_labs" id="can_teach_labs" value="1" {{ old('can_teach_labs', $teacher->can_teach_labs) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="can_teach_labs">Can Teach Labs</label>
                                        <small class="form-text text-muted">Whether teacher can conduct lab sessions</small>
                                    </div>
                                    @error('can_teach_labs') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Max Lectures Per Day</label>
                                    <input
                                        type="number"
                                        name="max_lectures_per_day"
                                        value="{{ old('max_lectures_per_day', $teacher->max_lectures_per_day ?? 4) }}"
                                        class="form-control @error('max_lectures_per_day') is-invalid @enderror"
                                        min="1"
                                        max="8"
                                    >
                                    <small class="form-text text-muted">Default: 4 (Lecturer/Asst Prof:4, Assoc Prof:3, Professor:2)</small>
                                    @error('max_lectures_per_day') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Max Lectures Per Week</label>
                                    <input
                                        type="number"
                                        name="max_lectures_per_week"
                                        value="{{ old('max_lectures_per_week', $teacher->max_lectures_per_week ?? 24) }}"
                                        class="form-control @error('max_lectures_per_week') is-invalid @enderror"
                                        min="1"
                                        max="40"
                                    >
                                    <small class="form-text text-muted">Default: 24 (Lecturer/Asst Prof:24, Assoc Prof:18, Professor:12)</small>
                                    @error('max_lectures_per_week') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2 justify-content-between">
                            <div>
                                <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                                <button type="reset" class="btn btn-secondary ms-2">
                                    <i class="fas fa-undo me-1"></i> Reset Changes
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Update Teacher
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleLoginFields() {
        const checkbox = document.getElementById('generateLogin');
        const loginFields = document.getElementById('loginFields');
        if (checkbox.checked) {
            loginFields.style.display = 'block';
        } else {
            loginFields.style.display = 'none';
            document.getElementById('username').value = '';
            document.querySelector('input[name="password"]').value = '';
            document.querySelector('select[name="role"]').value = '';
        }
    }

    function generateUsername() {
        const cnic = document.getElementById('cnicInput').value;
        if (cnic) {
            const username = cnic;
            document.getElementById('username').value = username;
        } else {
            alert('Please enter CNIC first');
        }
    }
</script>
@endsection

@extends('layouts.app')
@section('title', 'Edit Student')
@section('content')
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Student: {{ $student->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div id="studentFields" class="row" style="margin-top: 20px;">

                        <!-- Personal Information Section -->
                        <div class="col-12">
                            <div class="section-header bg-primary text-white p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Personal Information</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ old('name', $student->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Father's Name <span class="text-danger">*</span></label>
                                        <input type="text" name="father_name" value="{{ old('father_name', $student->father_name) }}" class="form-control @error('father_name') is-invalid @enderror" placeholder="Father's Full Name">
                                        @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">CNIC/B-Form <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="cnic"
                                            value="{{ old('cnic', $student->cnic) }}"
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
                                        <label class="form-label fw-bold">Father's CNIC</label>
                                        <input
                                            type="text"
                                            name="father_cnic"
                                            value="{{ old('father_cnic', $student->father_cnic) }}"
                                            class="form-control @error('father_cnic') is-invalid @enderror"
                                            placeholder="13 digits, no dashes"
                                            maxlength="13"
                                            inputmode="numeric"
                                            pattern="\d*"
                                            oninput="this.value = this.value.replace(/\D/g, '')"
                                        >
                                        @error('father_cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Date of Birth</label>
                                        <input
                                            type="date"
                                            name="date_of_birth"
                                            value="{{ old('date_of_birth', $student->date_of_birth) }}"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            min="1900-01-01"
                                        >
                                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="transgender" {{ old('gender', $student->gender) == 'transgender' ? 'selected' : '' }}>Transgender</option>
                                        </select>
                                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Religion</label>
                                        <input type="text" name="religion" value="{{ old('religion', $student->religion) }}" class="form-control @error('religion') is-invalid @enderror" placeholder="Religion">
                                        @error('religion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Blood Group</label>
                                        <input type="text" name="blood_group" value="{{ old('blood_group', $student->blood_group) }}" class="form-control @error('blood_group') is-invalid @enderror" placeholder="Blood Group">
                                        @error('blood_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Student Photo</label>
                                        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        @if($student->getFirstMediaUrl('student'))
                                            <div class="mt-2">
                                                <img src="{{ $student->getFirstMediaUrl('student') }}" alt="Student Photo" class="img-thumbnail" style="max-height: 100px;">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="remove_photo" id="remove_photo">
                                                    <label class="form-check-label text-danger" for="remove_photo">Remove current photo</label>
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
                                        <label class="form-label fw-bold">Address</label>
                                        <input type="text" name="address" value="{{ old('address', $student->address) }}" class="form-control @error('address') is-invalid @enderror" placeholder="Full Address">
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Student Contact</label>
                                        <input
                                            type="text"
                                            name="student_contact"
                                            value="{{ old('student_contact', $student->student_contact) }}"
                                            class="form-control @error('student_contact') is-invalid @enderror"
                                            placeholder="Student number"
                                            maxlength="20"
                                        >
                                        @error('student_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Parent Contact</label>
                                        <input
                                            type="text"
                                            name="parent_contact"
                                            value="{{ old('parent_contact', $student->parent_contact) }}"
                                            class="form-control @error('parent_contact') is-invalid @enderror"
                                            placeholder="Parent number"
                                            maxlength="20"
                                        >
                                        @error('parent_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">WhatsApp Number</label>
                                        <input
                                            type="text"
                                            name="whatsapp_no"
                                            value="{{ old('whatsapp_no', $student->whatsapp_no) }}"
                                            class="form-control @error('whatsapp_no') is-invalid @enderror"
                                            placeholder="WhatsApp number"
                                            maxlength="20"
                                        >
                                        @error('whatsapp_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Email</label>
                                        <input
                                            type="email"
                                            name="email"
                                            value="{{ old('email', $student->email) }}"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Email address"
                                        >
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information Section -->
                        <div class="col-12">
                            <div class="section-header bg-success text-white p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Information</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Registration Number </label>
                                        <input type="text" name="registration_number" value="{{ old('registration_number', $student->registration_number) }}" class="form-control @error('registration_number') is-invalid @enderror" placeholder="Registration Number">
                                        @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Roll Number <span class="text-danger">*</span></label>
                                        <input type="text" name="roll_number" value="{{ old('roll_number', $student->roll_number) }}" class="form-control @error('roll_number') is-invalid @enderror" placeholder="Roll Number">
                                        @error('roll_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="studying" {{ old('status', $student->status) == 'studying' ? 'selected' : '' }}>Studying</option>
                                            <option value="passed_out" {{ old('status', $student->status) == 'passed_out' ? 'selected' : '' }}>Passed Out</option>
                                            <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                            <option value="dropped" {{ old('status', $student->status) == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                            <option value="expelled" {{ old('status', $student->status) == 'expelled' ? 'selected' : '' }}>Expelled</option>
                                        </select>
                                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Academic Session Display -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Academic Session</label>
                                        <div class="form-control-plaintext">
                                            {{ $sessions->firstWhere('id', old('academic_session_id', $student->academic_session_id))?->name ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <!-- Study Level Display -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Study Level</label>
                                        <div class="form-control-plaintext">
                                            {{ $levels->firstWhere('id', old('study_level_id', $student->study_level_id))?->name ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <!-- Program Display -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Program</label>
                                        <div class="form-control-plaintext">
                                            {{ $programs->firstWhere('id', old('program_id', $student->program_id))?->name ?? 'N/A' }}
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Is Hafiz-e-Quran?</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_hafiz" id="is_hafiz" {{ old('is_hafiz', $student->is_hafiz) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_hafiz">Yes</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Is Active?</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Previous Education Section -->
                        <div class="col-12">
                            <div class="section-header bg-warning text-dark p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Previous Education</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <h6 class="fw-bold">Matriculation (Secondary School)</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Passing Year</label>
                                        <input type="text" name="matric_passing_year" value="{{ old('matric_passing_year', $student->matric_passing_year) }}" class="form-control" placeholder="Year">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Roll Number</label>
                                        <input type="text" name="matric_roll_no" value="{{ old('matric_roll_no', $student->matric_roll_no) }}" class="form-control" placeholder="Roll No">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Board</label>
                                        <input type="text" name="matric_board" value="{{ old('matric_board', $student->matric_board) }}" class="form-control" placeholder="Board">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Group</label>
                                        <input type="text" name="matric_group" value="{{ old('matric_group', $student->matric_group) }}" class="form-control" placeholder="Group">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Obtained Marks</label>
                                        <input type="number" name="matric_obtained_marks" value="{{ old('matric_obtained_marks', $student->matric_obtained_marks) }}" class="form-control" placeholder="Obtained">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Marks</label>
                                        <input type="number" name="matric_total_marks" value="{{ old('matric_total_marks', $student->matric_total_marks) }}" class="form-control" placeholder="Total">
                                    </div>
                                </div>

                                <h6 class="fw-bold">Intermediate (Higher Secondary)</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Passing Year</label>
                                        <input type="text" name="inter_passing_year" value="{{ old('inter_passing_year', $student->inter_passing_year) }}" class="form-control" placeholder="Year">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Roll Number</label>
                                        <input type="text" name="inter_roll_no" value="{{ old('inter_roll_no', $student->inter_roll_no) }}" class="form-control" placeholder="Roll No">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Board</label>
                                        <input type="text" name="inter_board" value="{{ old('inter_board', $student->inter_board) }}" class="form-control" placeholder="Board">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Group</label>
                                        <input type="text" name="inter_group" value="{{ old('inter_group', $student->inter_group) }}" class="form-control" placeholder="Group">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Obtained Marks</label>
                                        <input type="number" name="inter_obtained_marks" value="{{ old('inter_obtained_marks', $student->inter_obtained_marks) }}" class="form-control" placeholder="Obtained">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Marks</label>
                                        <input type="number" name="inter_total_marks" value="{{ old('inter_total_marks', $student->inter_total_marks) }}" class="form-control" placeholder="Total">
                                    </div>
                                </div>

                                <h6 class="fw-bold">Graduation</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Passing Year</label>
                                        <input type="text" name="grad_passing_year" value="{{ old('grad_passing_year', $student->grad_passing_year) }}" class="form-control" placeholder="Year">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Registration Number</label>
                                        <input type="text" name="grad_reg_no" value="{{ old('grad_reg_no', $student->grad_reg_no) }}" class="form-control" placeholder="Reg No">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Board/University</label>
                                        <input type="text" name="grad_board" value="{{ old('grad_board', $student->grad_board) }}" class="form-control" placeholder="Board/University">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Group/Subject</label>
                                        <input type="text" name="grad_group" value="{{ old('grad_group', $student->grad_group) }}" class="form-control" placeholder="Group/Subject">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Obtained Marks</label>
                                        <input type="number" name="grad_obtained_marks" value="{{ old('grad_obtained_marks', $student->grad_obtained_marks) }}" class="form-control" placeholder="Obtained">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Marks</label>
                                        <input type="number" name="grad_total_marks" value="{{ old('grad_total_marks', $student->grad_total_marks) }}" class="form-control" placeholder="Total">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Father's Job Information Section -->
                        <div class="col-12">
                            <div class="section-header bg-secondary text-white p-2 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Father's Job Information</h5>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Has Job?</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="father_job" id="father_job" {{ old('father_job', $student->father_job) ? 'checked' : '' }} onchange="toggleFatherJobFields()">
                                            <label class="form-check-label" for="father_job">Yes</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="fatherDepartmentField" style="{{ old('father_job', $student->father_job) ? '' : 'display: none;' }}">
                                        <label class="form-label fw-bold">Department</label>
                                        <input type="text" name="father_department" value="{{ old('father_department', $student->father_department) }}" class="form-control" placeholder="Department">
                                    </div>

                                    <div class="col-md-4" id="fatherDesignationField" style="{{ old('father_job', $student->father_job) ? '' : 'display: none;' }}">
                                        <label class="form-label fw-bold">Designation</label>
                                        <input type="text" name="father_designation" value="{{ old('father_designation', $student->father_designation) }}" class="form-control" placeholder="Designation">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Login Credentials Section -->
                        @if($student->user)
                            <div class="col-12">
                                <div class="section-header bg-dark text-white p-2 rounded-top">
                                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Login Credentials</h5>
                                </div>
                                <div class="border p-3 rounded-bottom mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Username</label>
                                            <input type="text" class="form-control" value="{{ $student->user->username }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Role</label>
                                            <input type="text" class="form-control" value="{{ $student->user->roles->first()->name ?? 'No role assigned' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="col-12 mt-4">
                            <div class="d-flex gap-2 justify-content-between">
                                <div>
                                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to List
                                    </a>
                                    <button type="reset" class="btn btn-warning ms-2">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Update Student
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
        // Existing functions
        function toggleFatherJobFields() {
            const checkbox = document.getElementById('father_job');
            const departmentField = document.getElementById('fatherDepartmentField');
            const designationField = document.getElementById('fatherDesignationField');

            if (checkbox.checked) {
                departmentField.style.display = 'block';
                designationField.style.display = 'block';
            } else {
                departmentField.style.display = 'none';
                designationField.style.display = 'none';
            }
        }

        // New dependent dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const academicSessionDropdown = document.getElementById('academic_session_id');
            const studyLevelDropdown = document.getElementById('study_level_id');
            const programDropdown = document.getElementById('program_id');

            // When academic session changes
            academicSessionDropdown.addEventListener('change', function() {
                const sessionId = this.value;

                // Reset and disable dependent dropdowns
                studyLevelDropdown.innerHTML = '<option value="">Loading...</option>';
                studyLevelDropdown.disabled = true;
                programDropdown.innerHTML = '<option value="">Select Study Level First</option>';
                programDropdown.disabled = true;

                if (sessionId) {
                    // Fetch study levels for selected academic session
                    fetch(`/ajax-study-levels?academic_session_id=${sessionId}`)
                        .then(response => response.json())
                        .then(data => {
                            studyLevelDropdown.innerHTML = '<option value="">Select Study Level</option>';
                            data.forEach(level => {
                                studyLevelDropdown.innerHTML += `<option value="${level.id}">${level.name}</option>`;
                            });
                            studyLevelDropdown.disabled = false;

                            // If there was a previously selected value (form validation failed)
                            const oldStudyLevelId = "{{ old('study_level_id', $student->study_level_id) }}";
                            if (oldStudyLevelId && data.some(level => level.id == oldStudyLevelId)) {
                                studyLevelDropdown.value = oldStudyLevelId;
                                loadPrograms(oldStudyLevelId);
                            }
                        });
                } else {
                    studyLevelDropdown.innerHTML = '<option value="">Select Academic Session First</option>';
                }
            });

            // When study level changes
            studyLevelDropdown.addEventListener('change', function() {
                const levelId = this.value;

                // Reset and disable program dropdown
                programDropdown.innerHTML = '<option value="">Loading...</option>';
                programDropdown.disabled = true;

                if (levelId) {
                    loadPrograms(levelId);
                } else {
                    programDropdown.innerHTML = '<option value="">Select Study Level First</option>';
                }
            });

            function loadPrograms(levelId) {
                // Fetch programs for selected study level
                fetch(`/ajax-programs?study_level_id=${levelId}`)
                    .then(response => response.json())
                    .then(data => {
                        programDropdown.innerHTML = '<option value="">Select Program</option>';
                        data.forEach(program => {
                            programDropdown.innerHTML += `<option value="${program.id}">${program.name}</option>`;
                        });
                        programDropdown.disabled = false;

                        // If there was a previously selected value (form validation failed)
                        const oldProgramId = "{{ old('program_id', $student->program_id) }}";
                        if (oldProgramId && data.some(program => program.id == oldProgramId)) {
                            programDropdown.value = oldProgramId;
                        }
                    });
            }

            // Trigger change event if academic session is already selected (form validation failed)
            if (academicSessionDropdown.value) {
                academicSessionDropdown.dispatchEvent(new Event('change'));
            }

            // Initialize toggle states if form validation failed
            if (document.getElementById('father_job').checked) {
                toggleFatherJobFields();
            }
        });
    </script>
@endsection

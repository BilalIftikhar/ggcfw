@extends('layouts.app')
@section('title', 'Add Student')
@section('content')
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Student</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
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
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Father's Name <span class="text-danger">*</span></label>
                                        <input type="text" name="father_name" value="{{ old('father_name') }}" class="form-control @error('father_name') is-invalid @enderror" placeholder="Father's Full Name">
                                        @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">CNIC/B-Form <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="cnic"
                                            value="{{ old('cnic') }}"
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
                                            value="{{ old('father_cnic') }}"
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
                                            value="{{ old('date_of_birth') }}"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            min="1900-01-01"
                                        >
                                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="transgender" {{ old('gender') == 'transgender' ? 'selected' : '' }}>Transgender</option>
                                        </select>
                                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Religion</label>
                                        <input type="text" name="religion" value="{{ old('religion') }}" class="form-control @error('religion') is-invalid @enderror" placeholder="Religion">
                                        @error('religion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Blood Group</label>
                                        <input type="text" name="blood_group" value="{{ old('blood_group') }}" class="form-control @error('blood_group') is-invalid @enderror" placeholder="Blood Group">
                                        @error('blood_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Student Photo</label>
                                        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                        <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" placeholder="Full Address">
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Student Contact</label>
                                        <input
                                            type="text"
                                            name="student_contact"
                                            value="{{ old('student_contact') }}"
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
                                            value="{{ old('parent_contact') }}"
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
                                            value="{{ old('whatsapp_no') }}"
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
                                            value="{{ old('email') }}"
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
                                        <input type="text" name="registration_number" value="{{ old('registration_number') }}" class="form-control @error('registration_number') is-invalid @enderror" placeholder="Registration Number">
                                        @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Roll Number <span class="text-danger">*</span></label>
                                        <input type="text" name="roll_number" value="{{ old('roll_number') }}" class="form-control @error('roll_number') is-invalid @enderror" placeholder="Roll Number">
                                        @error('roll_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="studying" {{ old('status') == 'studying' ? 'selected' : '' }}>Studying</option>
                                            <option value="passed_out" {{ old('status') == 'passed_out' ? 'selected' : '' }}>Passed Out</option>
                                            <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                            <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                            <option value="expelled" {{ old('status') == 'expelled' ? 'selected' : '' }}>Expelled</option>
                                        </select>
                                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Academic Session Dropdown -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Academic Session <span class="text-danger">*</span></label>
                                        <select name="academic_session_id" id="academic_session_id" class="form-select @error('academic_session_id') is-invalid @enderror" required>
                                            <option value="">Select Session</option>
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                                    {{ $session->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academic_session_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Study Level Dropdown -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Study Level <span class="text-danger">*</span></label>
                                        <select name="study_level_id" id="study_level_id" class="form-select @error('study_level_id') is-invalid @enderror" required disabled>
                                            <option value="">Select Academic Session First</option>
                                            @if(old('study_level_id'))
                                                @foreach($levels as $level)
                                                    @if($level->academic_session_id == old('academic_session_id'))
                                                        <option value="{{ $level->id }}" {{ old('study_level_id') == $level->id ? 'selected' : '' }}>
                                                            {{ $level->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('study_level_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Program Dropdown -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Program <span class="text-danger">*</span></label>
                                        <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror" required disabled>
                                            <option value="">Select Study Level First</option>
                                            @if(old('program_id'))
                                                @foreach($programs as $program)
                                                    @if($program->study_level_id == old('study_level_id'))
                                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('program_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Program Class Dropdown -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Select Class <span class="text-danger">*</span></label>
                                        <select name="program_class_id" id="program_class_id" class="form-select @error('program_class_id') is-invalid @enderror" required disabled>
                                            <option value="">Select Program First</option>
                                        </select>
                                        @error('program_class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>


                                    <!-- Courses Container -->
                                    <div class="col-md-12 mt-3" id="coursesContainer" style="display:none;">
                                        <!-- Mandatory Courses -->
                                        <div id="mandatoryCoursesContainer" class="mb-3">
                                            <label class="form-label fw-bold text-primary">Mandatory Courses</label>
                                            <div id="mandatoryCoursesList" class="d-flex flex-wrap gap-3">
                                                <!-- Mandatory course checkboxes will be inserted here -->
                                            </div>
                                        </div>

                                        <!-- Optional Courses -->
                                        <div id="optionalCoursesContainer">
                                            <label class="form-label fw-bold text-success">Optional Courses</label>
                                            <div id="optionalCoursesList" class="d-flex flex-wrap gap-3">
                                                <!-- Optional course checkboxes will be inserted here -->
                                            </div>
                                        </div>

                                    </div>


                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Examination Session</label>
                                        <select name="examination_session_id" id="examination_session_id" class="form-select @error('examination_session_id') is-invalid @enderror">
                                            <option value="">Select Examination Session</option>
                                            @foreach($examinationSessions as $examSession)
                                                <option value="{{ $examSession->id }}" {{ old('examination_session_id') == $examSession->id ? 'selected' : '' }}>
                                                    {{ $examSession->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('examination_session_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Is Hafiz-e-Quran?</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="hafiz" id="hafiz" {{ old('hafiz') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hafiz">Yes</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Is Active?</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
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
                                        <input type="text" name="matric_passing_year" value="{{ old('matric_passing_year') }}" class="form-control" placeholder="Year">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Roll Number</label>
                                        <input type="text" name="matric_roll_no" value="{{ old('matric_roll_no') }}" class="form-control" placeholder="Roll No">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Board</label>
                                        <input type="text" name="matric_board" value="{{ old('matric_board') }}" class="form-control" placeholder="Board">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Group</label>
                                        <input type="text" name="matric_group" value="{{ old('matric_group') }}" class="form-control" placeholder="Group">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Obtained Marks</label>
                                        <input type="number" name="matric_obtained_marks" value="{{ old('matric_obtained_marks') }}" class="form-control" placeholder="Obtained">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Marks</label>
                                        <input type="number" name="matric_total_marks" value="{{ old('matric_total_marks') }}" class="form-control" placeholder="Total">
                                    </div>
                                </div>

                                <h6 class="fw-bold">Intermediate (Higher Secondary)</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Passing Year</label>
                                        <input type="text" name="inter_passing_year" value="{{ old('inter_passing_year') }}" class="form-control" placeholder="Year">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Roll Number</label>
                                        <input type="text" name="inter_roll_no" value="{{ old('inter_roll_no') }}" class="form-control" placeholder="Roll No">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Board</label>
                                        <input type="text" name="inter_board" value="{{ old('inter_board') }}" class="form-control" placeholder="Board">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Group</label>
                                        <input type="text" name="inter_group" value="{{ old('inter_group') }}" class="form-control" placeholder="Group">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Obtained Marks</label>
                                        <input type="number" name="inter_obtained_marks" value="{{ old('inter_obtained_marks') }}" class="form-control" placeholder="Obtained">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Marks</label>
                                        <input type="number" name="inter_total_marks" value="{{ old('inter_total_marks') }}" class="form-control" placeholder="Total">
                                    </div>
                                </div>

                                <h6 class="fw-bold">Graduation</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Passing Year</label>
                                        <input type="text" name="grad_passing_year" value="{{ old('grad_passing_year') }}" class="form-control" placeholder="Year">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Registration Number</label>
                                        <input type="text" name="grad_reg_no" value="{{ old('grad_reg_no') }}" class="form-control" placeholder="Reg No">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Board/University</label>
                                        <input type="text" name="grad_board" value="{{ old('grad_board') }}" class="form-control" placeholder="Board/University">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Group/Subject</label>
                                        <input type="text" name="grad_group" value="{{ old('grad_group') }}" class="form-control" placeholder="Group/Subject">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Obtained Marks</label>
                                        <input type="number" name="grad_obtained_marks" value="{{ old('grad_obtained_marks') }}" class="form-control" placeholder="Obtained">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Marks</label>
                                        <input type="number" name="grad_total_marks" value="{{ old('grad_total_marks') }}" class="form-control" placeholder="Total">
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
                                            <input class="form-check-input" type="checkbox" name="father_job" id="father_job" {{ old('father_job') ? 'checked' : '' }} onchange="toggleFatherJobFields()">
                                            <label class="form-check-label" for="father_job">Yes</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="fatherDepartmentField" style="{{ old('father_job') ? '' : 'display: none;' }}">
                                        <label class="form-label fw-bold">Department</label>
                                        <input type="text" name="father_department" value="{{ old('father_department') }}" class="form-control" placeholder="Department">
                                    </div>

                                    <div class="col-md-4" id="fatherDesignationField" style="{{ old('father_job') ? '' : 'display: none;' }}">
                                        <label class="form-label fw-bold">Designation</label>
                                        <input type="text" name="father_designation" value="{{ old('father_designation') }}" class="form-control" placeholder="Designation">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Login Credentials Section -->
                        <div class="col-12">
                            <div class="section-header bg-dark text-white p-2 rounded-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Login Credentials</h5>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="generateLogin" id="generateLogin" onchange="toggleLoginFields()">
                                        <label class="form-check-label" for="generateLogin">Create Login Account</label>
                                    </div>
                                </div>
                            </div>
                            <div class="border p-3 rounded-bottom mb-4" id="loginFields" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username">
                                            <button class="btn btn-outline-secondary" type="button" onclick="generateUsername()">
                                                <i class="fas fa-sync-alt"></i> Generate
                                            </button>
                                        </div>
                                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                                        <select name="role_id" class="form-select @error('role_id') is-invalid @enderror">
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                    <i class="fas fa-save me-1"></i> Save Student
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Existing functions
        function toggleLoginFields() {
            const checkbox = document.getElementById('generateLogin');
            const loginFields = document.getElementById('loginFields');
            if (checkbox.checked) {
                loginFields.style.display = 'block';
            } else {
                loginFields.style.display = 'none';
                document.getElementById('username').value = '';
                document.querySelector('input[name="password"]').value = '';
                document.querySelector('select[name="role_id"]').value = '';
            }
        }

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

        function generateUsername() {
            const cnic = document.getElementById('cnicInput').value.trim();
            const usernameInput = document.getElementById('username');
            const passwordInput = document.querySelector('input[name="password"]');
            const nameField = document.querySelector('input[name="name"]');

            if (!cnic) {
                Swal.fire({
                    title: 'Missing CNIC',
                    text: 'Please enter CNIC/B-Form first.',
                    icon: 'warning',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Set username as CNIC
            usernameInput.value = cnic;

            // Generate password
            if (nameField) {
                const namePart = (nameField.value.trim().split(' ')[0] || '').toLowerCase();
                const cnicDigits = cnic.replace(/\D/g, '');
                let lastFourDigits = '';

                if (cnicDigits.length >= 4) {
                    lastFourDigits = cnicDigits.slice(-4);
                } else {
                    lastFourDigits = cnicDigits;
                }

                const generatedPassword = namePart + lastFourDigits;
                passwordInput.value = generatedPassword;

                Swal.fire({
                    title: 'Generated Password',
                    text: `The generated password is: ${generatedPassword}. Please note it down.`,
                    icon: 'info',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        }


        // New dependent dropdown functionality
        document.addEventListener('DOMContentLoaded', function () {
            const academicSessionDropdown = document.getElementById('academic_session_id');
            const studyLevelDropdown = document.getElementById('study_level_id');
            const programDropdown = document.getElementById('program_id');
            const programClassDropdown = document.getElementById('program_class_id');
            const mandatoryCoursesList = document.getElementById('mandatoryCoursesList');
            const optionalCoursesList = document.getElementById('optionalCoursesList');
            const coursesContainer = document.getElementById('coursesContainer');

            // Academic Session Change
            academicSessionDropdown.addEventListener('change', function () {
                const sessionId = this.value;
                studyLevelDropdown.innerHTML = '<option value="">Loading...</option>';
                studyLevelDropdown.disabled = true;
                programDropdown.innerHTML = '<option value="">Select Study Level First</option>';
                programDropdown.disabled = true;
                programClassDropdown.innerHTML = '<option value="">Select Program First</option>';
                programClassDropdown.disabled = true;
                clearCourses();

                if (sessionId) {
                    fetch(`/ajax-study-levels?academic_session_id=${sessionId}`)
                        .then(response => response.json())
                        .then(data => {
                            studyLevelDropdown.innerHTML = '<option value="">Select Study Level</option>';
                            data.forEach(level => {
                                studyLevelDropdown.innerHTML += `<option value="${level.id}">${level.name}</option>`;
                            });
                            studyLevelDropdown.disabled = false;
                        });
                } else {
                    studyLevelDropdown.innerHTML = '<option value="">Select Academic Session First</option>';
                }
            });

            // Study Level Change
            studyLevelDropdown.addEventListener('change', function () {
                const levelId = this.value;
                programDropdown.innerHTML = '<option value="">Loading...</option>';
                programDropdown.disabled = true;
                programClassDropdown.innerHTML = '<option value="">Select Program First</option>';
                programClassDropdown.disabled = true;
                clearCourses();

                if (levelId) {
                    fetch(`/ajax-programs?study_level_id=${levelId}`)
                        .then(response => response.json())
                        .then(data => {
                            programDropdown.innerHTML = '<option value="">Select Program</option>';
                            data.forEach(program => {
                                programDropdown.innerHTML += `<option value="${program.id}">${program.name}</option>`;
                            });
                            programDropdown.disabled = false;
                        });
                } else {
                    programDropdown.innerHTML = '<option value="">Select Study Level First</option>';
                }
            });

            // Program Change: Fetch Program Classes
            programDropdown.addEventListener('change', function () {
                const programId = this.value;
                programClassDropdown.innerHTML = '<option value="">Loading...</option>';
                programClassDropdown.disabled = true;
                clearCourses();

                if (programId) {
                    fetch(`/program-classes?program_id=${programId}`)
                        .then(response => response.json())
                        .then(data => {
                            programClassDropdown.innerHTML = '<option value="">Select Class</option>';
                            data.forEach(programClass => {
                                programClassDropdown.innerHTML += `<option value="${programClass.id}">${programClass.name}</option>`;
                            });
                            programClassDropdown.disabled = false;
                        });
                } else {
                    programClassDropdown.innerHTML = '<option value="">Select Program First</option>';
                }
            });

            // Program Class Change: Fetch Courses
            programClassDropdown.addEventListener('change', function () {
                const programId = programDropdown.value;
                const programClassId = this.value;
                clearCourses();

                if (programId && programClassId) {
                    fetch(`/ajax-courses?program_id=${programId}&program_class_id=${programClassId}`)
                        .then(response => response.json())
                        .then(courses => {
                            if (courses.length > 0) {
                                coursesContainer.style.display = 'block';
                                courses.forEach(course => {
                                    const checkbox = document.createElement('input');
                                    checkbox.type = 'checkbox';
                                    checkbox.name = 'courses[]';
                                    checkbox.value = course.id;
                                    checkbox.id = `course_${course.id}`;
                                    if (course.is_mandatory) {
                                        checkbox.checked = true;
                                        checkbox.disabled = true;
                                    }

                                    const label = document.createElement('label');
                                    label.htmlFor = checkbox.id;
                                    label.classList.add('me-3', 'd-flex', 'align-items-center', 'gap-2');
                                    label.appendChild(checkbox);
                                    label.appendChild(document.createTextNode(course.name));

                                    if (course.is_mandatory) {
                                        mandatoryCoursesList.appendChild(label);
                                    } else {
                                        optionalCoursesList.appendChild(label);
                                    }
                                });
                            }
                        })
                        .catch(err => {
                            console.error('Failed to fetch courses:', err);
                        });
                }
            });

            function clearCourses() {
                coursesContainer.style.display = 'none';
                mandatoryCoursesList.innerHTML = '';
                optionalCoursesList.innerHTML = '';
            }

            // Auto-trigger if old data exists
            if (academicSessionDropdown.value) {
                academicSessionDropdown.dispatchEvent(new Event('change'));
            }
        });


    </script>
@endsection

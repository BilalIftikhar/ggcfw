<div id="teachingFields" class="row" style="">

    <!-- Personal Information Section -->
    <div class="col-12">
        <div class="section-header bg-primary text-white p-2 rounded-top">
            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Personal Information</h5>
        </div>
        <div class="border p-3 rounded-bottom mb-4">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" placeholder="John">
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" placeholder="Doe">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Father's Name <span class="text-danger">*</span></label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}" class="form-control @error('father_name') is-invalid @enderror" placeholder="Father's Full Name">
                    @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">CNIC No <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="cnic_no"
                        value="{{ old('cnic_no') }}"
                        class="form-control @error('cnic_no') is-invalid @enderror"
                        placeholder="13 digits, no dashes"
                        maxlength="13"
                        inputmode="numeric"
                        pattern="\d*"
                        oninput="this.value = this.value.replace(/\D/g, '')"
                    >
                    @error('cnic_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Seniority No</label>
                    <input
                        type="text"
                        name="seniority_no"
                        value="{{ old('seniority_no') }}"
                        class="form-control @error('seniority_no') is-invalid @enderror"
                        placeholder="Seniority Number"
                        inputmode="numeric"
                        pattern="\d*"
                        oninput="this.value = this.value.replace(/\D/g, '')"
                    >
                    @error('seniority_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>
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
                    <label class="form-label fw-bold">Domicile</label>
                    <input type="text" name="domicile" value="{{ old('domicile') }}" class="form-control @error('domicile') is-invalid @enderror" placeholder="e.g., Punjab">
                    @error('domicile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Employee Photo</label>
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

                <div class="col-md-4">
                    <label class="form-label fw-bold">Home Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" placeholder="Full Address">
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Primary Contact No <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="primary_contact"
                        value="{{ old('primary_contact') }}"
                        class="form-control @error('primary_contact') is-invalid @enderror"
                        placeholder="e.g., 923347321030"
                        maxlength="12"
                        inputmode="numeric"
                        pattern="\d*"
                        oninput="this.value = this.value.replace(/\D/g, '')"
                    >
                    @error('primary_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Secondary Contact No</label>
                    <input
                        type="text"
                        name="secondary_contact"
                        value="{{ old('secondary_contact') }}"
                        class="form-control @error('secondary_contact') is-invalid @enderror"
                        placeholder="Optional"
                        maxlength="12"
                        inputmode="numeric"
                        pattern="\d*"
                        oninput="this.value = this.value.replace(/\D/g, '')"
                    >
                    @error('secondary_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <label class="form-label fw-bold">Designation <span class="text-danger">*</span></label>
                    <select name="designation" class="form-select @error('designation') is-invalid @enderror">
                        <option value="">Select Designation</option>
                        <option value="Lecturer" {{ old('designation') == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                        <option value="Assistant Professor" {{ old('designation') == 'Assistant Professor' ? 'selected' : '' }}>Assistant Professor</option>
                        <option value="Associate Professor" {{ old('designation') == 'Associate Professor' ? 'selected' : '' }}>Associate Professor</option>
                        <option value="Professor" {{ old('designation') == 'Professor' ? 'selected' : '' }}>Professor</option>
                        <option value="Principal" {{ old('designation') == 'Principal' ? 'selected' : '' }}>Principal</option>
                    </select>
                    @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">BPS <span class="text-danger">*</span></label>
                    <select name="bps" class="form-select @error('bps') is-invalid @enderror">
                        <option value="">Select BPS</option>
                        @for($i = 17; $i <= 21; $i++)
                            <option value="{{ $i }}" {{ old('bps') == $i ? 'selected' : '' }}>BPS-{{ $i }}</option>
                        @endfor
                    </select>
                    @error('bps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Quota</label>
                    <select name="quota" class="form-select @error('quota') is-invalid @enderror">
                        <option value="">Select Quota</option>
                        <option value="Open Merit" {{ old('quota') == 'Open Merit' ? 'selected' : '' }}>Open Merit</option>
                        <option value="Provincial" {{ old('quota') == 'Provincial' ? 'selected' : '' }}>Provincial</option>
                        <option value="Disabled" {{ old('quota') == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                        <option value="Minority" {{ old('quota') == 'Minority' ? 'selected' : '' }}>Minority</option>
                    </select>
                    @error('quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Employment Type <span class="text-danger">*</span></label>
                    <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror">
                        <option value="regular" {{ old('employment_type') == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="adhoc" {{ old('employment_type') == 'adhoc' ? 'selected' : '' }}>Adhoc</option>
                    </select>
                    @error('employment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Date of Retirement</label>
                    <input
                        type="date"
                        name="retirement_date"
                        value="{{ old('retirement_date') }}"
                        class="form-control @error('retirement_date') is-invalid @enderror"
                        min="{{ date('1900-01-01') }}"
                    >
                    @error('retirement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Qualification <span class="text-danger">*</span></label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}" class="form-control @error('qualification') is-invalid @enderror" placeholder="Highest Academic Qualification">
                    @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Various Employment Dates -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Govt. Entry Date</label>
                    <input
                        type="date"
                        name="govt_entry_date"
                        value="{{ old('govt_entry_date') }}"
                        class="form-control @error('govt_entry_date') is-invalid @enderror"
                        min="1900-01-01"
                    >
                    @error('govt_entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Contract Joining Date</label>
                    <input
                        type="date"
                        name="contract_joining_date"
                        value="{{ old('contract_joining_date') }}"
                        class="form-control @error('contract_joining_date') is-invalid @enderror"
                        min="1900-01-01"
                    >
                    @error('contract_joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Regular Joining Date</label>
                    <input
                        type="date"
                        name="regular_joining_date"
                        value="{{ old('regular_joining_date') }}"
                        class="form-control @error('regular_joining_date') is-invalid @enderror"
                        min="1900-01-01"
                    >
                    @error('regular_joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Present Station Joining Date</label>
                    <input
                        type="date"
                        name="present_station_joining_date"
                        value="{{ old('present_station_joining_date') }}"
                        class="form-control @error('present_station_joining_date') is-invalid @enderror"
                        min="1900-01-01"
                    >
                    @error('present_station_joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Qualifying Service (Years)</label>
                    <input
                        type="text"
                        name="qualifying_service"
                        value="{{ old('qualifying_service') }}"
                        class="form-control @error('qualifying_service') is-invalid @enderror"
                        placeholder="Format: Y.M.D (e.g., 10.6.15)"
                    >
                    @error('qualifying_service') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Cadre</label>
                    <input type="text" name="cadre" value="{{ old('cadre') }}" class="form-control @error('cadre') is-invalid @enderror" placeholder="Cadre (if applicable)">
                    @error('cadre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Subject<span class="text-danger">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="Subject" required>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>
    </div>
</div>

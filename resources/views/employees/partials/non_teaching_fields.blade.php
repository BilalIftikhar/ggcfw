<div id="nonTeachingFields" class="row" style="">

    <!-- Personal Information Section -->
    <div class="col-12">
        <div class="section-header bg-primary text-white p-2 rounded-top">
            <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Personal Information</h5>
        </div>
        <div class="border p-3 rounded-bottom mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">CNIC No <span class="text-danger">*</span></label>
                    <input type="text" name="cnic" value="{{ old('cnic') }}"
                           class="form-control @error('cnic') is-invalid @enderror"
                           placeholder="13-digit CNIC number"
                           maxlength="13"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13)">
                    @error('cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>


                <div class="col-md-3">
                    <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" placeholder="John">
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" placeholder="Doe">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Father's Name <span class="text-danger">*</span></label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}" class="form-control @error('father_name') is-invalid @enderror" placeholder="Father's Name">
                    @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control @error('date_of_birth') is-invalid @enderror">
                    @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Domicile <span class="text-danger">*</span></label>
                    <input type="text" name="domicile" value="{{ old('domicile') }}" class="form-control @error('domicile') is-invalid @enderror" placeholder="e.g., Punjab">
                    @error('domicile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Home Address</label>
                    <input type="text" name="home_address" value="{{ old('home_address') }}" class="form-control @error('home_address') is-invalid @enderror" placeholder="Full Address">
                    @error('home_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Primary Contact <span class="text-danger">*</span></label>
                    <input type="text" name="primary_contact" value="{{ old('primary_contact') }}"
                           class="form-control @error('primary_contact') is-invalid @enderror"
                           placeholder="923347321030"
                           maxlength="12"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)">
                    @error('primary_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Secondary Contact</label>
                    <input type="text" name="secondary_contact" value="{{ old('secondary_contact') }}"
                           class="form-control @error('secondary_contact') is-invalid @enderror"
                           placeholder="Optional"
                           maxlength="12"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)">
                    @error('secondary_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Employee Photo</label>
                    <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                <div class="col-md-3">
                    <label class="form-label fw-bold">Designation <span class="text-danger">*</span></label>
                    <input type="text" name="designation" value="{{ old('designation') }}" class="form-control @error('designation') is-invalid @enderror" placeholder="e.g., Junior Clerk">
                    @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">BPS <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="bps" value="{{ old('bps') }}" class="form-control @error('bps') is-invalid @enderror" placeholder="e.g., 14">
                    @error('bps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}" class="form-control @error('qualification') is-invalid @enderror" placeholder="Highest Degree">
                    @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Date of Retirement</label>
                    <input type="date" name="retirement_date" value="{{ old('retirement_date') }}" class="form-control @error('retirement_date') is-invalid @enderror">
                    @error('retirement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Date of 1st Entry in Govt. Service</label>
                    <input type="date" name="govt_service_entry_date" value="{{ old('govt_service_entry_date') }}" class="form-control @error('govt_service_entry_date') is-invalid @enderror">
                    @error('govt_service_entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Quota Recruited Against</label>
                    <input type="text" name="recruitment_quota" value="{{ old('recruitment_quota') }}" class="form-control @error('recruitment_quota') is-invalid @enderror" placeholder="e.g., Open Merit">
                    @error('recruitment_quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Date of Joining as Contract</label>
                    <input type="date" name="date_joining_contract" value="{{ old('date_joining_contract') }}" class="form-control @error('date_joining_contract') is-invalid @enderror">
                    @error('date_joining_contract') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Date of Joining as Regular</label>
                    <input type="date" name="date_joining_regular" value="{{ old('date_joining_regular') }}" class="form-control @error('date_joining_regular') is-invalid @enderror">
                    @error('date_joining_regular') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Employment Status</label>
                    <select name="employment_status" class="form-control @error('employment_status') is-invalid @enderror">
                        <option value="">Select Status</option>
                        <option value="Regular" {{ old('employment_status') == 'Regular' ? 'selected' : '' }}>Regular</option>
                        <option value="Contract" {{ old('employment_status') == 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Ad-hoc" {{ old('employment_status') == 'Ad-hoc' ? 'selected' : '' }}>Ad-hoc</option>
                    </select>
                    @error('employment_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Designation-wise Joining Dates -->
    <div class="col-12">
        <div class="section-header bg-info text-white p-2 rounded-top">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Joining by Designation</h5>
        </div>
        <div class="border p-3 rounded-bottom mb-4">
            <div class="row g-3">
                @foreach([
                    'junior_clerk' => 'Junior Clerk / Lec Assistant',
                    'senior_clerk' => 'Senior Clerk / Lec Assistant',
                    'lab_supervisor' => 'Lab Supervisor',
                    'head_clerk' => 'Head Clerk / Lab Superintendent',
                    'superintendent' => 'Superintendent',
                    'sr_bursar' => 'Sr. Bursar / EAD'
                ] as $field => $label)
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ $label }}</label>
                        <input type="date" name="date_of_joining_{{ $field }}" value="{{ old('date_of_joining_'.$field) }}" class="form-control @error('date_of_joining_'.$field) is-invalid @enderror" min="1900-01-01">
                        @error('date_of_joining_'.$field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Service Summary Section -->
    <div class="col-12">
        <div class="section-header bg-warning text-dark p-2 rounded-top">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Service Summary</h5>
        </div>
        <div class="border p-3 rounded-bottom">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Qualifying Service (Y.M.D)</label>
                    <input type="text" name="qualifying_service" value="{{ old('qualifying_service') }}" class="form-control @error('qualifying_service') is-invalid @enderror" placeholder="e.g., 10-03-15">
                    @error('qualifying_service') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Present Station Joining Date</label>
                    <input type="date" name="present_station_joining_date" value="{{ old('present_station_joining_date') }}" class="form-control @error('present_station_joining_date') is-invalid @enderror" min="1900-01-01">
                    @error('present_station_joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

</div>

<form method="POST" action="{{ route('attendance.daily.search') }}">
    @csrf
    <div class="row mt-3">
        <!-- Academic Session -->
        <div class="col-md-3">
            <label>Academic Session</label>
            <select name="academic_session_id" id="academic_session_id" class="form-select" required>
                <option value="">Select Academic Session</option>
                @foreach($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ old('academic_session_id', $selectedFilters['academic_session_id'] ?? '') == $session->id ? 'selected' : '' }}>
                        {{ $session->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Study Level -->
        <div class="col-md-3">
            <label>Study Level</label>
            <select name="study_level_id" id="study_level_id" class="form-select" {{ !isset($selectedFilters['study_level_id']) ? 'disabled' : '' }} required>
                <option value="">Select Study Level</option>
                @if($studyLevels)
                    @foreach($studyLevels as $level)
                        <option value="{{ $level->id }}" {{ old('study_level_id', $selectedFilters['study_level_id'] ?? '') == $level->id ? 'selected' : '' }}>
                            {{ $level->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Program -->
        <div class="col-md-3">
            <label>Program</label>
            <select name="program_id" id="program_id" class="form-select" {{ !isset($selectedFilters['program_id']) ? 'disabled' : '' }} required>
                <option value="">Select Program</option>
                @if($programs)
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id', $selectedFilters['program_id'] ?? '') == $program->id ? 'selected' : '' }}>
                            {{ $program->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Program Class -->
        <div class="col-md-3">
            <label>Class</label>
            <select name="program_class_id" id="program_class_id" class="form-select" {{ !isset($selectedFilters['program_class_id']) ? 'disabled' : '' }} required>
                <option value="">Select Class</option>
                @if($classes)
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('program_class_id', $selectedFilters['program_class_id'] ?? '') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="row mt-3">
        <!-- Year -->
        <div class="col-md-3">
            <label>Year</label>
            <input type="number" name="year" class="form-control"
                   value="{{ old('year', $year ?? date('Y')) }}" required>
        </div>

        <!-- Month -->
        <div class="col-md-3">
            <label>Month (Optional)</label>
            <select name="month" class="form-select">
                <option value="">All Months</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ old('month', $month ?? '') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </div>
</form>

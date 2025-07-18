@extends('layouts.app')

@section('title', 'Create Timetable')

@section('content')
    <style>
        .table th, .table td {
            vertical-align: middle !important;
        }
        .badge {
            font-size: 0.85em;
            font-weight: 500;
        }
        .btn-group-toggle .btn {
            transition: all 0.3s ease;
        }
        .btn-group-toggle .btn.active {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        .btn-group-toggle .btn:not(.active) {
            background-color: white;
            color: #0d6efd;
        }
        .btn-group-toggle .btn:first-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .btn-group-toggle .btn:last-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Create Timetable</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('timetable.index') }}">Timetables</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Timetable Setup</h5>

                    <form method="GET" action="{{ route('timetable.setup') }}" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Examination Session</label>
                            <select name="examination_session_id" id="examination_session_id" class="form-select" required>
                                <option value="">Select Examination Session</option>
                                @foreach($examinationSession as $session)
                                    <option value="{{ $session->id }}" {{ old('examination_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold d-block">Program Type</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary {{ old('program_type', 'semester') == 'semester' ? 'active' : '' }}">
                                    <input type="radio" name="program_type" value="semester" autocomplete="off"
                                        {{ old('program_type', 'semester') == 'semester' ? 'checked' : '' }}> Semester Programs
                                </label>
                                <label class="btn btn-outline-primary {{ old('program_type', 'semester') == 'annual' ? 'active' : '' }}">
                                    <input type="radio" name="program_type" value="annual" autocomplete="off"
                                        {{ old('program_type', 'semester') == 'annual' ? 'checked' : '' }}> Annual Programs
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('timetable.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-arrow-right"></i> Continue
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // This ensures the radio button toggle works properly
            $('.btn-group-toggle .btn').click(function() {
                $('.btn-group-toggle .btn').removeClass('active');
                $(this).addClass('active');
            });

            // If you want to persist the selection after form submission
            const programType = "{{ old('program_type', 'semester') }}";
            if (programType) {
                $(`.btn-group-toggle input[value="${programType}"]`).parent().addClass('active');
            }
        });
    </script>
@endsection

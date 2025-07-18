@extends('layouts.app')

@section('title', 'Transfer Academic Session Data')

@section('content')
    <style>
        .form-label {
            font-weight: 600;
        }
        .btn-transfer:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Transfer Academic Session Data</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('academic-session.index') }}">Academic Sessions</a></li>
                    <li class="breadcrumb-item active">Transfer Data</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Transfer Session Data</h5>

                            <form action="{{ route('academic-session.transfer') }}" method="POST" id="transferForm">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="source_session_id" class="form-label">Source Session <span class="text-danger">*</span></label>
                                            <select name="source_session_id" id="source_session_id" class="form-select @error('source_session_id') is-invalid @enderror" required>
                                                <option value="">Select Source Session</option>
                                                @foreach($sessions as $session)
                                                    <option value="{{ $session->id }}" {{ old('source_session_id') == $session->id ? 'selected' : '' }}>
                                                        {{ $session->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('source_session_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="target_session_id" class="form-label">Target Session <span class="text-danger">*</span></label>
                                            <select name="target_session_id" id="target_session_id" class="form-select @error('target_session_id') is-invalid @enderror" required>
                                                <option value="">Select Target Session</option>
                                                @foreach($sessions as $session)
                                                    <option value="{{ $session->id }}" {{ old('target_session_id') == $session->id ? 'selected' : '' }}>
                                                        {{ $session->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('target_session_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info bg-info text-white border-0">
                                    <i class="bi bi-info-circle me-2"></i> This will copy all study levels, programs, classes, and courses from the source session to the target session.
                                </div>

                                <div id="validationError" class="alert alert-danger d-none">
                                    <i class="bi bi-exclamation-triangle me-2"></i> Source and Target sessions cannot be the same.
                                </div>

                                <div class="text-end mt-3">
                                    <a href="{{ route('academic-session.index') }}" class="btn btn-secondary me-2">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" id="transferBtn" class="btn btn-primary btn-transfer" disabled>
                                        <i class="bi bi-arrow-left-right"></i> Transfer Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sourceSelect = document.getElementById('source_session_id');
            const targetSelect = document.getElementById('target_session_id');
            const transferBtn = document.getElementById('transferBtn');
            const validationError = document.getElementById('validationError');
            const form = document.getElementById('transferForm');

            function validateSelections() {
                const sourceValue = sourceSelect.value;
                const targetValue = targetSelect.value;

                // Enable/disable transfer button
                if (sourceValue && targetValue && sourceValue !== targetValue) {
                    transferBtn.disabled = false;
                    validationError.classList.add('d-none');
                } else {
                    transferBtn.disabled = true;
                    if (sourceValue && targetValue && sourceValue === targetValue) {
                        validationError.classList.remove('d-none');
                    } else {
                        validationError.classList.add('d-none');
                    }
                }
            }

            function updateDropdownOptions() {
                const sourceValue = sourceSelect.value;
                const targetValue = targetSelect.value;

                // Enable all options first
                sourceSelect.querySelectorAll('option').forEach(opt => opt.disabled = false);
                targetSelect.querySelectorAll('option').forEach(opt => opt.disabled = false);

                // Disable selected options in the other dropdown
                if (sourceValue) {
                    targetSelect.querySelector(`option[value="${sourceValue}"]`).disabled = true;
                    if (targetSelect.value === sourceValue) {
                        targetSelect.value = '';
                    }
                }
                if (targetValue) {
                    sourceSelect.querySelector(`option[value="${targetValue}"]`).disabled = true;
                    if (sourceSelect.value === targetValue) {
                        sourceSelect.value = '';
                    }
                }

                validateSelections();
            }

            // Event listeners
            sourceSelect.addEventListener('change', updateDropdownOptions);
            targetSelect.addEventListener('change', updateDropdownOptions);

            // Form submission prevention for same sessions
            form.addEventListener('submit', function(e) {
                if (sourceSelect.value === targetSelect.value) {
                    e.preventDefault();
                    validationError.classList.remove('d-none');
                }
            });

            // Initialize on page load
            updateDropdownOptions();
        });
    </script>
@endpush

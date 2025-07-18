@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')
    <style>
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.4em;
            cursor: pointer;
        }
        .border-dashed {
            border-style: dashed !important;
            border-color: #dee2e6 !important;
        }
        .bg-info-light {
            background-color: rgba(13, 202, 240, 0.1);
        }
        .bg-secondary-light {
            background-color: rgba(108, 117, 125, 0.1);
        }
        .card {
            border-radius: 0.5rem;
            width: calc(100% - 2rem);
            margin: 0 auto;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-bottom: 1px dashed #64b5f6 !important;
            padding: 1rem 1.5rem;
        }
        .main-content {
            min-height: calc(100vh - 120px);
            padding-bottom: 2rem;
        }
    </style>

    <main class="main main-content">
        <div class="pagetitle">
            <h1>Email (SMTP) Settings</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Email Settings</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header card-header-custom">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-envelope me-2"></i>Email (SMTP) Settings
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="alert alert-info-custom border border-dashed mb-4">
                                <i class="bi bi-info-circle me-2"></i> Configure your email server settings for system notifications and communications.
                            </div>

                            <form action="{{ route('update.email') }}" method="POST">
                                @csrf
                                <input type="hidden" name="update" value="email">
                                @php $canEdit = auth()->user()->can('update_email_settings'); @endphp

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" name="smtp_host" value="{{ $setting->smtp_host }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">SMTP Port</label>
                                        <input type="text" class="form-control" name="smtp_port" value="{{ $setting->smtp_port }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" name="smtp_username" value="{{ $setting->smtp_username }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="smtp_password" value="{{ $setting->smtp_password }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Encryption</label>
                                        <select class="form-control" name="smtp_encryption" {{ $canEdit ? '' : 'disabled' }}>
                                            <option value="tls" {{ $setting->smtp_encryption == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ $setting->smtp_encryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="" {{ empty($setting->smtp_encryption) ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">From Address</label>
                                        <input type="email" class="form-control" name="smtp_from_address" value="{{ $setting->smtp_from_address }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">From Name</label>
                                        <input type="text" class="form-control" name="smtp_from_name" value="{{ $setting->smtp_from_name }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="smtp_active" value="1" id="smtp_active" {{ $setting->smtp_active ? 'checked' : '' }} {{ $canEdit ? '' : 'disabled' }}>
                                    <label class="form-check-label ms-2" for="smtp_active">Enable SMTP</label>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    @if($canEdit)
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Email Settings</button>
                                    @else
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-1"></i>Back</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

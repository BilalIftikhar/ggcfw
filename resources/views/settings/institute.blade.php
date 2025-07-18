@extends('layouts.app')

@section('title', 'Institute Settings')

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
        .bg-success-light {
            background-color: rgba(15, 216, 123, 0.1);
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
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border-bottom: 1px dashed #81c784 !important;
            padding: 1rem 1.5rem;
        }
        .main-content {
            min-height: calc(100vh - 120px);
            padding-bottom: 2rem;
        }
        .custom-image {
            height: 60px;
            margin-top: 5px;
        }
    </style>

    <main class="main main-content">
        <div class="pagetitle">
            <h1>Institute Settings</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Institute</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header card-header-custom">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-gear me-2"></i>Institute Settings
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="alert alert-info-custom border border-dashed mb-4">
                                <i class="bi bi-info-circle me-2"></i> Only users with permission can update these settings.
                            </div>

                            <form action="{{ route('update.institute') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="update" value="institute">
                                @php $canEdit = auth()->user()->can('update_institute_settings'); @endphp

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Institute Name</label>
                                        <input type="text" class="form-control" name="institute_name" value="{{ $setting->institute_name }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tagline</label>
                                        <input type="text" class="form-control" name="tagline" value="{{ $setting->tagline }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="institute_email" value="{{ $setting->institute_email }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="institute_phone" value="{{ $setting->institute_phone }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Website</label>
                                        <input type="text" class="form-control" name="institute_website" value="{{ $setting->institute_website }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="institute_address" rows="2" {{ $canEdit ? '' : 'disabled' }}>{{ $setting->institute_address }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Institute Logo</label><br>
                                        @if ($setting->institute_logo_url)
                                            <img src="{{ $setting->institute_logo_url }}" class="custom-image" alt="Logo">
                                        @endif
                                        @if($canEdit)
                                            <input type="file" name="institute_logo" class="form-control mt-2">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Voucher Logo</label><br>
                                        @if ($setting->voucher_logo_url)
                                            <img src="{{ $setting->voucher_logo_url }}" class="custom-image" alt="Logo">
                                        @endif
                                        @if($canEdit)
                                            <input type="file" name="voucher_logo" class="form-control mt-2">
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Voucher Footer Note</label>
                                        <textarea class="form-control" name="voucher_footer_note" rows="2" {{ $canEdit ? '' : 'disabled' }}>{{ $setting->voucher_footer_note }}</textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Default Currency</label>
                                        <input type="text" class="form-control" name="default_currency" value="{{ $setting->default_currency }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Timezone</label>
                                        <input type="text" class="form-control" name="timezone" value="{{ $setting->timezone }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Academic Year Start</label>
                                        <input type="date" class="form-control" name="academic_year_start" value="{{ $setting->academic_year_start }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Academic Year End</label>
                                        <input type="date" class="form-control" name="academic_year_end" value="{{ $setting->academic_year_end }}" {{ $canEdit ? '' : 'disabled' }}>
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintenance_mode" {{ $setting->maintenance_mode ? 'checked' : '' }} {{ $canEdit ? '' : 'disabled' }}>
                                    <label class="form-check-label ms-2" for="maintenance_mode">Enable Maintenance Mode</label>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    @if($canEdit)
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Settings</button>
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

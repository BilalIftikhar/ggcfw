@extends('layouts.app')

@section('title', 'WhatsApp Settings')

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
            background-color: rgba(25, 135, 84, 0.1);
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
            background: linear-gradient(135deg, #e6f7ee 0%, #c3e6cb 100%);
            border-bottom: 1px dashed #28a745 !important;
            padding: 1rem 1.5rem;
        }
        .main-content {
            min-height: calc(100vh - 120px);
            padding-bottom: 2rem;
        }
    </style>

    <main class="main main-content">
        <div class="pagetitle">
            <h1>WhatsApp Settings</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">WhatsApp Settings</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header card-header-custom">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-whatsapp me-2"></i>WhatsApp API Settings
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="alert alert-info-custom border border-dashed mb-4">
                                <i class="bi bi-info-circle me-2"></i> Configure your WhatsApp API settings for system notifications and communications.
                            </div>

                            <form action="{{ route('update.whatsapp') }}" method="POST">
                                @csrf
                                <input type="hidden" name="update" value="whatsapp">
                                @php $canEdit = auth()->user()->can('update_whatsapp_settings'); @endphp

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">API Key</label>
                                        <input type="text" class="form-control" name="whatsapp_api_key" value="{{ $setting->whatsapp_api_key }}" {{ $canEdit ? '' : 'disabled' }}>
                                        <small class="text-muted">Your WhatsApp Business API key</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">WhatsApp Number</label>
                                        <input type="text" class="form-control" name="whatsapp_number" value="{{ $setting->whatsapp_number }}" {{ $canEdit ? '' : 'disabled' }}>
                                        <small class="text-muted">Registered WhatsApp Business number</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">API URL</label>
                                        <input type="url" class="form-control" name="whatsapp_url" value="{{ $setting->whatsapp_url }}" {{ $canEdit ? '' : 'disabled' }}>
                                        <small class="text-muted">Endpoint for WhatsApp API service</small>
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_active" value="1" id="whatsapp_active" {{ $setting->whatsapp_active ? 'checked' : '' }} {{ $canEdit ? '' : 'disabled' }}>
                                    <label class="form-check-label ms-2" for="whatsapp_active">Enable WhatsApp Notifications</label>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    @if($canEdit)
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update WhatsApp Settings</button>
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

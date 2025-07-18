@extends('layouts.app')

@section('title', 'Assign Permissions to Role')

@section('content')
    <style>
        .module-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .module-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .accordion-button {
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        .accordion-button:not(.collapsed) {
            background-color: #ecfdf5;
            color: #065f46;
        }
        .permission-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .permission-item:last-child {
            border-bottom: none;
        }
        .permission-item:hover {
            background-color: #f9fefa;
        }
        .permission-name {
            font-weight: 500;
            color: #065f46;
        }
        .permission-status {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }
        .status-granted {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-not-granted {
            background-color: #fef2f2;
            color: #991b1b;
        }
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.5em;
        }
        .form-switch .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }
        .action-buttons .btn {
            border-radius: 6px;
            font-weight: 500;
        }
        .sticky-footer {
            position: sticky;
            bottom: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            z-index: 10;
        }
        .selected-count {
            font-weight: 600;
            color: #065f46;
        }
        .module-badge {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 500;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage Permissions</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role Management</a></li>
                    <li class="breadcrumb-item active">Assign Permissions</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Assigning to: <span class="text-success text-capitalize" style="color: #d3b934 !important;">{{ $role->name }}</span>
                                </h5>
                                <small class="text-muted">Toggle to grant or revoke permissions</small>
                            </div>
                            <div class="action-buttons d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success" id="expandAll">
                                    <i class="bi bi-arrows-angle-expand me-1"></i> Expand All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAll">
                                    <i class="bi bi-arrows-angle-contract me-1"></i> Collapse All
                                </button>
                                <div class="vr mx-1"></div>
                                <button type="button" class="btn btn-sm btn-success" id="grantAll">
                                    <i class="bi bi-check-circle me-1"></i> Grant All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="revokeAll">
                                    <i class="bi bi-x-circle me-1"></i> Revoke All
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <form method="POST" action="{{ route('roles.permissions.update', $role->id) }}">
                                @csrf
                                @method('PATCH')

                                <div class="accordion accordion-flush" id="permissionsAccordion">
                                    @foreach($permissionsGroupedByModule as $moduleName => $permissions)
                                        <div class="accordion-item module-card m-3">
                                            <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse{{ $loop->index }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse{{ $loop->index }}">
                                                    <div class="d-flex align-items-center w-100">
                                                        <i class="bi bi-collection me-3 text-success"></i>
                                                        <div class="flex-grow-1">
                                                            {{ $moduleName }}
                                                        </div>
                                                        <span class="module-badge badge rounded-pill me-2">
                                                        {{ count($permissions) }} permissions
                                                    </span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $loop->index }}"
                                                 class="accordion-collapse collapse"
                                                 aria-labelledby="heading{{ $loop->index }}"
                                                 data-bs-parent="#permissionsAccordion">
                                                <div class="accordion-body p-0">
                                                    <div class="list-group list-group-flush">
                                                        @foreach($permissions as $permission)
                                                            <div class="permission-item d-flex justify-content-between align-items-center">
                                                                <div class="flex-grow-1">
                                                                    <div class="permission-name">{{ $permission->name }}</div>
                                                                    <div class="mt-2">
                                                                    <span class="permission-status {{ $role->hasPermissionTo($permission->name) ? 'status-granted' : 'status-not-granted' }}">
                                                                        <i class="bi {{ $role->hasPermissionTo($permission->name) ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                                                        {{ $role->hasPermissionTo($permission->name) ? 'Currently granted' : 'Not granted' }}
                                                                    </span>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox"
                                                                               name="permissions[]"
                                                                               value="{{ $permission->name }}"
                                                                               id="perm_{{ $permission->id }}"
                                                                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="sticky-footer p-3 mt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted me-1">Selected:</span>
                                            <span class="selected-count" id="selectedCount">0</span>
                                        </div>
                                        <div>
                                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary me-2">
                                                <i class="bi bi-arrow-left me-1"></i> Back
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-save me-1"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#expandAll').click(function() {
                $('.accordion-collapse').addClass('show');
                $('.accordion-button').removeClass('collapsed');
            });
            $('#collapseAll').click(function() {
                $('.accordion-collapse').removeClass('show');
                $('.accordion-button').addClass('collapsed');
            });
            $('#grantAll').click(function() {
                $('input[name=\"permissions[]\"]').prop('checked', true).trigger('change');
                updateSelectedCount();
                updateStatusBadges();
            });
            $('#revokeAll').click(function() {
                $('input[name=\"permissions[]\"]').prop('checked', false).trigger('change');
                updateSelectedCount();
                updateStatusBadges();
            });
            function updateSelectedCount() {
                const count = $('input[name=\"permissions[]\"]:checked').length;
                $('#selectedCount').text(count);
            }
            function updateStatusBadges() {
                $('input[name=\"permissions[]\"]').each(function() {
                    const statusBadge = $(this).closest('.permission-item').find('.permission-status');
                    if ($(this).is(':checked')) {
                        statusBadge.removeClass('status-not-granted').addClass('status-granted')
                            .html('<i class=\"bi bi-check-circle-fill me-1\"></i> Currently granted');
                    } else {
                        statusBadge.removeClass('status-granted').addClass('status-not-granted')
                            .html('<i class=\"bi bi-x-circle-fill me-1\"></i> Not granted');
                    }
                });
            }
            updateSelectedCount();
            updateStatusBadges();
            $(document).on('change', 'input[name=\"permissions[]\"]', function() {
                updateSelectedCount();
                updateStatusBadges();
            });
        });
    </script>
@endsection

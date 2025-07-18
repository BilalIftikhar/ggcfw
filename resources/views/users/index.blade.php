@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>User Management</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card shadow-sm">
                <div class="card-body pt-4">
                    <ul class="nav nav-tabs nav-justified" id="roleTabs" role="tablist">
                        @foreach($roles as $index => $role)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if($index == 0) active @endif" id="tab-{{ $role->id }}"
                                        data-bs-toggle="tab" data-bs-target="#role-{{ $role->id }}" type="button" role="tab"
                                        style="color: #3adb76 !important;">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $role->name }}
                                    <span class="badge bg-success rounded-pill ms-1">{{ $role->users->count() }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-3" id="roleTabsContent">
                        @foreach($roles as $index => $role)
                            <div class="tab-pane fade @if($index == 0) show active @endif" id="role-{{ $role->id }}"
                                 role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle user-table" id="datatable-{{ $role->id }}">
                                        <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($role->users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($role->is_admin)
                                                        System Admin
                                                    @elseif($role->is_teaching)
                                                        {{ $user->teacher->name ?? 'N/A' }}
                                                    @elseif($role->is_student)
                                                        {{ $user->student->name ?? 'N/A' }}
                                                    @else
                                                        {{ $user->employee->name ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td>{{ $user->username }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input status-toggle" type="checkbox"
                                                               role="switch" data-user-id="{{ $user->id }}"
                                                            @checked($user->status)>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('change_username')
                                                            <button
                                                                class="btn btn-sm btn-outline-success edit-username-btn"
                                                                data-user-id="{{ $user->id }}"
                                                                data-username="{{ $user->username }}">
                                                                <i class="bi bi-pencil"></i> Username
                                                            </button>
                                                        @endcan
                                                        @can('change_password')
                                                            <button
                                                                class="btn btn-sm btn-outline-secondary edit-password-btn"
                                                                data-user-id="{{ $user->id }}">
                                                                <i class="bi bi-key"></i> Password
                                                            </button>
                                                        @endcan
                                                        @can('user_impersonate')
                                                            @if(auth()->id() !== $user->id)
                                                                <a href="{{ route('users.impersonate', $user->id) }}"
                                                                   class="btn btn-sm btn-outline-secondary"
                                                                   onclick="return confirm('Are you sure you want to impersonate {{ $user->username }}?');">
                                                                    <i class="bi bi-person-badge"></i> Impersonate
                                                                </a>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="bi bi-people display-5 text-muted"></i>
                                                        <p class="mt-2 mb-0 text-muted">No users found under this role.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Change Username Modal -->
    <div class="modal fade" id="changeUsernameModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="changeUsernameForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-person"></i> Change Username</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newUsername" class="form-label">New Username</label>
                            <input type="text" name="username" id="newUsername" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="changePasswordForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light text-dark">
                        <h5 class="modal-title"><i class="bi bi-shield-lock"></i> Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="newPassword" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="newPasswordConfirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="newPasswordConfirmation"
                                       class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            function initializeDataTable(table) {
                if (!$(table).hasClass('dt-initialized')) {
                    console.log('Initializing DataTable on:', table.id);

                    $(table).DataTable({
                        dom: '<"row mb-2"<"col-md-6"B><"col-md-6"f>>rt<"row mt-2"<"col-md-6"l><"col-md-6"p>>',
                        buttons: [
                            { extend: 'copy', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-files me-1"></i> Copy' },
                            { extend: 'csv', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV' },
                            { extend: 'excel', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-file-excel me-1"></i> Excel' },
                            { extend: 'print', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-printer me-1"></i> Print' },
                        ],
                        responsive: true,
                        language: {
                            searchPlaceholder: 'Search users...',
                            search: '',
                        },
                        columns: [
                            { data: 0, defaultContent: '' }, // #
                            { data: 1, defaultContent: '' }, // Name
                            { data: 2, defaultContent: '' }, // Username
                            { data: 3, defaultContent: '' }, // Email
                            { data: 4, defaultContent: '' }, // Status
                            { data: 5, defaultContent: '', orderable: false }  // Actions
                        ]
                    });

                    $(table).addClass('dt-initialized');
                } else {
                    console.log('Already initialized:', table.id);
                }
            }

            // Initialize DataTable for initially visible table
            $('.tab-pane.active .user-table').each(function() {
                initializeDataTable(this);
            });

            // Listen for tab switch
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                console.log('Tab switched:', e.target);

                let targetId = $(e.target).attr('data-bs-target');
                console.log('Target ID:', targetId);

                let table = $(targetId).find('table.user-table');
                console.log('Found table:', table.attr('id'));

                initializeDataTable(table);

                setTimeout(function() {
                    console.log('Adjusting columns for visible tables.');
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
                }, 200);
            });

            $('.toggle-password').click(function() {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');
                input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
                icon.toggleClass('bi-eye bi-eye-slash');
            });

            $('.edit-username-btn').click(function() {
                $('#newUsername').val($(this).data('username'));
                $('#changeUsernameForm').attr('action', `/users/${$(this).data('user-id')}/update-username`);
                $('#changeUsernameModal').modal('show');
            });

            $('.edit-password-btn').click(function() {
                $('#changePasswordForm').trigger('reset');
                $('#changePasswordForm').attr('action', `/users/${$(this).data('user-id')}/update-password`);
                $('#changePasswordModal').modal('show');
            });

            $('.status-toggle').change(function() {
                let toggle = $(this);
                let userId = toggle.data('user-id');
                let newStatus = toggle.is(':checked') ? 1 : 0;
                Swal.fire({
                    title: 'Confirm Status Change',
                    html: `Are you sure you want to <strong>${newStatus ? 'activate' : 'deactivate'}</strong> this user?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/users/${userId}/change-status`,
                            type: 'PATCH',
                            data: { _token: '{{ csrf_token() }}', status: newStatus },
                            success: function(response) {
                                Swal.fire('Updated!', response.message, 'success');
                            },
                            error: function() {
                                toggle.prop('checked', !newStatus);
                                Swal.fire('Error', 'Something went wrong', 'error');
                            }
                        });
                    } else {
                        toggle.prop('checked', !newStatus);
                    }
                });
            });
        });
    </script>
@endsection

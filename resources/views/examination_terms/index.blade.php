@extends('layouts.app')

@section('title', 'Examination Terms')

@section('content')
    <style>
        .main { background-color: #f8f9fa; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: none; }
        .card-title { color: #2c3e50; font-weight: 600; }
        .table { margin-top: 20px; border-collapse: separate; border-spacing: 0; }
        .table thead th { background-color: #e8f5e9; color: #2c3e50; font-weight: 600; border-bottom: 1px solid #c8e6c9 !important; border-top: none; vertical-align: middle; padding: 12px 15px; }
        .table tbody td { padding: 12px 15px; vertical-align: middle; border-color: #e9ecef !important; }
        .table-hover tbody tr:hover { background-color: rgba(233, 245, 233, 0.3); }
        .badge { padding: 6px 10px; font-size: 0.85em; font-weight: 500; border-radius: 4px; }
        .btn { border-radius: 6px; font-weight: 500; padding: 8px 16px; }
        .btn i { margin-right: 5px; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-outline-primary { color: #2c7be5; border-color: #2c7be5; }
        .btn-outline-secondary { color: #6c757d; border-color: #6c757d; }
        .btn-outline-danger { color: #dc3545; border-color: #dc3545; }
        .action-buttons { display: flex; gap: 8px; justify-content: flex-end; }
        .modal-content { border-radius: 10px; border: none; }
        .modal-header { border-radius: 10px 10px 0 0 !important; padding: 15px 20px; }
        .modal-title { font-weight: 600; }
        .modal-body { padding: 20px; }
        .form-control, .form-select { border-radius: 6px; padding: 8px 12px; border: 1px solid #dfe2e6; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25); border-color: #28a745; }
        .dataTables_wrapper .dataTables_filter input, .dataTables_wrapper .dataTables_length select { border-radius: 6px; padding: 6px 12px; border: 1px solid #dfe2e6; }
        @media (max-width: 767.98px) {
            .table-responsive { border: none; }
            .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_length { float: none !important; margin-bottom: 15px; }
            .action-buttons { justify-content: flex-start; margin-top: 10px; }
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle bg-white p-3 rounded shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold text-dark">Examination Terms</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active text-muted">Examination Terms</li>
                        </ol>
                    </nav>
                </div>
                @can('create_examination_term')
                    <button type="button" class="btn btn-success" id="openModalBtn">
                        <i class="bi bi-plus-circle"></i> Add Examination Term
                    </button>
                @endcan
            </div>
        </div>

        <section class="section dashboard mt-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title fw-semibold mb-0">List of Examination Terms</h5>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-outline-secondary" id="copyBtn"><i class="bi bi-files"></i> Copy</button>
                                    <button type="button" class="btn btn-outline-secondary" id="exportBtn"><i class="bi bi-file-earmark-excel"></i> Export</button>
                                    <button type="button" class="btn btn-outline-secondary" id="printBtn"><i class="bi bi-printer"></i> Print</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="termsTable" class="table table-hover align-middle" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Examination Session</th>
                                        <th>Description</th>
                                        <th>Sessional Enabled</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($terms as $index => $term)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $term->title }}</td>
                                            <td>{{ $term->session->title }}</td>
                                            <td>{{ Str::limit($term->description, 50) }}</td>
                                            <td>
                                                @can('update_examination_term')
                                                    <form action="{{ route('examination-term.toggle-sessional', $term->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm {{ $term->enable_sessional ? 'btn-success' : 'btn-outline-secondary' }}">
                                                            {{ $term->enable_sessional ? 'Enabled' : 'Disabled' }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge {{ $term->enable_sessional ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $term->enable_sessional ? 'Enabled' : 'Disabled' }}
                                            </span>
                                                @endcan
                                            </td>
                                            <td class="text-end">
                                                <div class="action-buttons">
                                                    @can('update_examination_term')
                                                        <button type="button" class="btn btn-sm btn-outline-primary editBtn"
                                                                data-id="{{ $term->id }}"
                                                                data-title="{{ $term->title }}"
                                                                data-description="{{ $term->description }}"
                                                                data-session="{{ $term->examination_session_id }}"
                                                                data-enable-sessional="{{ $term->enable_sessional }}"
                                                                data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                    @can('delete_examination_term')
                                                        <form action="{{ route('examination-term.destroy', $term->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-bs-toggle="tooltip" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Create/Edit Modal -->
        <div class="modal fade" id="termModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="termModalLabel">Add Examination Term</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="termForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">
                            <div class="mb-3">
                                <label for="examination_session_id" class="form-label">Examination Session <span class="text-danger">*</span></label>
                                <select class="form-select" name="examination_session_id" id="examination_session_id" required>
                                    <option value="">-- Select Examination Session --</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}">{{ $session->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" id="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="enable_sessional" id="enable_sessional" value="1">
                                <label class="form-check-label" for="enable_sessional">Enable Sessional</label>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#termsTable').DataTable({
                responsive: true,
                columnDefs: [{ orderable: false, targets: -1 }],
                language: {
                    search: '<i class="bi bi-search"></i>',
                    searchPlaceholder: 'Search terms...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: { previous: '<i class="bi bi-chevron-left"></i>', next: '<i class="bi bi-chevron-right"></i>' }
                },
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-select');
                }
            });

            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#copyBtn').click(function() { table.button('.buttons-copy').trigger(); });
            $('#exportBtn').click(function() { table.button('.buttons-csv').trigger(); });
            $('#printBtn').click(function() { table.button('.buttons-print').trigger(); });

            $('#openModalBtn').click(function () {
                $('#termForm').attr('action', '{{ route('examination-term.store') }}');
                $('#_method').val('POST');
                $('#termForm')[0].reset();
                $('#enable_sessional').prop('checked', false);
                $('#termModalLabel').text('Add Examination Term');
                $('#termModal').modal('show');
            });

            $('.editBtn').click(function () {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const description = $(this).data('description');
                const sessionId = $(this).data('session');
                const enableSessional = $(this).data('enable-sessional');

                $('#title').val(title);
                $('#description').val(description);
                $('#examination_session_id').val(sessionId);
                $('#enable_sessional').prop('checked', enableSessional == 1);
                $('#_method').val('PUT');
                $('#termForm').attr('action', `/examination-term/${id}`);
                $('#termModalLabel').text('Edit Examination Term');
                $('#termModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the examination term and all associated data.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-outline-secondary' }
                }).then((result) => { if (result.isConfirmed) { form.submit(); } });
            });
        });
    </script>
@endsection

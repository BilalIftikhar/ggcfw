@extends('layouts.app')

@section('title', 'Fee Templates')

@section('content')
    <style>
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table th, .table td {
            vertical-align: middle !important;
            border: 1px solid #dee2e6;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 2px solid #a5d6a7 !important;
            color: #2e7d32;
            font-weight: 600;
        }
        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }
        .btn {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .modal-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-bottom: none;
        }
        .form-control:focus {
            border-color: #4caf50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .action-buttons {
            min-width: 140px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(232, 245, 233, 0.3);
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Fee Templates</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Fee Templates</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_fee_template')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-end" style="padding: 1.5rem">
                                <button type="button" class="btn btn-success" id="openModalBtn">
                                    <i class="bi bi-plus-circle"></i> Add Fee Template
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">List of Fee Templates</h5>
                            <div class="table-responsive">
                                <table id="templatesTable" class="table table-striped table-bordered table-hover align-middle w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($feeTemplates as $index => $template)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $template->title }}</td>
                                            <td>{{ Str::limit($template->description, 50) }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center action-buttons">
                                                    @can('view_fee_template')
                                                        <a href="{{ route('fee-templates.show', $template->id) }}" class="btn btn-sm btn-outline-info me-2" data-bs-toggle="tooltip" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete_fee_template')
                                                        <form action="{{ route('fee-templates.destroy', $template->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="tooltip" title="Delete">
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

        <!-- Modal -->
        <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="templateModalLabel">Add Fee Template</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="templateForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="POST">

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" id="title" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" name="description" id="description">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Select Fees</label>
                                <ul class="nav nav-tabs" id="feeGroupTabs" role="tablist">
                                    @foreach ($feeGroups as $groupName => $groupFees)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link @if ($loop->first) active @endif" id="tab-{{ Str::slug($groupName) }}-tab" data-bs-toggle="tab" data-bs-target="#tab-{{ Str::slug($groupName) }}" type="button" role="tab">
                                                {{ $groupName }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content mt-2 border rounded p-3" id="feeGroupTabsContent" style="max-height: 400px; overflow-y: auto;">
                                    @foreach ($feeGroups as $groupName => $groupFees)
                                        <div class="tab-pane fade @if ($loop->first) show active @endif" id="tab-{{ Str::slug($groupName) }}" role="tabpanel">
                                            @php
                                                $feesByType = $groupFees->groupBy(fn($fee) => optional($fee->feeType)->name ?? 'No Type');
                                            @endphp
                                            @foreach ($feesByType as $typeName => $feesList)
                                                <h6 class="mt-2">{{ $typeName }}</h6>
                                                <div class="row">
                                                    @foreach ($feesList as $fee)
                                                        <div class="col-md-4">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="fee_ids[]" value="{{ $fee->id }}" id="fee-{{ $fee->id }}">
                                                                <label class="form-check-label" for="fee-{{ $fee->id }}">
                                                                    {{ $fee->feeType->name ?? 'No Type' }} - {{ $fee->amount }} PKR
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="modal-footer border-top-0 mt-3">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save</button>
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
            $('#templatesTable').DataTable({
                dom: '<"row mb-3"<"col-md-6 d-flex align-items-center gap-2"B><"col-md-6 d-flex justify-content-md-end justify-content-start"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-md-end justify-content-start"p>>',
                buttons: [
                    { extend: 'copy', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-files me-1"></i> Copy' },
                    { extend: 'csv', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV' },
                    { extend: 'print', className: 'btn btn-sm btn-light border', text: '<i class="bi bi-printer me-1"></i> Print' }
                ],
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Search fee templates...",
                    lengthMenu: "Show _MENU_ records",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: -1, className: 'text-center' }
                ]
            });

            $('#openModalBtn').click(function () {
                $('#templateForm').attr('action', '{{ route('fee-templates.store') }}');
                $('#_method').val('POST');
                $('#templateForm')[0].reset();
                $('#templateModalLabel').text('Add Fee Template');
                $('#templateModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Confirm Deletion',
                    text: "Are you sure you want to delete this fee template? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

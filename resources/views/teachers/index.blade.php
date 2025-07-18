@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
    <style>
        .table th, .table td {
            vertical-align: middle !important;
        }
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 1px solid #c8e6c9;
        }
        .dataTables_scrollHead {
            border-top: 1px solid #c8e6c9 !important;
            background-color: #e8f5e9;
        }
        .badge {
            font-size: 0.85em;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dt-buttons {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dt-buttons {
            margin-right: 15px;
        }
        .dataTables_wrapper .table {
            margin-top: 20px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
        .img-thumbnail {
            width: 100px;    /* increased from 60px */
            height: 100px;   /* increased from 60px */
            object-fit: cover;
        }
    </style>


    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Teachers</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Teachers</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_employee')
                <div class="mb-3">
                    <a href="{{ route('teachers.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Teacher
                    </a>
                </div>
            @endcan

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of Teachers</h5>

                    <div class="table-responsive">
                        <table id="teachersTable" class="table table-bordered table-hover w-100 align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>CNIC</th>
                                <th>Designation</th>
                                <th>Work Contact</th>
                                <th>Home Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($teachers as $index => $teacher)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($teacher->hasMedia('employee'))
                                            <img src="{{ $teacher->getFirstMediaUrl('employee') }}"
                                                 alt="{{ $teacher->name }}"
                                                 class="img-thumbnail rounded-circle">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}"
                                                 alt="Default Avatar"
                                                 class="img-thumbnail rounded-circle">
                                        @endif
                                    </td>
                                    <td>{{ $teacher->name }}</td>
                                    <td>{{ $teacher->cnic }}</td>
                                    <td>{{ $teacher->designation }}</td>
                                    <td>{{ $teacher->work_contact }}</td>
                                    <td>{{ $teacher->home_contact }}</td>
                                    <td>
                                        <span class="badge bg-{{ $teacher->is_active ? 'success' : 'secondary' }}">
                                            {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">

                                            @can('view_teacher')
                                                <a href="{{ route('teachers.show', $teacher->id) }}"
                                                   class="btn btn-outline-info me-2"
                                                   title="View Teacher">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endcan
                                            @can('update_employee')
                                                <a href="{{ route('teachers.edit', $teacher->id) }}"
                                                   class="btn btn-outline-primary" title="Edit Teacher">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                            @can('delete_employee')
                                                <form action="{{ route('teachers.destroy', $teacher->id) }}"
                                                      method="POST" class="deleteForm d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete Teacher">
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
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#teachersTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bfl>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-files"></i> Copy',
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-file-earmark-excel"></i> CSV',
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-printer"></i> Print',
                    }
                ],
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [-1, 1] }
                ]
            });

            $('.deleteForm').submit(function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

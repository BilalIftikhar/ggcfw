@extends('layouts.app')

@section('title', 'Employees')

@section('content')

    <style>
        .table th, .table td {
            vertical-align: middle !important;
        }
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 1px solid #c8e6c9;
        }
        .img-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .badge {
            font-size: 0.85em;
            font-weight: 500;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Employees</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Employees</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @can('create_employee')
                <div class="mb-3">
                    <a href="{{ route('employees.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Employee
                    </a>
                </div>
            @endcan

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of Employees</h5>

                    <div class="table-responsive">
                        <table id="employeesTable" class="table table-bordered table-hover w-100 align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>CNIC</th>
                                <th>Designation</th>
                                <th>Home Contact</th>
                                <th>Work Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($employees as $index => $employee)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($employee->hasMedia('employee'))
                                            <img src="{{ $employee->getFirstMediaUrl('employee') }}"
                                                 alt="{{ $employee->name }}"
                                                 class="img-thumbnail rounded-circle">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}"
                                                 alt="Default Avatar"
                                                 class="img-thumbnail rounded-circle">
                                        @endif
                                    </td>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->cnic_no }}</td>
                                    <td>{{ $employee->designation }}</td>
                                    <td>{{ $employee->home_contact }}</td>
                                    <td>{{ $employee->work_contact }}</td>
                                    <td>
                                    <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                        {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Employee Actions">
                                        @can('view_employee')
                                            <a href="{{ route('employees.show', $employee->id) }}"
                                               class="btn btn-outline-info me-2"
                                               title="View Employee">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan

                                        @can('update_employee')
                                                <a href="{{ route('employees.edit', $employee->id) }}"
                                                   class="btn btn-outline-primary me-2"
                                                   title="Edit Employee">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endcan
                                            @can('delete_employee')
                                                <form action="{{ route('employees.destroy', $employee->id) }}"
                                                      method="POST" class="deleteForm d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete Employee">
                                                        <i class="bi bi-trash3"></i>
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
            $('#employeesTable').DataTable({
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

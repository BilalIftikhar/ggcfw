@extends('layouts.app')

@section('title', 'Assignment Submissions')

@section('content')
    <style>
        /* Table styling */
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

        /* DataTables wrapper spacing */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dt-buttons {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dt-buttons {
            margin-right: 15px;
        }
        .dataTables_wrapper .table {
            margin-top: 20px !important;
        }

        /* Borders and visual elements */
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
        .dataTables_wrapper .dataTables_info {
            margin-left: 15px;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-right: 15px;
        }
        .btn-light.border {
            border-color: #ced4da !important;
        }

        /* Card title enhancement */
        .card-title {
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Submissions - {{ $assignment->title }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Assignments</a></li>
                    <li class="breadcrumb-item active">Submissions</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student Submissions</h5>

                    <div class="table-responsive">
                        <table id="submissionsTable" class="table table-bordered table-hover align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>CNIC</th>
                                <th>Status</th>
                                <th>Submission</th>
                                <th>Submitted At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->user->name ?? 'N/A' }}</td>
                                    <td>{{ $student->cnic ?? 'N/A' }}</td>
                                    <td>
                                        @if($student->submissions->isNotEmpty())
                                            <span class="badge bg-success">Submitted</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->submissions->isNotEmpty())
                                            @foreach($student->submissions as $submission)
                                                @foreach($submission->getMedia('submission') as $media)
                                                    <a href="{{ $media->getFullUrl() }}" class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                @endforeach
                                            @endforeach
                                        @else
                                            <span class="text-muted">No submission</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->submissions->isNotEmpty())
                                            {{ $student->submissions->first()->created_at->format('d M Y, h:i A') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
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
        $(document).ready(function() {
            // Initialize DataTable with buttons
            $('#submissionsTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bfl>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-files me-1"></i> Copy',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        title: 'Assignment Submissions - {{ $assignment->title }}'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        title: 'Assignment Submissions - {{ $assignment->title }}'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-file-excel me-1"></i> Excel',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        title: 'Assignment Submissions - {{ $assignment->title }}'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-light border',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        title: 'Assignment Submissions - {{ $assignment->title }}',
                        customize: function (win) {
                            $(win.document.body).find('h1').css({
                                'text-align': 'center',
                                'margin-top': '20px'
                            });
                            $(win.document.body).find('table').addClass('display').css('font-size', '12px');
                        }
                    }
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search submissions...",
                }
            });
        });
    </script>
@endsection

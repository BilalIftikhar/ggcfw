@extends('layouts.app')

@section('title', "{$program->name} ({$program->academicSession->name})")

@section('content')
    <style>
        .program-card {
            border-radius: 8px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: none;
        }
        .program-header {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            padding: 1rem 1.5rem; /* Reduced padding */
            text-align: center;
            margin-bottom: 0;
        }
        .program-title {
            font-size: 1.5rem; /* Slightly smaller */
            font-weight: 700;
            margin-bottom: 0.2rem;
        }
        .program-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        .study-level-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.3rem 0.8rem; /* Smaller badge */
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 0.4rem; /* Reduced margin */
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .class-section {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 1.25rem;
            overflow: hidden;
            background-color: white;
        }
        .class-header {
            background-color: #2c3e50;
            color: white;
            padding: 0.7rem 1.25rem; /* More compact */
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.3px;
        }
        .course-table {
            width: 100%;
            border-collapse: collapse;
        }
        .course-table th,
        .course-table td {
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem; /* Slightly tighter padding */
            vertical-align: middle;
        }
        .course-table thead th {
            background-color: #27ae60;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem; /* Slightly smaller */
            letter-spacing: 0.3px;
            text-align: left;
        }
        .course-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .course-table tbody tr:hover {
            background-color: #f1f8ff;
        }
        .credit-hours {
            text-align: center;
            white-space: nowrap;
            font-weight: 500;
            color: #2c3e50;
        }
        .lab-hours {
            color: #7f8c8d;
            font-size: 0.8em; /* Slightly smaller */
        }
        .no-courses {
            color: #95a5a6;
            font-style: italic;
            padding: 1rem;
            text-align: center;
        }
        .course-code {
            font-weight: 600;
            color: #2980b9;
        }
        .card-body {
            padding: 0;
        }
        .table-container {
            padding: 1.25rem; /* Slightly reduced */
        }
    </style>
    <main id="main" class="main">
        <div class="pagetitle">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('programs.index') }}">Programs</a></li>
                    <li class="breadcrumb-item active">Course Path</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="program-card card">
                <!-- Program Header - Now more compact -->
                <div class="program-header">
                    <h1 class="program-title">{{ $program->name }}s({{ $program->academicSession->name }})</h1>
                    <p class="program-subtitle"></p>
                    <span class="study-level-badge">Study Level: {{ $program->studyLevel->name }}</span>
                </div>

                <div class="table-container">
                    @foreach ($program->classes as $class)
                        <div class="class-section">
                            <!-- Class Header -->
                            <div class="class-header">
                                {{ $class->name }}
                            </div>

                            <!-- Courses Table -->
                            <table class="course-table">
                                <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Credit Hours</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($class->courses as $course)
                                    <tr>
                                        <td class="course-code">{{ $course->code }}</td>
                                        <td>{{ $course->name }}</td>
                                        <td class="credit-hours">
                                            <span>{{ $course->credit_hours }}</span>
                                            @if ($course->has_lab)
                                                <span class="lab-hours">+ {{ $course->lab_credit_hours }} lab</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="no-courses">No courses assigned to this class yet</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection

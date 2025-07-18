@extends('layouts.app')

@section('title', 'Manage Courses - ' . $student->name)

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage Courses for {{ $student->name }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Manage Courses</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <form method="POST" action="{{ route('students.courses.update', $student->id) }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Enrolled Courses</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="border p-3 rounded bg-light">
                                    <h6 class="fw-bold mb-3 text-success">Mandatory Courses</h6>
                                    @if($mandatoryCourses->isEmpty())
                                        <p class="text-muted">No mandatory courses found.</p>
                                    @else
                                        <ul class="list-group">
                                            @foreach($mandatoryCourses as $course)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>{{ $course->name }}</span>
                                                    <span class="badge bg-success">Mandatory</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mt-4 mt-md-0">
                                <div class="border p-3 rounded bg-light">
                                    <h6 class="fw-bold mb-3 text-primary">Optional Courses</h6>
                                    @if($optionalCourses->isEmpty())
                                        <p class="text-muted">No optional courses available.</p>
                                    @else
                                        <ul class="list-group">
                                            @foreach($optionalCourses as $course)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>{{ $course->name }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="optional_courses[]"
                                                               value="{{ $course->id }}"
                                                               id="optional_{{ $course->id }}"
                                                               @if(in_array($course->id, $currentCourseIds)) checked @endif>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left-circle"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Enrollment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>
@endsection

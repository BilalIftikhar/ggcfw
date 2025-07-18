@extends('layouts.app')

@section('title', 'Assignment Details')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assignment Details</h5>
                <a href="{{ route('assignments.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ $assignment->title }}</h5>
                <p class="card-text"><strong>Program:</strong> {{ $assignment->program?->name }}</p>
                <p class="card-text"><strong>Course:</strong> {{ $assignment->course?->name }}</p>
                <p class="card-text"><strong>Section:</strong> {{ $assignment->courseSection?->name }}</p>
                <p class="card-text"><strong>Teacher:</strong> {{ $assignment->teacher?->full_name }}</p>
                <p class="card-text"><strong>Due Date:</strong> {{ $assignment->due_date }}</p>
                <p class="card-text"><strong>Description:</strong></p>
                <p>{!! nl2br(e($assignment->description)) !!}</p>

                @if ($assignment->getFirstMediaUrl('attachment'))
                    <p class="card-text mt-3">
                        <strong>Attachment:</strong>
                        <a href="{{ $assignment->getFirstMediaUrl('attachment') }}" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                            View Attachment
                        </a>
                    </p>
                @endif

                @if(auth()->user()->hasRole('student'))
                    <hr>

                    @if (!$alreadySubmitted)
                        <form id="submissionForm" action="{{ route('assignment-submissions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

                            <div class="mb-3">
                                <label for="file" class="form-label">Upload File</label>
                                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                                <small class="text-muted">Allowed formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Assignment</button>
                        </form>
                    @elseif($submittedFileUrl)
                        <div class="mt-3">
                            <label class="form-label">You have already submitted this assignment:</label><br>
                            <a href="{{ $submittedFileUrl }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-download me-1"></i> Download Your Submission
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('submissionForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Once uploaded, your assignment cannot be changed.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        });
    </script>
@endsection

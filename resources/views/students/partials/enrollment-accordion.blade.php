<div class="accordion mb-3" id="enrollmentAccordion{{ $enrollment->id }}">
    <div class="accordion-item border-start border-primary border-0 shadow-sm">
        <h2 class="accordion-header" id="heading{{ $enrollment->id }}">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $enrollment->id }}" aria-expanded="false" aria-controls="collapse{{ $enrollment->id }}">
                Enrollment #{{ $enrollment->id }} - {{ $enrollment->program->name ?? 'N/A' }} - {{ $enrollment->programClass->name ?? 'N/A' }}
            </button>
        </h2>

        <div id="collapse{{ $enrollment->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $enrollment->id }}" data-bs-parent="#enrollmentAccordion{{ $enrollment->id }}">
            <div class="accordion-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-label">Academic Session</div>
                        <div class="info-value">{{ $enrollment->academicSession->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Examination Session</div>
                        <div class="info-value">{{ $enrollment->examinationSession->title ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Enrolled On</div>
                        <div class="info-value">{{ $enrollment->enrolled_on ? $enrollment->enrolled_on->format('d M, Y') : 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="badge bg-{{ $enrollment->status === 'enrolled' ? 'success' : ($enrollment->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Courses and Teachers --}}
                @if($enrollment->details->count())
                    <h6 class="text-primary mt-4">Course Details</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Section</th>
                                <th>Mandatory?</th>
                                <th>Teachers</th>
                                <th>Course Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($enrollment->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->course->name ?? 'N/A' }}</td>
                                    <td>{{ $detail->courseSection?->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $detail->is_mandatory ? 'success' : 'warning' }}">
                                            {{ $detail->is_mandatory ? 'Yes' : 'Optional' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($detail->courseSection && $detail->courseSection->teachers)
                                            <ul class="mb-0 ps-3">
                                                <li>{{ $detail->courseSection->teachers->name ?? $detail->courseSection->teachers->name }}</li>
                                            </ul>
                                        @else
                                            <span class="text-muted">No teacher assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($detail->status)
                                            @case('enrolled')
                                                <span class="badge bg-primary">{{ $detail->status }}</span>
                                                @break
                                            @case('dropped')
                                                <span class="badge bg-danger">{{ $detail->status }}</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-warning text-dark">{{ $detail->status }}</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">{{ $detail->status }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $detail->status }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No courses found for this enrollment.</p>
                @endif
            </div>
        </div>
    </div>
</div>

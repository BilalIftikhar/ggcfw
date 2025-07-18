@extends('layouts.app')

@section('title', 'Create Attendance')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Create Attendance</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Select Attendance Type</h5>

                    @php
                        $isDaily = ($attendanceType ?? 'daily') === 'daily';
                        $isSubject = ($attendanceType ?? '') === 'subject';
                    @endphp

                        <!-- Tabs -->
                    <ul class="nav nav-tabs" id="attendanceTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link {{ $isDaily ? 'active' : '' }}" id="daily-tab"
                                    data-bs-toggle="tab" data-bs-target="#daily"
                                    type="button" role="tab">Daily Attendance</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link {{ $isSubject ? 'active' : '' }}" id="subject-tab"
                                    data-bs-toggle="tab" data-bs-target="#subject"
                                    type="button" role="tab">Subject-Wise Attendance</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content pt-3" id="attendanceTabsContent">
                        <!-- Daily Attendance Tab -->
                        <div class="tab-pane fade {{ $isDaily ? 'show active' : '' }}" id="daily" role="tabpanel">
                            @include('attendance.partials.daily-create')
                        </div>

                        <!-- Subject-wise Attendance Tab -->
                        <div class="tab-pane fade {{ $isSubject ? 'show active' : '' }}" id="subject" role="tabpanel">
                            @include('attendance.partials.subject-create')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Bootstrap tab activation on page load
            @if($isSubject)
            $('button[data-bs-target="#subject"]').tab('show');
            @else
            $('button[data-bs-target="#daily"]').tab('show');
            @endif

            // ======================= Shared: Attendance Radio Save =======================
            $(document).on('change', '.attendance-radio', function () {
                const attendanceId = $(this).data('id');
                const day = $(this).data('day');
                const status = $(this).val();

                const isSubjectTab = $('#subject-tab').hasClass('active');
                const url = isSubjectTab
                    ? "{{ route('attendance.subject.update') }}"
                    : "{{ route('attendance.daily.update') }}";

                const postData = isSubjectTab
                    ? {
                        _token: "{{ csrf_token() }}",
                        attendance_id: attendanceId,
                        status: status
                    }
                    : {
                        _token: "{{ csrf_token() }}",
                        attendance_id: attendanceId,
                        day: day,
                        status: status
                    };

                $.ajax({
                    url: url,
                    type: "POST",
                    data: postData,
                    success: function (res) {
                        toastr.success('Attendance updated successfully!');
                    },
                    error: function () {
                        toastr.error('Failed to update attendance.');
                    }
                });
            });

            // ======================= Subject-Wise Tab Scoped Dropdowns =======================
            const $subjectPane = $('#subject');
            const $s_session = $subjectPane.find('.academic-session');
            const $s_level = $subjectPane.find('.study-level');
            const $s_program = $subjectPane.find('.program');
            const $s_class = $subjectPane.find('.program-class');
            const $s_course = $subjectPane.find('.course');
            const $s_section = $subjectPane.find('.course-section');
            const $s_slot = $subjectPane.find('.timetable');

            function resetDropdowns($dropdowns) {
                $dropdowns.prop('disabled', true).empty().append('<option value="">Select...</option>');
            }

            resetDropdowns($s_level.add($s_program).add($s_class).add($s_course).add($s_section).add($s_slot));

            $s_session.on('change', function () {
                const id = $(this).val();
                resetDropdowns($s_level.add($s_program).add($s_class).add($s_course).add($s_section).add($s_slot));
                if (!id) return;
                $.get(`/ajax-study-levels?academic_session_id=${id}`, function (data) {
                    $s_level.prop('disabled', false);
                    $.each(data, (i, d) => $s_level.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            $s_level.on('change', function () {
                const id = $(this).val();
                resetDropdowns($s_program.add($s_class).add($s_course).add($s_section).add($s_slot));
                if (!id) return;
                $.get(`/ajax-programs?study_level_id=${id}`, function (data) {
                    $s_program.prop('disabled', false);
                    $.each(data, (i, d) => $s_program.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            $s_program.on('change', function () {
                const id = $(this).val();
                resetDropdowns($s_class.add($s_course).add($s_section).add($s_slot));
                if (!id) return;
                $.get(`/program-classes?program_id=${id}`, function (data) {
                    $s_class.prop('disabled', false);
                    $.each(data, (i, d) => $s_class.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            $s_class.on('change', function () {
                const id = $(this).val();
                resetDropdowns($s_course.add($s_section).add($s_slot));
                if (!id) return;
                $.get(`/ajax-courses?program_class_id=${id}`, function (data) {
                    $s_course.prop('disabled', false);
                    $.each(data, (i, d) => $s_course.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            $s_course.on('change', function () {
                const id = $(this).val();
                resetDropdowns($s_section.add($s_slot));
                if (!id) return;
                $.get(`/ajax-course-sections?course_id=${id}`, function (data) {
                    $s_section.prop('disabled', false);
                    $.each(data, (i, d) => $s_section.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            $s_section.on('change', function () {
                const sectionId = $(this).val();
                const classId = $s_class.val();
                resetDropdowns($s_slot);
                if (!sectionId || !classId) return;
                $.get(`/ajax-timetables?course_section_id=${sectionId}&program_class_id=${classId}`, function (data) {
                    $s_slot.prop('disabled', false);
                    $.each(data, (i, d) => $s_slot.append(`<option value="${d.id}">${d.text}</option>`));
                });
            });

            $('#close-daily-attendance').on('click', function () {
                $('#daily .card:has(.table-responsive)').remove();
                $('#daily .alert-warning').remove();
            });

            // ======================= Daily Tab Scoped Dropdowns =======================
            const $dailyPane = $('#daily');
            const $d_session = $dailyPane.find('.academic-session');
            const $d_level = $dailyPane.find('.study-level');
            const $d_program = $dailyPane.find('.program');
            const $d_class = $dailyPane.find('.program-class');

            resetDropdowns($d_level.add($d_program).add($d_class));

            $d_session.on('change', function () {
                const id = $(this).val();
                resetDropdowns($d_level.add($d_program).add($d_class));
                if (!id) return;
                $.get(`/ajax-study-levels?academic_session_id=${id}`, function (data) {
                    $d_level.prop('disabled', false);
                    $.each(data, (i, d) => $d_level.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            $d_level.on('change', function () {
                const id = $(this).val();
                resetDropdowns($d_program.add($d_class));
                if (!id) return;
                $.get(`/ajax-programs?study_level_id=${id}`, function (data) {
                    $d_program.prop('disabled', false);
                    $.each(data, (i, d) => $d_program.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            $d_program.on('change', function () {
                const id = $(this).val();
                resetDropdowns($d_class);
                if (!id) return;
                $.get(`/program-classes?program_id=${id}`, function (data) {
                    $d_class.prop('disabled', false);
                    $.each(data, (i, d) => $d_class.append(`<option value="${d.id}">${d.name}</option>`));
                });
            });

            // ======================= Auto-trigger on Tab Change =======================
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                if ($(e.target).attr('id') === 'subject-tab' && $s_class.val()) {
                    $s_class.trigger('change');
                } else if ($(e.target).attr('id') === 'daily-tab' && $d_program.val()) {
                    $d_program.trigger('change');
                }
            });

            $('#close-subject-attendance')?.on('click', () => {
                $('#subject-attendance-card')?.remove();
            });
        });
    </script>
@endsection

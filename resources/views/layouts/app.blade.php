<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title')</title>
    <!-- Favicons -->
    <link href="{{url('backend/img/favicon.ico')}}" rel="icon">
    <link href="{{url('backend/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->


    <link href="{{url('backend/css/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{url('backend/css/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">

    <link href="{{url('backend/css/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
    <link href="{{url('backend/css/quill/quill.snow.css')}}" rel="stylesheet">
    <link href="{{url('backend/css/quill/quill.bubble.css')}}" rel="stylesheet">
    <link href="{{url('backend/css/remixicon/remixicon.css')}}" rel="stylesheet">
    <link href="{{url('backend/css/simple-datatables/style.css')}}" rel="stylesheet">
    <link href="{{url('backend/js/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{url('backend/js/toastr/toastr.min.css')}}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="{{url('backend/js/datatable/datatables.min.css')}}" rel="stylesheet">




    <!-- Template Main CSS File -->
    <link href="{{url('backend/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include CSS and other head content here -->
</head>
<body class="r">

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{route('dashboard')}}" class="logo d-flex align-items-center">
            {{--            <img src="{{url('backend/img/logo.png')}}" alt="">--}}
            <span class="d-none d-lg-block">Education ERP</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">




            <li class="nav-item dropdown pe-3">

                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ ucwords (Auth::user()->name)}}</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ Auth::user()->name }}</h6>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @if(session()->has('impersonated_by'))
                        <li>
                            <a href="{{ route('users.impersonate.leave') }}"
                               class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-box-arrow-left text-danger"></i>
                                <span>Exit Impersonation</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    @endif


                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{route('profile.edit')}}">
                            <i class="bi bi-gear"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center border-0 bg-transparent">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>

                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2 text-primary"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Administrator Settings -->
        @canany(['view_role', 'edit_role', 'update_role', 'create_role', 'assign_permission','view_institute_settings', 'view_email_settings', 'view_whatsapp_settings'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#admin-settings-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-shield-lock text-danger"></i>
                    <span>Administrator Settings</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="admin-settings-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">

                    @canany(['view_role','edit_role','update_role','create_role','assign_permission'])
                        <li>
                            <a href="{{ route('roles.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Roles Management</span>
                            </a>
                        </li>
                    @endcanany

                        @canany(['view_users'])
                            <li>
                                <a href="{{ route('users.index') }}">
                                    <i class="bi bi-circle text-secondary"></i><span>Users Management</span>
                                </a>
                            </li>
                        @endcanany

                    @can('view_institute_settings')
                        <li>
                            <a href="{{ route('settings.institute') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Institute Settings</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_email_settings')
                        <li>
                            <a href="{{ route('settings.email') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Email Settings</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_whatsapp_settings')
                        <li>
                            <a href="{{ route('settings.whatsapp') }}">
                                <i class="bi bi-circle text-secondary"></i><span>WhatsApp Settings</span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcanany

        <!-- Academic Management -->
        @canany(['create_academic_session', 'view_academic_session', 'update_academic_session', 'delete_academic_session'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#academic-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-journal-bookmark-fill text-success"></i>
                    <span>Academic Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="academic-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    @can('create_academic_session')
                        <li>
                            <a href="{{ route('academic-session.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Academic Session</span>
                            </a>
                        </li>
                    @endcan
                    @can('create_academic_session')
                        <li>
                            <a href="{{ route('academic-session.transfer.form') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Transfer Session</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Assignment Management -->
        @canany(['view_assignment', 'create_assignment', 'update_assignment', 'delete_assignment'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#assignment-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-clipboard2-check-fill text-info"></i>
                    <span>Assignment Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="assignment-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    @can('view_assignment')
                        <li>
                            <a href="{{ route('assignments.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Assignment List</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Employee Management -->
        @canany(['view_employee', 'create_employee', 'update_employee', 'delete_employee'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#employee-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-people-fill text-primary"></i>
                    <span>Employee Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="employee-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('employees.index') }}">
                            <i class="bi bi-circle text-secondary"></i><span>Employee List</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany

        <!-- Teacher Management -->
        @canany(['view_teacher', 'create_teacher', 'update_teacher', 'delete_teacher'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#teacher-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-person-video3 text-warning"></i>
                    <span>Teacher Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="teacher-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('teachers.index') }}">
                            <i class="bi bi-circle text-secondary"></i><span>Teacher List</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany

        <!-- Student Management -->
        @canany(['view_student', 'create_student', 'update_student', 'delete_student'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-mortarboard-fill text-info"></i>
                    <span>Student Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="student-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('students.index') }}">
                            <i class="bi bi-circle text-secondary"></i><span>Student List</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany

        <!-- TimeTable Management -->
        @canany(['view_working_days','update_working_days','view_time_slots','update_time_slots','create_time_slot','delete_time_slot','view_time_table','create_time_table','update_time_table','delete_time_table'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#timetable-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-calendar-week-fill text-primary"></i>
                    <span>Timetable Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul id="timetable-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <!-- Working Days Section -->
                    @canany(['view_working_days','update_working_days'])
                        <li>
                            <a href="{{ route('working-days.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Working Days</span>
                            </a>
                        </li>
                    @endcanany

                    <!-- Time Slots Section -->
                    @canany(['view_time_slots','update_time_slots','create_time_slot','delete_time_slot'])
                        <li>
                            <a href="{{ route('time-slots.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Time Slots</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['view_room'])
                        <li>
                            <a href="{{ route('rooms.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Rooms</span>
                            </a>
                        </li>
                    @endcanany

                    <!-- Timetable Operations -->
                    @can('view_time_table')
                        <li>
                            <a href="{{ route('timetable.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>View Timetable</span>
                            </a>
                        </li>
                    @endcan

                    @can('create_time_table')
                        <li>
                            <a href="{{ route('timetable.create') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Create Timetable</span>
                            </a>
                        </li>
                    @endcan

                    @can('update_time_table')
                        <li>
                            <a href="{{ route('timetable.edit')}}">
                                <i class="bi bi-circle text-secondary"></i><span>Edit Timetable</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Attendance Management -->
        @canany(['view_attendance', 'create_attendance', 'update_attendance', 'delete_attendance', 'take_attendance'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#attendance-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-clipboard2-pulse-fill text-success"></i>
                    <span>Attendance Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul id="attendance-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <!-- Take Attendance -->
                    @can('take_attendance')
                        <li>
                            <a href="#">
                                <i class="bi bi-circle text-secondary"></i><span>Take Attendance</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Create Attendance -->
                    @can('create_attendance')
                        <li>
                            <a href="{{ route('attendance.create') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Create Attendance Sheet</span>
                            </a>
                        </li>
                    @endcan

                    <!-- View Attendance -->
                    @can('view_attendance')
                        <li>
                            <a href="{{ route('attendance.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>View Attendance</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Examination Management -->
        @canany(['view_examination_term',  'view_examination_session' , 'view_date_sheet','view_examination_marks'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#examination-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-journal-text text-warning"></i>
                    <span style="font-size: 0.85rem;">Examination Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul id="examination-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">

                    @can('view_examination_session')
                        <li>
                            <a href="{{ route('examination-session.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Examination Sessions</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Examination Terms -->
                    @can('view_examination_term')
                        <li>
                            <a href="{{ route('examination-term.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Examination Terms</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_date_sheet')
                        <li>
                            <a href="{{ route('examination-date-sheet.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Examination Date Sheet</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_examination_marks')
                        <li>
                            <a href="{{ route('examination-marks.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Examination Marks</span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcanany

        <!-- Fee Management -->
        @canany(['view_fee_group',  'view_fee_type' , 'view_fee'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#fee-management-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-cash-stack text-success"></i>
                    <span style="font-size: 0.85rem;">Fee Management</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul id="fee-management-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    @can('view_fee_group')
                        <li>
                            <a href="{{ route('fee-group.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Fee Groups</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_fee_type')
                        <li>
                            <a href="{{ route('fee-type.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Fee Types</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_fee')
                        <li>
                            <a href="{{ route('fee.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Fee Setup</span>
                            </a>
                        </li>
                    @endcan
                        @can('view_fee_template')
                            <li>
                                <a href="{{ route('fee-templates.index') }}">
                                    <i class="bi bi-circle text-secondary"></i><span>Fee Template</span>
                                </a>
                            </li>
                        @endcan
                </ul>
            </li>
        @endcanany

        <!-- Front Office Management -->
        @canany(['view_visitor_log', 'view_postal_log'])
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#front-office-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-building text-primary"></i>
                    <span style="font-size: 0.85rem;">Front Office</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul id="front-office-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    @can('view_visitor_log')
                        <li>
                            <a href="{{ route('visitor-logs.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Visitor Logs</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_post_log')
                        <li>
                            <a href="{{ route('postals.index') }}">
                                <i class="bi bi-circle text-secondary"></i><span>Postal Logs</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

    </ul>
</aside>

@yield('content')

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">

    </div>
    <div class="credits">
        Designed by <a href="#">KloudTech</a>
    </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
{{--<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>--}}
<script src="{{url('backend/js/jquery-3.5.1.min.js')}}"></script>
<script src="{{url('backend/js/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{url('backend/css/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{url('backend/js/chart.js/chart.umd.js')}}"></script>
<script src="{{url('backend/js/echartsnew/echarts.min.js')}}"></script>
<script src="{{url('backend/css/quill/quill.min.js')}}"></script>
<script src="{{url('backend/css/tinymce/tinymce.min.js')}}"></script>
<script src="{{url('backend/js/php-email-form/validate.js')}}"></script>
<script src="{{url('backend/js/select2/js/select2.min.js')}}"></script>
<script src="{{url('backend/js/toastr/toastr.min.js')}}"></script>
<script src="{{url('backend/js/datatable/datatables.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>




<!-- Template Main JS File -->
<script src="{{url('backend/js/main.js')}}"></script>
<script>
    // Check for Toastr notifications
    @if(session('toastr'))
    var toastrOptions = @json(session('toastr')['options']);
    toastr.{{ session('toastr')['type'] }}('{{ session('toastr')['message'] }}', '{{ session('toastr')['title'] }}', toastrOptions);
    @endif
    $(document).ready(function() {
        // Initialize all modals
        $('[data-toggle="modal"]').modal();
    });
</script>
@yield('scripts')
</body>
</html>

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

    <!-- Template Main CSS File -->
    <link href="{{url('backend/css/style.css')}}" rel="stylesheet">

    <!-- Include CSS and other head content here -->
</head>
<body class="toggle-sidebar">

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{route('login')}}" class="logo d-flex align-items-center">
            {{--            <img src="{{url('backend/img/logo.png')}}" alt="">--}}
            <span class="d-none d-lg-block">Education ERP</span>
        </a>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->


@yield('content');

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">

    </div>
    <div class="credits">

        Designed by <a href="#">KloudTech</a>
    </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="{{url('backend/css/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

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

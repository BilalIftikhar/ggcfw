<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head') {{-- Inject head partial --}}
</head>
<body>

<!-- Preloader -->
<div class="preloader">
    <div class="loader"></div>
</div>

<!-- Sidebar Overlay -->
<div class="side-overlay"></div>

<!-- Sidebar -->
@include('layouts.partials.sidebar') {{-- Sidebar partial --}}

<!-- Main Wrapper -->
<div class="dashboard-main-wrapper">

    <!-- Top Navbar/Header -->
    @include('layouts.partials.header') {{-- Top navbar partial --}}

    <!-- Main Content -->
    <div class="dashboard-body">
        @yield('content') {{-- Page-specific content goes here --}}
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer') {{-- Footer partial --}}
</div>

{{-- Scripts --}}
@include('layouts.partials.scripts') {{-- Inject script partial --}}

</body>
</html>

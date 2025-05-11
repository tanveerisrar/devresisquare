<!DOCTYPE html>
<html>

<head>
    <title>Backend Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ getBaseURL() }}">
	<meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Use asset() to generate the correct URL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('asset/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('asset/css/toastr.min.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('asset/backend/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tagify CSS -->    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
    <link href="{{ asset('asset/css/carousel.css') }}" rel="stylesheet">

    {{-- <link href="{{ asset('asset/backend/css/aiz-core.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('asset/backend/css/media.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('asset/backend/css/vendors.css') }}" rel="stylesheet"> --}}
</head>
<body class="frontend_body">

    <x-frontend.header/>
    <div class="mt-4">
        @yield('content')
    </div>    <x-frontend.footer/>
    <script src="{{ asset('asset/js/jquery.min.js') }}"></script>
    <script src="{{ asset('asset/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('asset/js/carousel.js') }}"></script>
    @yield('page.scripts') 


</body>
</html>

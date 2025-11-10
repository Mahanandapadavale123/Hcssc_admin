<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords" content="admin, estimates, bootstrap, business, html5, responsive, Projects">
    <meta name="author" content="Dreams technologies - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>HCSSC - Admin Panel</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('admin/img/apple-touch-icon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/tabler-icons/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>

<body class="bg-white">
    <div class="main-wrapper">

        @yield('content')

    </div>

    <script src="{{ asset('admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/js/validation.js') }}"></script>
    <script src="{{ asset('admin/js/otp.js') }}"></script>
    <script src="{{ asset('admin/js/script.js') }}"></script>

</body>

</html>

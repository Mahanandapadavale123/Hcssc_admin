<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Handicrafts and Carpet Sector Skill Council &#8211; (HCSSC)">
    <meta name="keywords" content="Handicrafts and Carpet Sector Skill Council &#8211; (HCSSC)">
    <meta name="author" content="HCSSC : Handicrafts and Carpet Sector Skill Council">
    <meta name="robots" content="noindex, nofollow">
    <title>Handicrafts and Carpet Sector Skill Council &#8211; (HCSSC)</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/logo-icon.svg') }}">

    <script src="{{ asset('admin/js/theme-script.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.35.0/tabler-icons.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('admin/plugins/ion-rangeslider/css/ion.rangeSlider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/summernote/summernote-lite.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin/plugins/icons/flags/flags.css') }}">
    {{-- Datatable --}}
    <link rel="stylesheet" href="{{ asset('admin/css/dataTables.bootstrap5.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">


    @stack('styles')

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>

<body>
    <div class="main-wrapper">

        @include('layouts.partials.header')

        @include('layouts.partials.sidebar')

        <div class="page-wrapper">
            <div class="content">

                @yield('content')


            </div>
            <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
                <p class="mb-0">{{ date('Y') }} &copy; {{ env('APP_NAME') }}.</p>
                <p>Designed &amp; Developed By <a href="javascript:void(0);" class="text-primary">Digitalnock</a></p>
            </div>
        </div>

        @component('components.modal-popup')
        @endcomponent

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
    <script src="{{ asset('admin/plugins/summernote/summernote-lite.min.js') }}"></script>

    <!-- Datatable JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Daterangepikcer JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('admin/js/script.js') }}"></script>
    <script>

        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "closeButton": true,
            "progressBar": true
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>


    @stack('scripts')

</body>

</html>

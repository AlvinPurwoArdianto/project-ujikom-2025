<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('user/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('user/assets/img/favicon.png') }}">
    <title>
        Argon Dashboard 2 by Creative Tim
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('user/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('user/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js')}}" crossorigin="anonymous"></script>
    <link href="{{ asset('user/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('user/assets/css/argon-dashboard.css?v=2.0.4') }}" rel="stylesheet" />

    {{-- calender --}}
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .bg-primary {
            background-color: #0d6efd !important;
        }

        .text-white {
            color: #ffffff !important;
        }
    </style>


</head>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>

    {{-- awal sidebar --}}
    @include('include.user.sidebar')
    {{-- akhir sidebar --}}


    <main class="main-content position-relative border-radius-lg ">
        <!-- Navbar -->
        @include('include.user.navbar')

        @yield('content')

        {{-- end calender --}}

        {{-- Footer --}}
        @include('include.user.footer')
        {{-- / Footer --}}
        {{-- </div> --}}
    </main>

    <!--   Core JS Files   -->
    <script src="{{ asset('user/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('user/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('user/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('user/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('user/assets/js/plugins/chartjs.min.js') }}"></script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('user/assets/js/argon-dashboard.min.js?v=2.0.4') }}"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
            });
        </script>
    @endif
</body>

</html>

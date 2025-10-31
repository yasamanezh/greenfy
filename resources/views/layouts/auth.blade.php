<!DOCTYPE html>
<html class="light-style layout-wide customizer-hide" data-assets-path="{{ asset('assets/') }}"
    data-template="vertical-menu-template-no-customizer" data-theme="theme-default" dir="rtl" lang="fa">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
        name="viewport" />
    <title>@yield('title', 'احراز هویت') | {{ config('app.name') }}</title>
    <meta content="" name="description" />
    <!-- Favicon -->
    <link href="{{ asset('assets/img/favicon/favicon.ico') }}" rel="icon" type="image/x-icon" />
    <!-- Icons -->
    <link href="{{ asset('assets/assets/vendor/fonts/fontawesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/vendor/fonts/tabler-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/vendor/fonts/flag-icons.css') }}" rel="stylesheet" />
    <!-- Core CSS -->
    <link href="{{ asset('assets/assets/vendor/css/rtl/core.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/vendor/css/rtl/theme-default.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/css/demo.css') }}" rel="stylesheet" />
    <!-- Vendors CSS -->
    <link href="{{ asset('assets/assets/vendor/libs/node-waves/node-waves.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/vendor/libs/typeahead-js/typeahead.css') }}" rel="stylesheet" />
    <!-- Vendor -->

    <link href="{{ asset('assets/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />

    <!-- Page CSS -->
    <link href="{{ asset('assets/assets/vendor/css/pages/page-auth.css') }}" rel="stylesheet" />
    <!-- Helpers -->
    <script src="{{ asset('assets/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/assets/js/config.js') }}"></script>
    <!-- Better experience of RTL -->
    <link href="{{ asset('assets/assets/css/rtl.css') }}" rel="stylesheet" />
    @stack('styles')
    @livewireStyles
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4" style="max-width: 500px;">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                            {{ $slot }}
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
 

    <!-- Core JS -->
    <script src="{{ asset('assets/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/js/menu.js') }}"></script>


    <!-- Vendors JS -->
    <script src="{{ asset('assets/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/libs/select2/i18n/fa.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/assets/js/main.js') }}"></script>
    <script>
        function togglePassword(targetId, el) {
            const input = document.getElementById(targetId);
            if (!input) {
                return;
            }
            const icon = el.querySelector('i');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            if (icon) {
                icon.classList.toggle('ti-eye-off', !isPassword);
                icon.classList.toggle('ti-eye', isPassword);
            }

            input.focus();
        }
    </script>

    @livewireScripts
</body>

</html>

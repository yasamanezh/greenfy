<!DOCTYPE html>
<html class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-starter" data-theme="theme-default" dir="rtl" lang="fa">

<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" name="viewport"/>
    <title>@yield('title', 'پنل مدیریت') - {{ config('app.name', 'Laravel') }}</title>
    <meta content="" name="description"/>
    
    <!-- Favicon -->
    <link href="{{ asset('assets/img/favicon/favicon.ico') }}" rel="icon" type="image/x-icon"/>
    
    <!-- Fonts -->
    <link href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" rel="stylesheet"/>
    
    <!-- Core CSS -->
    <link class="template-customizer-core-css" href="{{ asset('assets/vendor/css/rtl/core.css') }}" rel="stylesheet"/>
    <link class="template-customizer-theme-css" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/demo.css') }}" rel="stylesheet"/>
    
    <!-- Vendors CSS -->
    <link href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet"/>
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Page CSS -->
    @stack('styles')
    
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    
    <!-- Template customizer & Theme config -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    
    <!-- Better experience of RTL -->
    <link href="{{ asset('assets/css/rtl.css') }}" rel="stylesheet"/>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            
            <!-- Sidebar Menu -->
            <aside class="layout-menu menu-vertical menu bg-menu-theme" id="layout-menu">
                <div class="app-brand demo">
                    <a class="app-brand-link" href="{{ route('dashboard') }}">
                        <span class="app-brand-logo demo">
                            <svg fill="none" height="22" viewBox="0 0 32 22" width="32" xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0" fill-rule="evenodd"/>
                                <path clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" fill-rule="evenodd" opacity="0.06"/>
                                <path clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" fill-rule="evenodd" opacity="0.06"/>
                                <path clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0" fill-rule="evenodd"/>
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    <a class="layout-menu-toggle menu-link text-large ms-auto" href="javascript:void(0);">
                        <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
                        <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
                    </a>
                </div>
                
                <div class="menu-inner-shadow"></div>
                
                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('dashboard') }}">
                            <i class="menu-icon tf-icons ti ti-smart-home"></i>
                            <div>داشبورد</div>
                        </a>
                    </li>

                    <!-- Websites -->
                    <li class="menu-item {{ request()->routeIs('websites.*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('websites.index') }}">
                            <i class="menu-icon tf-icons ti ti-world"></i>
                            <div>وبسایت‌ها</div>
                        </a>
                    </li>

                    <!-- Plans -->
                    <li class="menu-item {{ request()->routeIs('plans.*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('plans.index') }}">
                            <i class="menu-icon tf-icons ti ti-crown"></i>
                            <div>پلن‌ها</div>
                        </a>
                    </li>

                    <!-- Modules -->
                    <li class="menu-item {{ request()->routeIs('modules.*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('modules.index') }}">
                            <i class="menu-icon tf-icons ti ti-package"></i>
                            <div>ماژول‌ها</div>
                        </a>
                    </li>

                    <!-- SMS -->
                    <li class="menu-item {{ request()->routeIs('sms.*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('sms.packages') }}">
                            <i class="menu-icon tf-icons ti ti-message"></i>
                            <div>پیامک</div>
                        </a>
                    </li>

                    <!-- Wallet -->
                    <li class="menu-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('wallet.show') }}">
                            <i class="menu-icon tf-icons ti ti-wallet"></i>
                            <div>کیف پول</div>
                        </a>
                    </li>

                    <!-- Transactions -->
                    <li class="menu-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('transactions.index') }}">
                            <i class="menu-icon tf-icons ti ti-receipt"></i>
                            <div>تراکنش‌ها</div>
                        </a>
                    </li>

                    <!-- Settings -->
                    @can('view-settings')
                    <li class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('settings.index') }}">
                            <i class="menu-icon tf-icons ti ti-settings"></i>
                            <div>تنظیمات</div>
                        </a>
                    </li>
                    @endcan
                </ul>
            </aside>
            <!-- / Sidebar Menu -->
            
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-sm"></i>
                        </a>
                    </div>
                    
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Theme Switcher -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                                <a class="nav-link dropdown-toggle hide-arrow" data-bs-toggle="dropdown" href="javascript:void(0);">
                                    <i class="ti ti-md"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-start dropdown-styles">
                                    <li>
                                        <a class="dropdown-item" data-theme="light" href="javascript:void(0);">
                                            <span class="align-middle">
                                                <i class="ti ti-sun me-2"></i>
                                                روز
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" data-theme="dark" href="javascript:void(0);">
                                            <span class="align-middle">
                                                <i class="ti ti-moon me-2"></i>
                                                شب
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" data-theme="system" href="javascript:void(0);">
                                            <span class="align-middle">
                                                <i class="ti ti-device-desktop me-2"></i>
                                                سیستم
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" data-bs-toggle="dropdown" href="javascript:void(0);">
                                    <div class="avatar avatar-online">
                                        <img alt class="h-auto rounded-circle" src="{{ auth()->user()->avatar_url ?? asset('assets/img/avatars/1.png') }}"/>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img alt class="h-auto rounded-circle" src="{{ auth()->user()->avatar_url ?? asset('assets/img/avatars/1.png') }}"/>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block mb-1">{{ auth()->user()->name }}</span>
                                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                                            <i class="ti ti-user-check me-2 ti-sm"></i>
                                            <span class="align-middle">پروفایل من</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('settings.profile') }}">
                                            <i class="ti ti-settings me-2 ti-sm"></i>
                                            <span class="align-middle">تنظیمات</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('transactions.index') }}">
                                            <span class="d-flex align-items-center align-middle">
                                                <i class="flex-shrink-0 ti ti-credit-card me-2 ti-sm"></i>
                                                <span class="flex-grow-1 align-middle">خریدها</span>
                                                <span class="flex-shrink-0 badge badge-center rounded-pill bg-label-danger w-px-20 h-px-20">
                                                    {{ auth()->user()->pending_transactions_count ?? 0 }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="dropdown-item" href="{{ route('logout') }}" 
                                               onclick="event.preventDefault(); this.closest('form').submit();">
                                                <i class="ti ti-logout me-2 ti-sm"></i>
                                                <span class="align-middle">خروج از حساب</span>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        
                        <!-- Page Header -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h4 class="fw-bold py-3 mb-2">
                                            @yield('page-title', 'داشبورد')
                                        </h4>
                                        <p class="mb-0 text-muted">
                                            @yield('page-description', 'به پنل مدیریت خوش آمدید')
                                        </p>
                                    </div>
                                    <div>
                                        @yield('page-actions')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flash Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Main Content -->
                        {{ $slot }}

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                                <div>
                                    © <script>document.write(new Date().getFullYear());</script>
                                    , ارائه شده توسط
                                    <span class="text-danger byte-hover">بایت‌مَستر</span>
                                    در سایت
                                    <a class="fw-medium" href="#support" target="_blank">راستچین</a>
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    <a class="footer-link me-4" href="#" target="_blank">مستندات</a>
                                    <span class="text-muted">ورژن: {{ config('app.version', '1.0.0') }}</span>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                    
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
        
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page Scripts -->
    @stack('scripts')

    <!-- Livewire Loader -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Livewire initialization
        });

        document.addEventListener('livewire:load', () => {
            // Livewire loaded
        });
    </script>
</body>

</html>
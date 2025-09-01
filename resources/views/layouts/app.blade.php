<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @php
        // Map route names to page titles
        $titles = [
        'dashboard' => 'Dashboard - Vibrant Engineering',
        'profile.edit' => 'Edit Profile - Vibrant Engineering',
        'products.index' => 'Products - Vibrant Engineering',
        'categories.index' => 'Categories - Vibrant Engineering',
        'suppliers.index' => 'Suppliers - Vibrant Engineering',
        'customers.index' => 'Customers - Vibrant Engineering',
        'purchases.index' => 'Purchases - Vibrant Engineering',
        'sales.index' => 'Sales - Vibrant Engineering',
        'expenses.index' => 'Expenses - Vibrant Engineering',
        'reports.index' => 'Reports - Vibrant Engineering',
        'analytics.index' => 'Analytics - Vibrant Engineering',
        // Add more routes as needed
        ];

        $currentRoute = Route::currentRouteName();
        $title = $titles[$currentRoute] ?? 'Vibrant Engineering Inventory';
        @endphp

        {{ $title }}
    </title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

    <!-- Toast Notification CSS -->
    <style>
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            /* green for success */
            color: #fff;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .toast-notification.error {
            background-color: #dc3545;
            /* red for error */
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast-notification .close-toast {
            margin-left: 15px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Page Sidebar -->
        @include('layouts.sidebar')

        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <span>Admin</span>
                                    <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="" width="35"
                                        height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="{{ route('profile.edit') }}"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="#" class="btn btn-outline-primary mx-3 mt-2 d-block"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    @if(session('cache-clear-success'))
    <div id="toast-success" class="toast-notification">
        {{ session('cache-clear-success') }}
        <span class="close-toast" onclick="this.parentElement.style.display='none';">&times;</span>
    </div>
    @endif
    @if(session('cache-clear-error'))
    <div id="toast-error" class="toast-notification error">
        {{ session('cache-clear-error') }}
        <span class="close-toast" onclick="this.parentElement.style.display='none';">&times;</span>
    </div>
    @endif

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    <!-- Toast Notification JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toast = document.getElementById('toast-success') || document.getElementById('toast-error');
            if (toast) {
                toast.classList.add('show');
                setTimeout(() => {
                    if (toast) toast.classList.remove('show');
                }, 4000); // hide after 4 seconds
            }
        });
    </script>
</body>

</html>
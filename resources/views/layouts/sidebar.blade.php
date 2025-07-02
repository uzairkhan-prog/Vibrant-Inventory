<!-- Sidebar Start -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" style="width: 200px;">
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-6"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">

                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Dashboard</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('dashboard') }}">
                        <i class="ti ti-home"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Inventory</span>
                </li>

                <!-- <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('analytics.index') }}">
                        <i class="ti ti-chart-bar"></i>
                        <span class="hide-menu">Analytics</span>
                    </a>
                </li> -->

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('products.index') }}">
                        <i class="ti ti-package"></i>
                        <span class="hide-menu">Products</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('categories.index') }}">
                        <i class="ti ti-category"></i>
                        <span class="hide-menu">Categories</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('suppliers.index') }}">
                        <i class="ti ti-truck"></i>
                        <span class="hide-menu">Suppliers</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('customers.index') }}">
                        <i class="ti ti-users"></i>
                        <span class="hide-menu">Customers</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('purchases.index') }}">
                        <i class="ti ti-shopping-cart"></i>
                        <span class="hide-menu">Purchases</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('sales.index') }}">
                        <i class="ti ti-currency-dollar"></i>
                        <span class="hide-menu">Sales</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('sale-returns.index') }}">
                        <i class="ti ti-arrow-back-up"></i>
                        <span class="hide-menu">Sale Returns</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('expenses.index') }}">
                        <i class="ti ti-receipt"></i>
                        <span class="hide-menu">Expenses</span>
                    </a>
                </li>

                <!-- Product Ledger -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('ledger.products') }}">
                        <i class="ti ti-clipboard"></i>
                        <span class="hide-menu">Product Ledger</span>
                    </a>
                </li>

                <!-- General Ledger -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('ledgers.index') }}">
                        <i class="ti ti-book"></i>
                        <span class="hide-menu">Ledger</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('assets-inventory.index') }}">
                        <i class="ti ti-building-bank"></i>
                        <span class="hide-menu">Assets</span>
                    </a>
                </li>

                <!-- Reports -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('reports.index') }}">
                        <i class="ti ti-report"></i>
                        <span class="hide-menu">Reports</span>
                    </a>
                </li>

                <!-- Settings -->
                <li><span class="sidebar-divider lg"></span></li>

                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)">
                        <i class="ti ti-settings"></i>
                        <span class="hide-menu">Settings</span>
                    </a>
                    <ul class="collapse first-level">
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('profile.edit') }}">
                                <i class="ti ti-user"></i>
                                <span class="hide-menu">Profile Edit</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="#" onclick="return confirm('Are you sure you want to clear the cache?');">
                                <i class="ti ti-refresh"></i>
                                <span class="hide-menu">Clear Cache</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti ti-logout"></i>
                                <span class="hide-menu">Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>

                <li><span class="sidebar-divider lg"></span></li>

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!-- Sidebar End -->
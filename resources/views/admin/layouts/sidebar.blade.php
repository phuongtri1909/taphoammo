@extends('admin.layouts.app')

@section('content')
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <img src="{{ $logoPath }}" alt="logo" height="48">
                <button id="close-sidebar" class="close-sidebar d-md-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li class="{{ Route::currentRouteNamed('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <div class="icon-gradient-mask" style="--img: url('{{ asset('images/svg/admin/dashboard.svg') }}');"></div>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Quản lý danh mục sản phẩm -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.categories.*', 'admin.subcategories.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-folder-open"></i>
                            <span>DM sản phẩm</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed('admin.categories.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.categories.index') }}">
                                    <i class="fas fa-folder"></i>
                                    <span>Danh mục</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.subcategories.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.subcategories.index') }}">
                                    <i class="fas fa-folder-tree"></i>
                                    <span>Danh mục con</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Quản lý sản phẩm -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.products.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-box"></i>
                            <span>Quản lý sản phẩm</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed(['admin.products.pending', 'admin.products.review']) ? 'active' : '' }}">
                                <a href="{{ route('admin.products.pending') }}">
                                    <i class="fas fa-clock"></i>
                                    <span>Chờ duyệt</span>
                                    @if(isset($pendingProductsCount) && $pendingProductsCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingProductsCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.products.index', 'admin.products.show']) ? 'active' : '' }}">
                                <a href="{{ route('admin.products.index') }}">
                                    <i class="fas fa-list"></i>
                                    <span>Tất cả sản phẩm</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Quản lý danh mục dịch vụ -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.service-categories.*', 'admin.service-subcategories.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-concierge-bell"></i>
                            <span>DM dịch vụ</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed('admin.service-categories.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.service-categories.index') }}">
                                    <i class="fas fa-folder"></i>
                                    <span>Danh mục</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.service-subcategories.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.service-subcategories.index') }}">
                                    <i class="fas fa-folder-tree"></i>
                                    <span>Danh mục con</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <!-- Quản lý dịch vụ -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.services.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-concierge-bell"></i>
                            <span>Quản lý dịch vụ</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed(['admin.services.pending', 'admin.services.review']) ? 'active' : '' }}">
                                <a href="{{ route('admin.services.pending') }}">
                                    <i class="fas fa-clock"></i>
                                    <span>Chờ duyệt</span>
                                    @if(isset($pendingServicesCount) && $pendingServicesCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingServicesCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.services.index', 'admin.services.show']) ? 'active' : '' }}">
                                <a href="{{ route('admin.services.index') }}">
                                    <i class="fas fa-list"></i>
                                    <span>Tất cả dịch vụ</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Quản lý người bán -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.sellers.*', 'admin.seller-registrations.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-store"></i>
                            <span>Quản lý người bán</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed(['admin.seller-registrations.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.seller-registrations.index') }}">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Đăng ký chờ duyệt</span>
                                    @if(isset($pendingSellerRegistrationsCount) && $pendingSellerRegistrationsCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingSellerRegistrationsCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.sellers.index', 'admin.sellers.show']) ? 'active' : '' }}">
                                <a href="{{ route('admin.sellers.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span>Danh sách người bán</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Quản lý đơn hàng -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.orders.*', 'admin.service-orders.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <div class="icon-gradient-mask" style="--img: url('{{ asset('images/svg/admin/cart.svg') }}');"></div>
                            <span>Đơn hàng</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed(['admin.orders.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.index') }}">
                                    <i class="fas fa-box"></i>
                                    <span>Đơn hàng sản phẩm</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.service-orders.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.service-orders.index') }}">
                                    <i class="fas fa-concierge-bell"></i>
                                    <span>Đơn hàng dịch vụ</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Quản lý hoàn tiền & tranh chấp -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.refunds.*', 'admin.disputes.*', 'admin.service-disputes.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Hoàn & Tranh chấp</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed(['admin.disputes.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.disputes.index') }}">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Tranh chấp sản phẩm</span>
                                    @if(isset($reviewingDisputesCount) && $reviewingDisputesCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $reviewingDisputesCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.service-disputes.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.service-disputes.index') }}">
                                    <i class="fas fa-concierge-bell"></i>
                                    <span>Tranh chấp dịch vụ</span>
                                    @if(isset($reviewingServiceDisputesCount) && $reviewingServiceDisputesCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $reviewingServiceDisputesCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.refunds.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.refunds.index') }}">
                                    <i class="fas fa-undo"></i>
                                    <span>Hoàn tiền</span>
                                    @if(isset($pendingRefundsCount) && $pendingRefundsCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingRefundsCount }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Quản lý đấu giá -->
                    <li class="{{ Route::currentRouteNamed(['admin.auctions.*']) ? 'active' : '' }}">
                        <a href="{{ route('admin.auctions.index') }}">
                            <i class="fas fa-gavel"></i>
                            <span>Quản lý đấu giá</span>
                        </a>
                    </li>

                    <!-- Quản lý người dùng -->
                    <li class="{{ Route::currentRouteNamed(['admin.users.*']) ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Quản lý người dùng</span>
                        </a>
                    </li>

                    <!-- Quản lý tài chính -->
                    <li class="has-submenu {{ Route::currentRouteNamed(['admin.deposits.*', 'admin.withdrawals.*', 'admin.banks.*', 'admin.manual-wallet-adjustments.*', 'admin.featured-histories.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fa-solid fa-money-bill-trend-up"></i>
                            <span>Tài chính</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed(['admin.deposits.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.deposits.index') }}">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Nạp tiền</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.withdrawals.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.withdrawals.index') }}">
                                    <i class="fas fa-money-check-alt"></i>
                                    <span>Yêu cầu rút tiền</span>
                                    @if(isset($pendingWithdrawalsCount) && $pendingWithdrawalsCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingWithdrawalsCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.banks.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.banks.index') }}">
                                    <i class="fas fa-university"></i>
                                    <span>Ngân hàng</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.manual-wallet-adjustments.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.manual-wallet-adjustments.index') }}">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <span>Điều chỉnh ví thủ công</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.featured-histories.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.featured-histories.index') }}">
                                    <i class="fas fa-star"></i>
                                    <span>Lịch sử đề xuất</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Tin nhắn liên hệ -->
                    <li class="{{ Route::currentRouteNamed(['admin.contact-submissions.*']) ? 'active' : '' }}">
                        <a href="{{ route('admin.contact-submissions.index') }}">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Tin nhắn liên hệ</span>
                            @if(isset($unreadContactSubmissionsCount) && $unreadContactSubmissionsCount > 0)
                                <span class="badge bg-warning text-dark ms-auto">{{ $unreadContactSubmissionsCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- FAQ -->
                    <li class="{{ Route::currentRouteNamed(['admin.faqs.*']) ? 'active' : '' }}">
                        <a href="{{ route('admin.faqs.index') }}">
                            <i class="fas fa-question-circle"></i>
                            <span>Quản lý FAQ</span>
                        </a>
                    </li>

                    <!-- Quản lý bài chia sẻ -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.shares.*', 'admin.share-categories.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-newspaper"></i>
                            <span>Quản lý chia sẻ</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed(['admin.shares.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.shares.index') }}">
                                    <i class="fas fa-file-alt"></i>
                                    <span>Bài viết</span>
                                    @if(isset($pendingSharesCount) && $pendingSharesCount > 0)
                                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingSharesCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed(['admin.share-categories.*']) ? 'active' : '' }}">
                                <a href="{{ route('admin.share-categories.index') }}">
                                    <i class="fas fa-folder"></i>
                                    <span>Danh mục</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Cấu hình hệ thống -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.socials.*', 'admin.logo-site.*', 'admin.languages.*', 'admin.seo.*', 'admin.setting.*', 'admin.configs.*', 'admin.footer-contents.*', 'admin.contact-links.*', 'admin.header-configs.*', 'admin.terms-of-service.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-cogs"></i>
                            <span>Cấu hình hệ thống</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed('admin.socials.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.socials.index') }}">
                                    <i class="fa-solid fa-globe"></i>
                                    <span>Mạng xã hội</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.logo-site.edit') ? 'active' : '' }}">
                                <a href="{{ route('admin.logo-site.edit') }}">
                                    <i class="fas fa-image"></i>
                                    <span>Logo Site</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.setting.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.setting.index') }}">
                                    <i class="fas fa-cog"></i>
                                    <span>Cài đặt</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.configs.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.configs.index') }}">
                                    <i class="fas fa-database"></i>
                                    <span>Cấu hình</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.seo.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.seo.index') }}">
                                    <i class="fas fa-search"></i>
                                    <span>SEO</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.footer-contents.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.footer-contents.index') }}">
                                    <i class="fas fa-window-restore"></i>
                                    <span>Nội dung Footer</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.contact-links.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.contact-links.index') }}">
                                    <i class="fas fa-link"></i>
                                    <span>Liên kết liên hệ</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.header-configs.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.header-configs.index') }}">
                                    <i class="fas fa-heading"></i>
                                    <span>Cấu hình Header</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.terms-of-service.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.terms-of-service.index') }}">
                                    <i class="fas fa-file-contract"></i>
                                    <span>Điều khoản sử dụng</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="mt-4">
                        <a href="{{ route('home') }}">
                            <i class="fas fa-home"></i>
                            <span>Trang chủ</span>
                        </a>
                        <a href="{{ route('logout') }}" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Toggle sidebar button -->
        <button id="toggle-sidebar" class="toggle-sidebar-btn">
            <i class="fas fa-chevron-left"></i>
        </button>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="admin-topbar">
                <div class="container-fluid">
                    <div class="topbar-content">
                        <div class="topbar-right">
                            @if (Auth::check())
                                <span class="btn btn-wallet">
                                    <img src="{{ asset('images/svg/wallet.svg') }}" alt="wallet">
                                    {{ Auth::user()->wallet && Auth::user()->wallet->balance ? number_format(Auth::user()->wallet->balance, 0, ',', '.') : 0 }} VNĐ
                                </span>
                                <div class="header-separator"></div>
                                <div class="user-dropdown-wrapper">
                                    <button class="btn btn-user" id="adminUserDropdownToggle" type="button">
                                        <div class="user-avatar">
                                            <span
                                                class="user-avatar-initial">{{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <span class="user-name">{{ Auth::user()->full_name ?? 'User' }}</span>
                                        <i class="fas fa-chevron-down user-chevron"></i>
                                    </button>
                                    <div class="user-dropdown-menu" id="adminUserDropdownMenu">
                                        <a href="{{ route('profile.index') }}" class="dropdown-item">
                                            <i class="fas fa-user"></i>
                                            <span>Thông tin cá nhân</span>
                                        </a>
                                        <a href="{{ route('home') }}" class="dropdown-item">
                                            <i class="fas fa-home"></i>
                                            <span>Trang chủ</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('logout') }}" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>Đăng xuất</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-wrapper">
                <div class="content">
                    @yield('main-content')
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {
                initializeSidebarState();

                initializeMenuState();
                addTooltips();

                function initializeSidebarState() {
                    const savedSidebarState = getSidebarState();

                    if ($(window).width() > 768) {
                        if (savedSidebarState === 'collapsed') {
                            $('#sidebar').addClass('collapsed');
                            $('#toggle-sidebar').find('i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
                        } else {
                            $('#sidebar').removeClass('collapsed');
                            $('#toggle-sidebar').find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                        }
                    } else {
                        $('#sidebar').removeClass('collapsed');
                        $('#sidebar').removeClass('show');
                    }
                }

                function saveSidebarState(state) {
                    try {
                        localStorage.setItem('adminSidebarState', state);
                    } catch (e) {
                        console.log('Error saving sidebar state:', e);
                    }
                }

                function getSidebarState() {
                    try {
                        return localStorage.getItem('adminSidebarState');
                    } catch (e) {
                        console.log('Error getting sidebar state:', e);
                        return null;
                    }
                }

                $('.sidebar.collapsed .has-submenu').on('mouseenter', function() {
                    if ($('#sidebar').hasClass('collapsed') && $(window).width() > 768) {
                        const $submenu = $(this).find('.submenu');
                        const menuItemRect = this.getBoundingClientRect();

                        $submenu.css({
                            'top': menuItemRect.top + 'px !important',
                            'left': '70px !important'
                        });
                    }
                });

                $(document).on('mouseenter', '.sidebar.collapsed .has-submenu', function() {
                    if ($('#sidebar').hasClass('collapsed') && $(window).width() > 768) {
                        const $submenu = $(this).find('.submenu');
                        const menuItemRect = this.getBoundingClientRect();

                        $submenu.css({
                            'top': (menuItemRect.top - 10) + 'px',
                            'left': '70px'
                        });
                    }
                });

                // Handle submenu toggle
                $('.submenu-toggle').click(function(e) {
                    e.preventDefault();

                    if ($('#sidebar').hasClass('collapsed') && $(window).width() > 768) {
                        return;
                    }

                    const parentLi = $(this).closest('.has-submenu');
                    const isCurrentlyOpen = parentLi.hasClass('open');
                    const menuKey = getMenuKey(parentLi);

                    $('.has-submenu').not(parentLi).each(function() {
                        const $this = $(this);
                        $this.removeClass('open');
                        saveMenuState(getMenuKey($this), false);
                    });

                    if (isCurrentlyOpen) {
                        parentLi.removeClass('open');
                        saveMenuState(menuKey, false);
                    } else {
                        parentLi.addClass('open');
                        saveMenuState(menuKey, true);
                    }
                });

                $('.submenu a').click(function(e) {
                    if ($(window).width() <= 768) {
                        $('#sidebar').removeClass('show');
                    }

                    const parentSubmenu = $(this).closest('.has-submenu');
                    if (parentSubmenu.length) {
                        const menuKey = getMenuKey(parentSubmenu);
                        saveMenuState(menuKey, true);
                    }
                });

                function addTooltips() {
                    $('.sidebar-menu ul li:not(.has-submenu)').each(function() {
                        const menuText = $(this).find('span').text().trim();
                        $(this).attr('data-tooltip', menuText);
                    });
                }

                function initializeMenuState() {
                    $('.has-submenu').each(function() {
                        const $this = $(this);
                        const menuKey = getMenuKey($this);

                        // If server-side already marked as open (active route), save and keep it open
                        if ($this.hasClass('open')) {
                            saveMenuState(menuKey, true);
                            return;
                        }

                        const savedState = getMenuState(menuKey);
                        if (savedState === 'true') {
                            $this.addClass('open');
                        } else if (savedState === 'false') {
                            $this.removeClass('open');
                        }
                    });
                }

                function getMenuKey(menuElement) {
                    const menuText = menuElement.find('.submenu-toggle span').first().text().trim();
                    return 'menu_' + menuText.replace(/\s+/g, '_').toLowerCase();
                }

                function saveMenuState(menuKey, isOpen) {
                    try {
                        const menuStates = JSON.parse(localStorage.getItem('adminMenuStates') || '{}');
                        menuStates[menuKey] = isOpen;
                        localStorage.setItem('adminMenuStates', JSON.stringify(menuStates));
                    } catch (e) {
                        console.log('Error saving menu state:', e);
                    }
                }

                function getMenuState(menuKey) {
                    try {
                        const menuStates = JSON.parse(localStorage.getItem('adminMenuStates') || '{}');
                        return menuStates[menuKey];
                    } catch (e) {
                        console.log('Error getting menu state:', e);
                        return null;
                    }
                }

                // Sidebar toggle functionality
                $('#toggle-sidebar').click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if ($(window).width() <= 768) {
                        $('#sidebar').toggleClass('show');
                    } else {
                        $('#sidebar').toggleClass('collapsed');

                        const icon = $(this).find('i');
                        if ($('#sidebar').hasClass('collapsed')) {
                            icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                            saveSidebarState('collapsed');

                            setTimeout(() => {
                                $('.has-submenu').each(function() {
                                    const $submenu = $(this).find('.submenu');
                                    $submenu.css({
                                        'top': 'auto',
                                        'left': '70px'
                                    });
                                });
                            }, 100);
                        } else {
                            icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                            saveSidebarState('expanded');

                            initializeMenuState();

                            $('.submenu').css({
                                'top': 'auto',
                                'left': 'auto',
                                'position': 'relative'
                            });
                        }
                    }
                });

                $(document).click(function(e) {
                    if ($(window).width() <= 768) {
                        if (!$(e.target).closest('#sidebar, #toggle-sidebar').length) {
                            $('#sidebar').removeClass('show');
                        }
                    }
                });

                $('#close-sidebar').click(function() {
                    $('#sidebar').removeClass('show');
                });

                $(window).resize(function() {
                    if ($(window).width() > 768) {
                        $('#sidebar').removeClass('show');

                        initializeSidebarState();

                        if (!$('#sidebar').hasClass('collapsed')) {
                            initializeMenuState();
                            $('.submenu').css({
                                'top': 'auto',
                                'left': 'auto',
                                'position': 'relative'
                            });
                        }
                    } else {
                        $('#sidebar').removeClass('collapsed');

                        initializeMenuState();

                        $('.submenu').css({
                            'top': 'auto',
                            'left': 'auto',
                            'position': 'relative'
                        });
                    }
                });

                const adminUserDropdownToggle = document.getElementById('adminUserDropdownToggle');
                const adminUserDropdownMenu = document.getElementById('adminUserDropdownMenu');

                if (adminUserDropdownToggle && adminUserDropdownMenu) {
                    adminUserDropdownToggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        adminUserDropdownMenu.classList.toggle('show');
                        const chevron = this.querySelector('.user-chevron');
                        if (chevron) {
                            chevron.classList.toggle('rotate');
                        }
                    });

                    document.addEventListener('click', function(e) {
                        if (!adminUserDropdownToggle.contains(e.target) && !adminUserDropdownMenu.contains(e
                                .target)) {
                            adminUserDropdownMenu.classList.remove('show');
                            const chevron = adminUserDropdownToggle.querySelector('.user-chevron');
                            if (chevron) {
                                chevron.classList.remove('rotate');
                            }
                        }
                    });

                    const dropdownItems = adminUserDropdownMenu.querySelectorAll('.dropdown-item');
                    dropdownItems.forEach(item => {
                        item.addEventListener('click', function() {
                            adminUserDropdownMenu.classList.remove('show');
                            const chevron = adminUserDropdownToggle.querySelector('.user-chevron');
                            if (chevron) {
                                chevron.classList.remove('rotate');
                            }
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection

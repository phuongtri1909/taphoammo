@extends('seller.layouts.app')

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
                    <li class="{{ Route::currentRouteNamed('seller.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('seller.dashboard') }}">
                            <div class="icon-gradient-mask" style="--img: url('{{ asset('images/svg/admin/dashboard.svg') }}');"></div>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li
                        class="{{ Route::currentRouteNamed(['']) ? 'open' : '' }}">
                        <a href="">
                            <div class="icon-gradient-mask" style="--img: url('{{ asset('images/svg/admin/cart.svg') }}');"></div>
                            <span>Mua dịch vụ</span>
                        </a>
                    </li>

                    <li class="mt-4">
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
                                <a href="#" class="btn btn-wallet">
                                    <img src="{{ asset('images/svg/wallet.svg') }}" alt="wallet">
                                    0 VNĐ
                                </a>
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
                                        <a href="#" class="dropdown-item">
                                            <i class="fas fa-user"></i>
                                            <span>Thông tin cá nhân</span>
                                        </a>
                                        <a href="#" class="dropdown-item">
                                            <i class="fas fa-cog"></i>
                                            <span>Cài đặt</span>
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

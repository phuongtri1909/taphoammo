@extends('admin.layouts.app')

@section('content')
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <img src="{{  $logoPath }}" alt="logo" height="40">
                <button id="close-sidebar" class="close-sidebar d-md-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li class="{{ Route::currentRouteNamed('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.banner-homes.*', 'admin.image-homes.*', 'admin.general-introductions.*', 'admin.intro-features.*', 'admin.intro-locations.*', 'admin.slide-locations.*', 'admin.intro-images.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-home"></i>
                            <span>Trang chủ</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                    </li>

                    <!-- Cấu hình hệ thống -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.socials.*', 'admin.logo-site.*', 'admin.languages.*', 'admin.seo.*', 'admin.setting.*']) ? 'open' : '' }}">
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
                            <li class="{{ Route::currentRouteNamed('admin.seo.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.seo.index') }}">
                                    <i class="fas fa-cog"></i>
                                    <span>SEO</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="mt-4">
                        <a href="{{ route('logout') }}">
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
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="content">
                    <div class="container-fluid">
                        @yield('main-content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>

            /* Submenu styles */
            .has-submenu {
                position: relative;
            }

            .submenu-toggle {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                text-decoration: none !important;
            }

            .has-submenu.open .submenu-toggle {
                color: var(--primary-color);
            }

            .submenu-arrow {
                font-size: 12px;
                transition: transform 0.3s ease;
                margin-left: auto;
            }

            .has-submenu.open .submenu-arrow {
                transform: rotate(180deg);
            }

            .submenu {
                display: none;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 6px;
                margin: 8px 0;
                padding: 0;
                list-style: none;
                overflow: hidden;
                max-height: 0;
                opacity: 0;
                transition: all 0.3s ease;
            }

            .has-submenu.open .submenu {
                display: block;
                max-height: 500px;
                opacity: 1;
            }

            .submenu li {
                margin: 0;
            }

            .submenu li a {
                padding: 12px 20px 12px 60px;
                font-size: 14px;
                color: rgba(255, 255, 255, 0.8);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                text-decoration: none;
            }

            .submenu li a:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
                transform: translateX(5px);
            }

            .submenu li.active a {
                background: #D1A66E;
                color: #fff;
                font-weight: 500;
            }

            .submenu li a i {
                margin-right: 12px;
                width: 16px;
                text-align: center;
                font-size: 14px;
            }

            /* Main menu active state */
            .sidebar-menu>ul>li.has-submenu.open>a {
                background: rgba(255, 255, 255, 0.1);
            }

            /* Collapsed sidebar adjustments - ONLY ON DESKTOP */
            @media (min-width: 769px) {
                .sidebar.collapsed .submenu-arrow {
                    display: none;
                }

                .sidebar.collapsed .sidebar-menu ul li span {
                    display: none;
                }

                .sidebar.collapsed {
                    width: 70px;
                }

                .sidebar.collapsed .sidebar-menu ul li a {
                    justify-content: center;
                    padding: 15px;
                }

                .sidebar.collapsed .sidebar-menu ul li a i {
                    margin-right: 0;
                }

                .sidebar.collapsed~.main-content {
                    margin-left: 70px !important;
                }

                /* Collapsed submenu - MOVE OUTSIDE sidebar */
                .sidebar.collapsed .submenu {
                    display: none !important;
                    position: fixed !important;
                    left: 70px !important;
                    top: auto !important;
                    transform: translateY(-40px) !important;
                    background: #2c3e50;
                    border-radius: 6px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                    z-index: 1100;
                    padding: 8px 0;
                    margin: 0;
                    max-height: none !important;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.3s ease, visibility 0.3s ease;
                }

                /* Position submenu next to the hovered menu item */
                .sidebar.collapsed .has-submenu:hover .submenu {
                    display: block !important;
                    opacity: 1 !important;
                    visibility: visible !important;
                }

                /* Calculate position dynamically with JavaScript help */
                .sidebar.collapsed .has-submenu {
                    position: relative;
                }

                .sidebar.collapsed .submenu li a {
                    padding: 12px 20px;
                    margin: 0;
                    border-radius: 0;
                    color: rgba(255, 255, 255, 0.8);
                    font-size: 14px;
                    transform: none !important;
                }

                .sidebar.collapsed .submenu li a:hover {
                    background: rgba(255, 255, 255, 0.1);
                    transform: none;
                }

                .sidebar.collapsed .submenu li.active a {
                    background: #D1A66E;
                }

                .sidebar.collapsed .submenu li a i {
                    margin-right: 12px;
                }

                /* Tooltip for collapsed menu items */
                .sidebar.collapsed .sidebar-menu ul li {
                    position: relative;
                }

                .sidebar.collapsed .sidebar-menu ul li:not(.has-submenu):hover::after {
                    content: attr(data-tooltip);
                    position: absolute;
                    left: 70px;
                    top: 50%;
                    transform: translateY(-50%);
                    background: #2c3e50;
                    color: white;
                    padding: 8px 12px;
                    border-radius: 4px;
                    font-size: 14px;
                    white-space: nowrap;
                    z-index: 1100;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                }
            }

            /* Override toggle button styles from main CSS */
            .toggle-sidebar-btn {
                position: fixed !important;
                top: 70px !important;
                left: 250px !important;
                transform: translateY(-50%) !important;
                width: 24px;
                height: 48px;
                border-radius: 0 24px 24px 0;
                background-color: #fff;
                border: 1px solid #ddd;
                color: #6c757d;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                cursor: pointer !important;
                z-index: 1000 !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
            }

            .sidebar.collapsed+.toggle-sidebar-btn {
                left: 70px !important;
            }

            .toggle-sidebar-btn:hover {
                background-color: var(--primary-hover, #0056b3) !important;
                color: white !important;
                transform: translateY(-50%) scale(1.1) !important;
            }

            .toggle-sidebar-btn i {
                margin: 0 !important;
                font-size: 14px !important;
            }

            /* Main content margin adjustments */
            .main-content {
                margin-left: 250px !important;
                transition: margin-left 0.3s ease !important;
            }

            /* Mobile styles */
            @media (max-width: 768px) {
                .sidebar {
                    position: fixed;
                    left: -100%;
                    transition: left 0.3s ease;
                    z-index: 1050;
                    width: 250px;
                }

                .sidebar.show {
                    left: 0;
                }

                .sidebar .sidebar-menu ul li span {
                    display: block !important;
                }

                .sidebar .submenu-arrow {
                    display: block !important;
                }

                .sidebar .submenu {
                    display: none;
                }

                .sidebar.open .submenu {
                    display: block !important;
                }

                .toggle-sidebar-btn {
                    left: 0 !important;
                }

                .sidebar.show~.toggle-sidebar-btn {
                    left: 20px !important;
                    background-color: #dc3545 !important;
                }

                .submenu li a {
                    padding-left: 50px;
                    font-size: 13px;
                }

                .main-content {
                    margin-left: 0 !important;
                }

                .sidebar.collapsed {
                    width: 250px !important;
                    left: -100% !important;
                }

                .sidebar.collapsed.show {
                    left: 0 !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize sidebar state from localStorage
                initializeSidebarState();

                // Initialize submenu state
                initializeMenuState();
                // Add tooltips for collapsed menu items
                addTooltips();

                // Initialize sidebar state on page load
                function initializeSidebarState() {
                    const savedSidebarState = getSidebarState();

                    if ($(window).width() > 768) {
                        // Desktop: restore collapsed state
                        if (savedSidebarState === 'collapsed') {
                            $('#sidebar').addClass('collapsed');
                            $('#toggle-sidebar').find('i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
                        } else {
                            $('#sidebar').removeClass('collapsed');
                            $('#toggle-sidebar').find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                        }
                    } else {
                        // Mobile: don't restore collapsed state, but can restore show state if needed
                        $('#sidebar').removeClass('collapsed');
                        // Mobile sidebar should be hidden by default
                        $('#sidebar').removeClass('show');
                    }
                }

                // Save sidebar state to localStorage
                function saveSidebarState(state) {
                    try {
                        localStorage.setItem('adminSidebarState', state);
                    } catch (e) {
                        console.log('Error saving sidebar state:', e);
                    }
                }

                // Get sidebar state from localStorage
                function getSidebarState() {
                    try {
                        return localStorage.getItem('adminSidebarState');
                    } catch (e) {
                        console.log('Error getting sidebar state:', e);
                        return null;
                    }
                }

                // Handle submenu positioning for collapsed sidebar
                $('.sidebar.collapsed .has-submenu').on('mouseenter', function() {
                    if ($('#sidebar').hasClass('collapsed') && $(window).width() > 768) {
                        const $submenu = $(this).find('.submenu');
                        const menuItemRect = this.getBoundingClientRect();

                        // Position submenu at the same height as the menu item
                        $submenu.css({
                            'top': menuItemRect.top + 'px !important',
                            'left': '70px !important'
                        });
                    }
                });

                // Update submenu positions when hovering (in case of scroll)
                $(document).on('mouseenter', '.sidebar.collapsed .has-submenu', function() {
                    if ($('#sidebar').hasClass('collapsed') && $(window).width() > 768) {
                        const $submenu = $(this).find('.submenu');
                        const menuItemRect = this.getBoundingClientRect();

                        // Position submenu at the same height as the menu item minus 10px
                        $submenu.css({
                            'top': (menuItemRect.top - 10) + 'px',
                            'left': '70px'
                        });
                    }
                });

                // Handle submenu toggle
                $('.submenu-toggle').click(function(e) {
                    e.preventDefault();

                    // Don't toggle submenu if sidebar is collapsed on desktop
                    // Instead, let hover handle it
                    if ($('#sidebar').hasClass('collapsed') && $(window).width() > 768) {
                        return;
                    }

                    const parentLi = $(this).closest('.has-submenu');
                    const isCurrentlyOpen = parentLi.hasClass('open');
                    const menuKey = getMenuKey(parentLi);

                    // Close all other submenus
                    $('.has-submenu').not(parentLi).each(function() {
                        const $this = $(this);
                        $this.removeClass('open');
                        saveMenuState(getMenuKey($this), false);
                    });

                    // Toggle current submenu
                    if (isCurrentlyOpen) {
                        parentLi.removeClass('open');
                        saveMenuState(menuKey, false);
                    } else {
                        parentLi.addClass('open');
                        saveMenuState(menuKey, true);
                    }
                });

                // Prevent submenu links from closing the parent menu
                $('.submenu a').click(function(e) {
                    // Close mobile sidebar after clicking submenu item
                    if ($(window).width() <= 768) {
                        $('#sidebar').removeClass('show');
                    }

                    const parentSubmenu = $(this).closest('.has-submenu');
                    if (parentSubmenu.length) {
                        const menuKey = getMenuKey(parentSubmenu);
                        saveMenuState(menuKey, true);
                    }
                });

                // Add tooltips for menu items
                function addTooltips() {
                    $('.sidebar-menu ul li:not(.has-submenu)').each(function() {
                        const menuText = $(this).find('span').text().trim();
                        $(this).attr('data-tooltip', menuText);
                    });
                }

                // Initialize menu state on page load
                function initializeMenuState() {
                    $('.has-submenu').each(function() {
                        const $this = $(this);
                        const menuKey = getMenuKey($this);

                        // If server-side already marked as open (active route), save and keep it open
                        if ($this.hasClass('open')) {
                            saveMenuState(menuKey, true);
                            return;
                        }

                        // Otherwise, check localStorage for saved state
                        const savedState = getMenuState(menuKey);
                        if (savedState === 'true') {
                            $this.addClass('open');
                        } else if (savedState === 'false') {
                            $this.removeClass('open');
                        }
                    });
                }

                // Generate unique key for each menu based on its content
                function getMenuKey(menuElement) {
                    const menuText = menuElement.find('.submenu-toggle span').first().text().trim();
                    return 'menu_' + menuText.replace(/\s+/g, '_').toLowerCase();
                }

                // Save menu state to localStorage
                function saveMenuState(menuKey, isOpen) {
                    try {
                        const menuStates = JSON.parse(localStorage.getItem('adminMenuStates') || '{}');
                        menuStates[menuKey] = isOpen;
                        localStorage.setItem('adminMenuStates', JSON.stringify(menuStates));
                    } catch (e) {
                        console.log('Error saving menu state:', e);
                    }
                }

                // Get menu state from localStorage
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
                        // Mobile: only toggle show/hide, never collapse
                        $('#sidebar').toggleClass('show');
                        // Don't save mobile state to localStorage
                    } else {
                        // Desktop: toggle collapsed state
                        $('#sidebar').toggleClass('collapsed');

                        const icon = $(this).find('i');
                        if ($('#sidebar').hasClass('collapsed')) {
                            icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                            // Save collapsed state
                            saveSidebarState('collapsed');

                            // Reset submenu positions for collapsed state
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
                            // Save expanded state
                            saveSidebarState('expanded');

                            // Restore submenu states when sidebar is expanded
                            initializeMenuState();

                            // Reset submenu positioning
                            $('.submenu').css({
                                'top': 'auto',
                                'left': 'auto',
                                'position': 'relative'
                            });
                        }
                    }
                });

                // Close sidebar on mobile when clicking outside
                $(document).click(function(e) {
                    if ($(window).width() <= 768) {
                        if (!$(e.target).closest('#sidebar, #toggle-sidebar').length) {
                            $('#sidebar').removeClass('show');
                        }
                    }
                });

                // Mobile sidebar close button
                $('#close-sidebar').click(function() {
                    $('#sidebar').removeClass('show');
                });

                // Handle window resize
                $(window).resize(function() {
                    if ($(window).width() > 768) {
                        // Desktop mode: remove mobile show class
                        $('#sidebar').removeClass('show');

                        // Restore sidebar state from localStorage
                        initializeSidebarState();

                        // Restore submenu functionality based on collapsed state
                        if (!$('#sidebar').hasClass('collapsed')) {
                            initializeMenuState();
                            // Reset submenu positioning for expanded state
                            $('.submenu').css({
                                'top': 'auto',
                                'left': 'auto',
                                'position': 'relative'
                            });
                        }
                    } else {
                        // Mobile mode: remove collapsed class, keep full functionality
                        $('#sidebar').removeClass('collapsed');

                        // Restore all submenu states on mobile
                        initializeMenuState();

                        // Reset submenu positioning for mobile
                        $('.submenu').css({
                            'top': 'auto',
                            'left': 'auto',
                            'position': 'relative'
                        });
                    }
                });

                // Debug functions - uncomment to clear states if needed
                // localStorage.removeItem('adminMenuStates');
                // localStorage.removeItem('adminSidebarState');
            });
        </script>
    @endpush
@endsection

<!-- Top Support Bar -->
<div class="bg-blue-200 py-0 transition-all duration-300 header-top-bar" id="headerTopBar">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="flex w-full items-center justify-between space-x-2">
            <span class="flex items-center space-x-2">
                <span
                    class="hidden items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20 lg:flex">
                    Hỗ trợ trực tuyến:
                </span>
                <a href="https://facebook.com/shoptaphoazalo" target="_blank"
                    class="hidden items-center space-x-1 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 md:flex hover:opacity-80 transition-opacity">
                    <i class="fab fa-facebook-f"></i>
                    <span>facebook.com/shoptaphoazalo</span>
                </a>
                <a href="mailto:shoptaphoazalo@gmail.com"
                    class="hidden items-center space-x-1 rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 md:flex hover:opacity-80 transition-opacity">
                    <i class="fas fa-envelope"></i>
                    <span>shoptaphoazalo@gmail.com</span>
                </a>
            </span>
            <span class="flex items-center space-x-2">
                <span
                    class="mr-auto rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                    Thời gian hoạt động của sàn 24/7
                </span>
            </span>
        </div>
    </div>
</div>

<!-- Main Navigation Bar -->
<div class="sticky top-0 z-50 header-main" id="header">
    <nav class="bg-gradient-to-r from-white to-primary">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-14 items-center justify-between">
                <!-- Mobile Menu Button -->
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                    <button
                        class="relative items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        id="mobileMenuToggle">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars block h-6 w-6" id="mobileMenuIcon"></i>
                    </button>
                </div>

                <!-- Logo Section -->
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo/Logo-site-1050-x-300.webp') }}" alt="{{ config('app.name') }}"
                            class="img-fluid logo-site w-50 h-12">
                    </a>
                    <!-- Desktop Navigation -->
                    <div class="hidden items-center md:ml-6 md:flex">
                        <div class="flex space-x-3">
                            <a href="{{ route('home') }}"
                                class="rounded-3xl px-1 py-1 text-sm font-medium md:px-4 {{ Route::currentRouteNamed('home') ? 'bg-primary-6 text-white' : 'text-white hover:bg-primary hover:text-white' }} nav-link">
                                Trang chủ
                            </a>
                            <a href="#"
                                class="rounded-3xl px-1 py-1 text-sm font-medium md:px-4 text-white hover:bg-primary hover:text-white nav-link flex items-center gap-1">
                                Sản phẩm
                                <i class="fas fa-chevron-up text-xs"></i>
                            </a>
                            <a href="#"
                                class="rounded-3xl px-1 py-1 text-sm font-medium md:px-4 text-white hover:bg-primary hover:text-white nav-link flex items-center gap-1">
                                Dịch vụ
                                <i class="fas fa-chevron-up text-xs"></i>
                            </a>
                            <a href="#"
                                class="rounded-3xl px-1 py-1 text-sm font-medium md:px-3 text-white hover:bg-primary hover:text-white nav-link">Liên
                                hệ</a>
                            <a href="#"
                                class="rounded-3xl px-1 py-1 text-sm font-medium md:px-3 text-white hover:bg-primary hover:text-white nav-link">Chia
                                sẻ</a>
                            <a href="#"
                                class="rounded-3xl px-1 py-1 text-sm font-medium md:px-3 text-white hover:bg-primary hover:text-white nav-link">FAQs</a>
                            @auth
                                <a href="#"
                                    class="rounded-3xl px-1 py-1 text-sm font-medium md:px-3 text-white hover:bg-primary hover:text-white nav-link {{ !auth()->user() ? 'hidden' : '' }}">Nạp
                                    tiền</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                @auth
                    @php
                        $user = auth()->user();
                    @endphp
                    @if ($user)
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">

                            @if (auth()->user()->canRegisterAsSeller())
                                <a href="{{ route('seller.register') }}" class="mr-2 hidden cursor-pointer font-medium text-red-500 lg:flex">
                                    Bán hàng
                                </a>
                            @endif


                            <div class="mr-2 hidden font-medium text-white lg:flex">
                                <h4>
                                    Số dư: {{ $user->balance }}VNĐ
                                </h4>
                            </div>

                            <!-- User Avatar Menu -->
                            <div class="relative ml-3 user-menu">
                                <button
                                    class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                    id="userMenuButton">
                                    <span class="absolute -inset-1.5"></span>
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full overflow-hidden">
                                        @if (auth()->check() && auth()->user()->avatar)
                                            <img src="{{ Storage::url($avatarPath) }}" alt="Avatar"
                                                class="h-full w-full object-cover">
                                        @else
                                            <img src="{{ asset('images/default/avatar_default.jpg') }}" alt=""
                                                class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                </button>
                                <!-- Dropdown Menu -->
                                <div class="absolute right-0 z-10 mt-2 w-52 origin-top-right rounded-md bg-white py-0 shadow-lg focus:outline-none hidden user-menu-dropdown"
                                    id="userMenuDropdown">
                                    <div class="block px-2 font-medium text-green-500 lg:hidden border-b pb-2 pt-2">
                                        <h5>Số dư: {{ $user->balance }}VNĐ</h5>
                                    </div>
                                    @if (($user->role ?? null) === 'user')
                                        <div class="block cursor-pointer px-2 font-medium text-red-500 lg:hidden border-b pb-2 pt-2"
                                            onclick="handleClickSell()">
                                            <h5>Đăng kí bán hàng</h5>
                                        </div>
                                    @endif
                                    <a href="{{ route('profile.index') }}"
                                        class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Trang cá
                                        nhân</a>
                                    <a href="#"
                                        class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Đơn hàng đã
                                        mua</a>
                                    <a href="#"
                                        class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Gian hàng
                                        yêu thích</a>
                                    <a href="#"
                                        class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Lịch sử
                                        thanh toán</a>
                                    <a href="#"
                                        class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Chat</a>
                                    <a href="#"
                                        class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Quản lí nội
                                        dung</a>
                                    <a href="#"
                                        class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Đổi mật
                                        khẩu</a>
                                    @if (($user->role ?? null) !== 'user')
                                        <a href="{{ route('seller.dashboard') }}"
                                            class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-t border-gray-200">Quản
                                            lí cửa hàng</a>
                                    @endif

                                    @if (($user->role ?? null) === 'admin')
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="relative block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin
                                            Dashboard</a>
                                    @endif
                                    <a href="{{ route('logout') }}"
                                        class="relative block px-4 py-2 text-sm text-gray-700 text-center text-white bg-primary">Đăng
                                        xuất</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                        <a href="{{ route('sign-in') }}"
                            class="bg-primary/80 px-2 py-1 text-xl font-semibold text-white hover:opacity-90 transition-opacity">
                            Đăng nhập
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Promotional Banner -->
    <div class="w-full overflow-hidden whitespace-nowrap bg-green-50 font-medium text-red-500 ring-1 ring-inset ring-green-600/20 promo-banner"
        id="promoBanner">
        <p class="promo-marquee-text align-middle">
            <span class="w-max rounded-md text-xs">
                Tạp Hóa MMO - Sàn thương mại điện tử sản phẩm số phục vụ Kiếm tiền online. Mọi giao dịch trên trang đều
                hoàn toàn tự động và được giữ tiền 3 ngày, thay thế cho hình thức trung gian, các bạn yên tâm giao dịch
                nhé. (2) Cảnh báo gian hàng không uy tín: Nếu chủ shop bán cho bạn sản phẩm không đúng định dạng:
                tài-khoản|mật-khẩu..., mà là 1 chuỗi không liên quan ở đầu, có nghĩa là hàng đó đang cố pass hệ thống
                check trùng của sàn, hãy nhanh chóng khiếu nại đơn hàng và báo cho bên mình nhé, vì sản phẩm bạn mua có
                thể đã từng bán cho người khác trên sàn.
            </span>
        </p>
    </div>
</div>


<!-- Mobile Side Menu Overlay -->
<div class="fixed inset-0 bg-black/50 z-[1001] opacity-0 invisible transition-all duration-300 mobile-side-menu-overlay"
    id="mobileMenuOverlay"></div>

<!-- Mobile Side Menu -->
<div class="fixed top-0 -right-full w-[240px] max-w-[60%] h-screen bg-primary z-[1002] transition-[right] duration-300 shadow-[-2px_0_8px_rgba(0,0,0,0.1)] overflow-y-auto mobile-side-menu"
    id="mobileSideMenu">
    <div class="flex justify-center items-center p-4 border-b border-white/10 bg-white">
        <img src="{{ asset('images/logo/Logo-site-1050-x-300.webp') }}" alt="{{ config('app.name') }}"
            class="img-fluid logo-site w-60 h-15">
    </div>

    <div class="space-y-2 py-2">
        <a href="{{ route('home') }}"
            class="flex w-full flex-1 flex-col items-start border-b border-white/20 px-3 py-2 text-start text-sm font-medium text-white hover:bg-blue-900/90 {{ Route::currentRouteNamed('home') ? 'bg-primary-6' : '' }}"
            onclick="closeMobileMenu()">
            Trang chủ
        </a>

        <!-- Sản phẩm Dropdown -->
        <div class="mobile-dropdown">
            <button
                class="flex w-full items-center justify-between border-b border-white/20 px-3 py-2 text-start text-sm font-medium text-white hover:bg-blue-900/90 mobile-dropdown-toggle">
                <span>Sản phẩm</span>
                <i class="fas fa-chevron-down text-xs transition-transform mobile-dropdown-icon"></i>
            </button>
            <div class="hidden mt-2 grid w-full grid-cols-2 gap-2 bg-blue-700 px-3 pb-2 mobile-dropdown-content">
                <a href="#" class="text-start text-xs text-white hover:underline"
                    onclick="closeMobileMenu()">Sub 1</a>
                <a href="#" class="text-start text-xs text-white hover:underline"
                    onclick="closeMobileMenu()">Sub 2</a>
            </div>
        </div>

        <!-- Dịch vụ Dropdown -->
        <div class="mobile-dropdown">
            <button
                class="flex w-full items-center justify-between border-b border-white/20 px-3 py-2 text-start text-sm font-medium text-white hover:bg-blue-900/90 mobile-dropdown-toggle">
                <span>Dịch vụ</span>
                <i class="fas fa-chevron-down text-xs transition-transform mobile-dropdown-icon"></i>
            </button>
            <div class="hidden mt-2 grid w-full grid-cols-2 gap-2 bg-blue-700 px-3 pb-2 mobile-dropdown-content">
                <a href="#" class="text-start text-xs text-white hover:underline"
                    onclick="closeMobileMenu()">Sub 1</a>
                <a href="#" class="text-start text-xs text-white hover:underline"
                    onclick="closeMobileMenu()">Sub 2</a>
            </div>
        </div>

        <a href="#"
            class="flex w-full flex-1 flex-col items-start border-b border-white/20 px-3 py-2 text-start text-sm font-medium text-white hover:bg-blue-900/90"
            onclick="closeMobileMenu()">Liên hệ</a>
        <a href="#"
            class="flex w-full flex-1 flex-col items-start border-b border-white/20 px-3 py-2 text-start text-sm font-medium text-white hover:bg-blue-900/90"
            onclick="closeMobileMenu()">Chia sẻ</a>
        <a href="#"
            class="flex w-full flex-1 flex-col items-start border-b border-white/20 px-3 py-2 text-start text-sm font-medium text-white hover:bg-blue-900/90"
            onclick="closeMobileMenu()">FAQs</a>
        @auth
            <a href="#"
                class="flex w-full flex-1 flex-col items-start border-b border-white/20 px-3 py-2 text-start text-sm font-medium text-white hover:bg-blue-900/90"
                onclick="closeMobileMenu()">Nạp tiền</a>
        @endauth
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileSideMenu = document.getElementById('mobileSideMenu');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const mobileMenuClose = document.getElementById('mobileMenuClose');
        const headerTopBar = document.getElementById('headerTopBar');
        const header = document.getElementById('header');

        function updateHeaderPosition() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 50) {
                headerTopBar.classList.add('hidden');
            } else {
                headerTopBar.classList.remove('hidden');
            }
        }

        updateHeaderPosition();

        window.addEventListener('scroll', function() {
            updateHeaderPosition();
        });

        mobileMenuToggle.addEventListener('click', function() {
            mobileSideMenu.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            const icon = document.getElementById('mobileMenuIcon');
            if (icon) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        });

        window.closeMobileMenu = function() {
            mobileSideMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
            const icon = document.getElementById('mobileMenuIcon');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        };

        mobileMenuOverlay.addEventListener('click', window.closeMobileMenu);

        const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
        mobileDropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const dropdown = this.closest('.mobile-dropdown');
                const content = dropdown.querySelector('.mobile-dropdown-content');
                const icon = dropdown.querySelector('.mobile-dropdown-icon');

                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });

        const userMenuButton = document.getElementById('userMenuButton');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        if (userMenuButton && userMenuDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }

        window.handleClickSell = function() {
            @auth
            @php
                $user = auth()->user();
            @endphp
            @if ($user && ($user->role ?? null) !== 'user')
                showToast('Bạn đã là người bán hàng', 'warning');
            @endif
        @endauth
    };
    });
</script>

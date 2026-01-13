@extends('client.layouts.app')
@section('title', 'Home - ' . config('app.name'))
@section('description',
    config('app.name') .
    ' là dự án được đầu tư bởi Tập đoàn Hoàng Gia Việt Nam, với quy hoạch tổng thể như một
    tổ hợp khu công nghiệp và đô thị xanh, tuân thủ các tiêu chuẩn sinh thái, tích hợp sản xuất bền vững, logistics và không
    gian sống thân thiện với môi trường, với tổng quy mô hơn 2.300 ha.')
@section('keywords', config('app.name'))

@section('content')
    <!-- Search Component -->
    <div class="relative w-full min-h-[400px] flex items-center justify-center py-16">
        <!-- Wrapper Container (thụt vào 90px so với header container) -->
        <div class="relative w-full max-w-[calc(80rem-180px)] mx-auto min-h-[400px] flex items-center justify-center">
            <!-- Background Container -->
            <div class="absolute inset-0 search-component bg-cover bg-center bg-no-repeat rounded-lg"
                style="background-image: url('{{ asset('images/d/background.jpg') }}');">
                <!-- Overlay for better text readability -->
                <div class="absolute inset-0 bg-black/30 rounded-lg"></div>
            </div>

            <!-- Search Container -->
            <div class="relative z-10 w-full max-w-4xl flex flex-col items-center justify-center px-2 sm:px-6 lg:px-8">
                <!-- Dropdowns Row -->
                <div class="flex flex-col sm:flex-row gap-4 mb-4 w-full">
                    <!-- Sản phẩm Dropdown -->
                    <div class="flex-1 relative">
                        <select id="productDropdown"
                            class="w-full py-3.5 px-4 rounded-lg border-2 border-white bg-white/95 backdrop-blur-sm text-gray-700 text-sm font-medium cursor-pointer appearance-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-md hover:shadow-lg">
                            <option value="">Sản phẩm</option>
                            <option value="facebook">Facebook</option>
                            <option value="zalo">Zalo</option>
                            <option value="telegram">Telegram</option>
                            <option value="tiktok">TikTok</option>
                            <option value="instagram">Instagram</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Tài khoản Dropdown -->
                    <div class="flex-1 relative">
                        <select id="accountDropdown"
                            class="w-full py-3.5 px-4 rounded-lg border-2 border-white bg-white/95 backdrop-blur-sm text-gray-700 text-sm font-medium cursor-pointer appearance-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-md hover:shadow-lg">
                            <option value="">Tài khoản</option>
                            <option value="premium">Tài khoản Premium</option>
                            <option value="vip">Tài khoản VIP</option>
                            <option value="normal">Tài khoản Thường</option>
                            <option value="verified">Tài khoản Đã xác thực</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- "Hoặc" Text -->
                <div class="flex items-center justify-center my-4 w-full">
                    <span class="text-white font-bold text-xl">Hoặc</span>
                </div>

                <!-- Search Input and Button Row -->
                <div class="flex flex-col gap-3 w-full">
                    <!-- Search Input -->
                    <div class="w-full relative">
                        <input type="text" id="searchInput" placeholder="Tìm gian hàng và người bán"
                            class="w-full py-3.5 px-4 pr-12 rounded-lg border-2 border-white bg-white/95 backdrop-blur-sm text-gray-700 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-md hover:shadow-lg">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="flex justify-center w-full">
                        <button type="button" id="searchButton"
                            class="px-8 py-3.5 bg-primary hover:bg-primary-6 text-white font-semibold rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 whitespace-nowrap">
                            Tìm kiếm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchButton = document.getElementById('searchButton');
            const searchInput = document.getElementById('searchInput');
            const productDropdown = document.getElementById('productDropdown');
            const accountDropdown = document.getElementById('accountDropdown');

            searchButton.addEventListener('click', function() {
                const searchQuery = searchInput.value.trim();
                const product = productDropdown.value;
                const account = accountDropdown.value;

                // Build search URL or perform search
                const params = new URLSearchParams();
                if (searchQuery) params.append('q', searchQuery);
                if (product) params.append('product', product);
                if (account) params.append('account', account);

                // Redirect to search results or perform AJAX search
                const searchUrl = '/search?' + params.toString();
                window.location.href = searchUrl;
            });

            // Allow Enter key to trigger search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchButton.click();
                }
            });
        });
    </script>

    <!-- Product and Service Categories -->
    <div class="w-full bg-white">
        <!-- Container (giống search component) -->
        <div class="w-full max-w-[calc(80rem-180px)] mx-auto px-2 md:px-0">
            <!-- Danh sách Sản phẩm -->
            <div class="mb-16">
                <!-- Section Title -->
                <div class="relative flex items-center justify-center mb-8">
                    <div class="relative bg-white px-6">
                        <h2 class="text-center text-xl font-medium text-emerald-500">-- DANH SÁCH SẢN PHẨM --</h2>
                    </div>
                </div>

                <!-- Product Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Email Card -->
                        <a href="{{ route('products.index', ['category' => 'Email']) }}"
                            class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                            <div class="flex flex-col items-center text-center">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                                    class="h-20 w-20 font-medium text-primary" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M480 201.667c0-14.933-7.469-28.803-20.271-36.266L256 64 52.271 165.401C40.531 172.864 32 186.734 32 201.667v203.666C32 428.802 51.197 448 74.666 448h362.668C460.803 448 480 428.802 480 405.333V201.667zM256 304L84.631 192 256 106.667 427.369 192 256 304z">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-bold text-primary mb-2">Email</h3>
                                <p class="text-md px-2 text-center font-medium text-gray-500">Gmail, yahoo mail, hot mail... và
                                    nhiều hơn thế nữa</p>
                            </div>
                        </a>

                        <!-- Phần mềm Card -->
                        <a href="{{ route('products.index', ['category' => 'Phần mềm']) }}"
                            class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                            <div class="flex flex-col items-center text-center">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20"
                                    aria-hidden="true" class="h-20 w-20 font-medium text-primary" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.464 3.162A2 2 0 0 1 6.28 2h7.44a2 2 0 0 1 1.816 1.162l1.154 2.5c.067.145.115.291.145.438A3.508 3.508 0 0 0 16 6H4c-.288 0-.568.035-.835.1.03-.147.078-.293.145-.438l1.154-2.5Z">
                                    </path>
                                    <path fill-rule="evenodd"
                                        d="M2 9.5a2 2 0 0 1 2-2h12a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Zm13.24 0a.75.75 0 0 1 .75-.75H16a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75h-.01a.75.75 0 0 1-.75-.75V9.5Zm-2.25-.75a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75H13a.75.75 0 0 0 .75-.75V9.5a.75.75 0 0 0-.75-.75h-.01ZM2 15a2 2 0 0 1 2-2h12a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Zm13.24 0a.75.75 0 0 1 .75-.75H16a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75h-.01a.75.75 0 0 1-.75-.75V15Zm-2.25-.75a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75H13a.75.75 0 0 0 .75-.75V15a.75.75 0 0 0-.75-.75h-.01Z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-bold text-primary mb-2">Phần mềm</h3>
                                <p class="text-md px-2 text-center font-medium text-gray-500">Các phần mềm chuyên dụng cho kiếm
                                    tiền online từ những coder uy tín</p>
                            </div>
                        </a>

                        <!-- Tài khoản Card -->
                        <a href="{{ route('products.index', ['category' => 'Tài khoản']) }}"
                            class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                            <div class="flex flex-col items-center text-center">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                                    class="h-20 w-20 font-medium text-primary" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-bold text-primary mb-2">Tài khoản</h3>
                                <p class="text-md px-2 text-center font-medium text-gray-500">Fb, BM, key window, kaspersky....
                                </p>
                            </div>
                        </a>

                        <!-- Khác Card -->
                        <a href="{{ route('products.index', ['category' => 'Khác']) }}"
                            class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                            <div class="flex flex-col items-center text-center">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                                    class="h-20 w-20 font-medium text-primary" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M512 48v288c0 26.5-21.5 48-48 48h-48V176c0-44.1-35.9-80-80-80H128V48c0-26.5 21.5-48 48-48h288c26.5 0 48 21.5 48 48zM384 176v288c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V176c0-26.5 21.5-48 48-48h288c26.5 0 48 21.5 48 48zm-68 28c0-6.6-5.4-12-12-12H76c-6.6 0-12 5.4-12 12v52h252v-52z">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-bold text-primary mb-2">Khác</h3>
                                <p class="text-md px-2 text-center font-medium text-gray-500">Các sản phẩm số khác</p>
                            </div>
                        </a>
                </div>
            </div>

            <!-- Danh sách Dịch vụ -->
            <div>
                <!-- Section Title -->
                <div class="relative flex items-center justify-center mb-8">
                    <div class="relative bg-white px-6">
                        <h2 class="text-center text-xl font-medium text-emerald-500">-- DANH SÁCH DỊCH VỤ --</h2>
                    </div>
                </div>

                <!-- Service Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Tăng tương tác Card -->
                    <a href="{{ route('products.index', ['category' => 'Tăng tương tác']) }}"
                        class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                        <div class="flex flex-col items-center text-center">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20"
                                aria-hidden="true" class="h-20 w-20 font-medium text-primary" height="1em"
                                width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.25 2A2.25 2.25 0 0 0 2 4.25v11.5A2.25 2.25 0 0 0 4.25 18h11.5A2.25 2.25 0 0 0 18 15.75V4.25A2.25 2.25 0 0 0 15.75 2H4.25ZM15 5.75a.75.75 0 0 0-1.5 0v8.5a.75.75 0 0 0 1.5 0v-8.5Zm-8.5 6a.75.75 0 0 0-1.5 0v2.5a.75.75 0 0 0 1.5 0v-2.5ZM8.584 9a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5a.75.75 0 0 1 .75-.75Zm3.58-1.25a.75.75 0 0 0-1.5 0v6.5a.75.75 0 0 0 1.5 0v-6.5Z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="text-lg font-bold text-primary mb-2">Tăng tương tác</h3>
                            <p class="text-md px-2 text-center font-medium text-gray-500">Tăng like, view.share, comment...
                                cho sản phẩm của bạn</p>
                        </div>
                    </a>

                    <!-- Dịch vụ phần mềm Card -->
                    <a href="{{ route('products.index', ['category' => 'Dịch vụ phần mềm']) }}"
                        class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                        <div class="flex flex-col items-center text-center">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20"
                                aria-hidden="true" class="h-20 w-20 font-medium text-primary" height="1em"
                                width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.464 3.162A2 2 0 0 1 6.28 2h7.44a2 2 0 0 1 1.816 1.162l1.154 2.5c.067.145.115.291.145.438A3.508 3.508 0 0 0 16 6H4c-.288 0-.568.035-.835.1.03-.147.078-.293.145-.438l1.154-2.5Z">
                                </path>
                                <path fill-rule="evenodd"
                                    d="M2 9.5a2 2 0 0 1 2-2h12a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Zm13.24 0a.75.75 0 0 1 .75-.75H16a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75h-.01a.75.75 0 0 1-.75-.75V9.5Zm-2.25-.75a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75H13a.75.75 0 0 0 .75-.75V9.5a.75.75 0 0 0-.75-.75h-.01ZM2 15a2 2 0 0 1 2-2h12a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Zm13.24 0a.75.75 0 0 1 .75-.75H16a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75h-.01a.75.75 0 0 1-.75-.75V15Zm-2.25-.75a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75H13a.75.75 0 0 0 .75-.75V15a.75.75 0 0 0-.75-.75h-.01Z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="text-lg font-bold text-primary mb-2">Dịch vụ phần mềm</h3>
                            <p class="text-md px-2 text-center font-medium text-gray-500">Dịch vụ code tool MMO, đồ họa,
                                video... và các dịch vụ liên quan</p>
                        </div>
                    </a>

                    <!-- Blockchain Card -->
                    <a href="{{ route('products.index', ['category' => 'Blockchain']) }}"
                        class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                        <div class="flex flex-col items-center text-center">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20"
                                aria-hidden="true" class="h-20 w-20 font-medium text-primary" height="1em"
                                width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="m3.196 12.87-.825.483a.75.75 0 0 0 0 1.294l7.25 4.25a.75.75 0 0 0 .758 0l7.25-4.25a.75.75 0 0 0 0-1.294l-.825-.484-5.666 3.322a2.25 2.25 0 0 1-2.276 0L3.196 12.87Z">
                                </path>
                                <path
                                    d="m3.196 8.87-.825.483a.75.75 0 0 0 0 1.294l7.25 4.25a.75.75 0 0 0 .758 0l7.25-4.25a.75.75 0 0 0 0-1.294l-.825-.484-5.666 3.322a2.25 2.25 0 0 1-2.276 0L3.196 8.87Z">
                                </path>
                                <path
                                    d="M10.38 1.103a.75.75 0 0 0-.76 0l-7.25 4.25a.75.75 0 0 0 0 1.294l7.25 4.25a.75.75 0 0 0 .76 0l7.25-4.25a.75.75 0 0 0 0-1.294l-7.25-4.25Z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-bold text-primary mb-2">Blockchain</h3>
                            <p class="text-md px-2 text-center font-medium text-gray-500">Dịch vụ tiền ảo, NFT, coinlist...
                                và các dịch vụ blockchain khác</p>
                        </div>
                    </a>

                    <!-- Dịch vụ khác Card -->
                    <a href="{{ route('products.index', ['category' => 'Dịch vụ khác']) }}"
                        class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                        <div class="flex flex-col items-center text-center">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20"
                                aria-hidden="true" class="h-20 w-20 font-medium text-primary" height="1em"
                                width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.606 12.97a.75.75 0 0 1-.134 1.051 2.494 2.494 0 0 0-.93 2.437 2.494 2.494 0 0 0 2.437-.93.75.75 0 1 1 1.186.918 3.995 3.995 0 0 1-4.482 1.332.75.75 0 0 1-.461-.461 3.994 3.994 0 0 1 1.332-4.482.75.75 0 0 1 1.052.134Z"
                                    clip-rule="evenodd"></path>
                                <path fill-rule="evenodd"
                                    d="M5.752 12A13.07 13.07 0 0 0 8 14.248v4.002c0 .414.336.75.75.75a5 5 0 0 0 4.797-6.414 12.984 12.984 0 0 0 5.45-10.848.75.75 0 0 0-.735-.735 12.984 12.984 0 0 0-10.849 5.45A5 5 0 0 0 1 11.25c.001.414.337.75.751.75h4.002ZM13 9a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="text-lg font-bold text-primary mb-2">Dịch vụ khác</h3>
                            <p class="text-md px-2 text-center font-medium text-gray-500">Các dịch vụ MMO phổ biến khác
                                hiện nay</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Shortcuts Carousel -->
    @php
        // Sample data - Replace with $shortcutsProducts from controller
        $shortcutsProducts = $shortcutsProducts ?? [
                                [
                                    'id' => 1,
                                    'title' => 'dsadsa',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ Facebook',
                                    'price_min' => 2222,
                                    'price_max' => 2222,
                                ],
                                [
                                    'id' => 2,
                                    'title' => 'Tut add thẻ trên via tỉ lệ 99% thành công',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ khác',
                                    'price_min' => 100000,
                                    'price_max' => 100000,
                                ],
                                [
                                    'id' => 3,
                                    'title' => 'Tut kháng 792 tỉ lệ về 90%',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ khác',
                                    'price_min' => 100000,
                                    'price_max' => 100000,
                                ],
                                [
                                    'id' => 4,
                                    'title' => 'Khắc phục lỗi khi chấp nhận tk vào BM',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ khác',
                                    'price_min' => 100000,
                                    'price_max' => 100000,
                                ],
                                [
                                    'id' => 5,
                                    'title' => 'Cập nhật cách fix lỗi FB mới',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ khác',
                                    'price_min' => 100000,
                                    'price_max' => 100000,
                                ],
                                [
                                    'id' => 6,
                                    'title' => 'Hướng dẫn tạo tài khoản Facebook Business',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ Facebook',
                                    'price_min' => 150000,
                                    'price_max' => 200000,
                                ],
                                [
                                    'id' => 7,
                                    'title' => 'Tối ưu quảng cáo Facebook hiệu quả',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ Facebook',
                                    'price_min' => 200000,
                                    'price_max' => 250000,
                                ],
                                [
                                    'id' => 8,
                                    'title' => 'Cách tăng like và tương tác tự nhiên',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Tăng tương tác',
                                    'price_min' => 50000,
                                    'price_max' => 100000,
                                ],
                                [
                                    'id' => 9,
                                    'title' => 'Tool tự động đăng bài trên Facebook',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Phần mềm',
                                    'subcategory' => 'Tool Facebook',
                                    'price_min' => 300000,
                                    'price_max' => 500000,
                                ],
                                [
                                    'id' => 10,
                                    'title' => 'Dịch vụ chạy quảng cáo Facebook uy tín',
                                    'image' => 'images/placeholder.jpg',
                                    'rating' => 5,
                                    'reviews' => 0,
                                    'category' => 'Dịch vụ',
                                    'subcategory' => 'Dịch vụ Facebook',
                                    'price_min' => 1000000,
                                    'price_max' => 2000000,
                                ],
                            ];
                        @endphp
    <x-product-carousel 
        title="Lối tắt" 
        :products="$shortcutsProducts" 
        :carouselId="'shortcutsCarousel'"
    />

    <!-- About Platform Component -->
    <div class="w-full py-12 bg-white">
        <!-- Container (giống search component) -->
        <div class="w-full max-w-[calc(80rem-180px)] mx-auto px-2 md:px-0">
            <div class="bg-white rounded-xl border border-primary p-6 md:p-8 relative pb-20">
                <!-- Title -->
                <h2 class="text-2xl md:text-3xl font-bold text-primary text-center mb-6">
                    Tạp hóa MMO - Chuyên trang thương mại điện tử sản phẩm số - Phục vụ cộng đồng MMO (Kiếm tiền online)
                </h2>

                <!-- Short Content (Always Visible) -->
                <div class="mb-6">
                    <p class="text-gray-700 leading-relaxed">
                        Một sản phẩm ra đời với mục đích thuận tiện và an toàn hơn trong các giao dịch mua bán sản phẩm số.
                        Như các bạn đã biết, tình trạng lừa đảo trên mạng xã hội kéo dài bao nhiêu năm nay, mặc dù đã có rất
                        nhiều giải pháp từ cộng đồng như là trung gian hay bảo hiểm, nhưng vẫn rất nhiều người dùng lựa chọn
                        mua bán nhanh gọc bước kiểm tra, hay trung gian, từ đó tạo cơ hội cho s.c.a.m.m.e.r
                        hoạt động. Ở Taphoazalo, bạn sẽ có 1 trải nghiệm mua hàng yên tâm hơn n mà bỏ qua cárất nhiều, chúng tôi sẽ giữ
                        tiền người bán 3 ngày, kiểm tra toàn bộ sản phẩm bán ra có trùng với người khác hay không, nhằm mục
                        đích tạo ra một nơi giao dịch mà người dùng có thể tin tưởng, một trang mà người bán có thể yên tâm
                        đặt kho hàng, và cạnh tranh sòng phẳng.
                    </p>
                </div>

                <!-- Expandable Content -->
                <div id="expandableContent" class="overflow-hidden transition-all duration-500 ease-in-out"
                    style="max-height: 0; opacity: 0;">
                    <!-- Section 1: Features -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-primary mb-4">Các tính năng trên trang</h3>
                        <div class="space-y-4">
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Check trùng sản phẩm bán ra</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Trang đảm bảo không có sản phẩm trùng lặp được bán ra. Hệ thống sẽ kiểm tra từng sản
                                    phẩm để đảm bảo sản phẩm đó chưa từng được bán cho bất kỳ ai khác trên trang, và sản
                                    phẩm bạn mua không thể được bán lại bởi người khác.
                                </p>
                            </div>
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Nạp tiền và thanh toán tự động</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Bạn có thể nạp tiền bằng cách sử dụng cú pháp đúng, số dư của bạn sẽ được cập nhật trong
                                    vòng 1-5 phút. Tất cả các quy trình thanh toán và giao hàng đều được thực hiện tự động
                                    và tức thì.
                                </p>
                            </div>
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Giữ tiền đơn hàng 3 ngày</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Sau khi mua hàng, trạng thái đơn hàng sẽ là "Tạm giữ tiền 3 ngày", đủ thời gian để bạn
                                    kiểm tra và đổi mật khẩu sản phẩm. Nếu có bất kỳ vấn đề nào, bạn nên nhanh chóng sử dụng
                                    tính năng "Khiếu nại".
                                </p>
                            </div>
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Tính năng dành cho cộng tác viên (Reseller)
                                </h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Nếu bạn quan tâm đến tính năng này, vui lòng đọc thêm trong phần "FAQs - Câu hỏi thường
                                    gặp".
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Products & Services -->
                    <div>
                        <h3 class="text-xl font-bold text-primary mb-4">Các mặt hàng đang kinh doanh tại Tạp Hóa MMO</h3>
                        <div class="space-y-4">
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Mua bán email</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Bao gồm mua bán Gmail, Outlook mail, domain và các sản phẩm liên quan khác.
                                </p>
                            </div>
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Mua bán phần mềm MMO</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Các phần mềm chuyên dụng cho kiếm tiền online như phần mềm YouTube, phần mềm chạy
                                    Facebook, phần mềm PTC, phần mềm PTU, phần mềm Gmail...
                                </p>
                            </div>
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Mua bán tài khoản</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Mua bán tài khoản Facebook, Twitter, Zalo, Instagram...
                                </p>
                            </div>
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Các sản phẩm số khác</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Bao gồm VPS, key Windows, key antivirus và bất kỳ sản phẩm số nào khác không vi phạm
                                    chính sách của trang.
                                </p>
                            </div>
                            <div class="border-l-4 border-primary pl-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Các dịch vụ tăng tương tác</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Như like, comment, share, cũng như các dịch vụ phần mềm nói chung, dịch vụ blockchain và
                                    các dịch vụ số khác.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggle Button - Fixed at bottom center -->
                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2">
                    <button id="toggleContentBtn" onclick="toggleContent()"
                        class="w-max absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-2 rounded bg-primary px-4 text-sm text-white data-[hover]:bg-primary/80 data-[hover]:data-[active]:bg-primary/90">
                        Nhiều hơn
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Toggle expandable content
        function toggleContent() {
            const content = document.getElementById('expandableContent');
            const toggleBtn = document.getElementById('toggleContentBtn');
            const isExpanded = content.style.maxHeight && content.style.maxHeight !== '0px';

            if (!isExpanded) {
                // Expand
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                toggleBtn.textContent = 'Ít hơn';
            } else {
                // Collapse
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                toggleBtn.textContent = 'Nhiều hơn';
            }
        }
    </script>
@endpush

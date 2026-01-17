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
        <div class="relative w-full max-w-[calc(80rem-180px)] mx-auto min-h-[400px] flex items-center justify-center">
            <div class="absolute inset-0 search-component bg-cover bg-center bg-no-repeat rounded-lg"
                style="background-image: url('{{ asset('images/d/background.jpg') }}');">
                <div class="absolute inset-0 bg-black/30 rounded-lg"></div>
            </div>

            <div class="relative z-10 w-full max-w-4xl flex flex-col items-center justify-center px-2 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row gap-4 mb-4 w-full">
                    <div class="flex-1 relative">
                        <select id="productDropdown"
                            class="w-full py-3.5 px-4 rounded-lg border-2 border-white bg-white/95 backdrop-blur-sm text-gray-700 text-sm font-medium cursor-pointer appearance-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-md hover:shadow-lg">
                            <option value="product">Sản phẩm</option>
                            <option value="service">Dịch vụ</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>

                    <div class="flex-1 relative">
                        <select id="categoryDropdown"
                            class="w-full py-3.5 px-4 rounded-lg border-2 border-white bg-white/95 backdrop-blur-sm text-gray-700 text-sm font-medium cursor-pointer appearance-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-md hover:shadow-lg">
                            <option value="">Chọn danh mục</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center my-4 w-full">
                    <span class="text-white font-bold text-xl">Hoặc</span>
                </div>

                <div class="flex flex-col gap-3 w-full">
                    <div class="w-full relative">
                        <input type="text" id="searchInput" placeholder="Tìm gian hàng và người bán"
                            class="w-full py-3.5 px-4 pr-12 rounded-lg border-2 border-white bg-white/95 backdrop-blur-sm text-gray-700 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-md hover:shadow-lg">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>

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

    <!-- Product and Service Categories -->
    <div class="w-full bg-white">
        <div class="w-full max-w-[calc(80rem-180px)] mx-auto px-2 md:px-0">
            <div class="mb-16">
                <div class="relative flex items-center justify-center mb-8">
                    <div class="relative bg-white px-6">
                        <h2 class="text-center text-xl font-medium text-emerald-500">-- DANH SÁCH SẢN PHẨM --</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                            class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                            <div class="flex flex-col items-center text-center">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                                    class="h-20 w-20 font-medium text-primary" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M480 201.667c0-14.933-7.469-28.803-20.271-36.266L256 64 52.271 165.401C40.531 172.864 32 186.734 32 201.667v203.666C32 428.802 51.197 448 74.666 448h362.668C460.803 448 480 428.802 480 405.333V201.667zM256 304L84.631 192 256 106.667 427.369 192 256 304z">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-bold text-primary mb-2">{{ $category->name }}</h3>
                                <p class="text-md px-2 text-center font-medium text-gray-500">
                                    {{ $category->description ?? 'Danh mục sản phẩm' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="relative flex items-center justify-center mb-8">
                    <div class="relative bg-white px-6">
                        <h2 class="text-center text-xl font-medium text-emerald-500">-- DANH SÁCH DỊCH VỤ --</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <a href="{{ route('products.index', ['category' => 'Tăng tương tác']) }}"
                        class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                        <div class="flex flex-col items-center text-center">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20"
                                aria-hidden="true" class="h-20 w-20 font-medium text-primary" height="1em" width="1em"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.25 2A2.25 2.25 0 0 0 2 4.25v11.5A2.25 2.25 0 0 0 4.25 18h11.5A2.25 2.25 0 0 0 18 15.75V4.25A2.25 2.25 0 0 0 15.75 2H4.25ZM15 5.75a.75.75 0 0 0-1.5 0v8.5a.75.75 0 0 0 1.5 0v-8.5Zm-8.5 6a.75.75 0 0 0-1.5 0v2.5a.75.75 0 0 0 1.5 0v-2.5ZM8.584 9a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5a.75.75 0 0 1 .75-.75Zm3.58-1.25a.75.75 0 0 0-1.5 0v6.5a.75.75 0 0 0 1.5 0v-6.5Z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="text-lg font-bold text-primary mb-2">Tăng tương tác</h3>
                            <p class="text-md px-2 text-center font-medium text-gray-500">Tăng like, view.share, comment...
                                cho sản phẩm của bạn</p>
                        </div>
                    </a>

                    <a href="{{ route('products.index', ['category' => 'Dịch vụ phần mềm']) }}"
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
                            <h3 class="text-lg font-bold text-primary mb-2">Dịch vụ phần mềm</h3>
                            <p class="text-md px-2 text-center font-medium text-gray-500">Dịch vụ code tool MMO, đồ họa,
                                video... và các dịch vụ liên quan</p>
                        </div>
                    </a>

                    <a href="{{ route('products.index', ['category' => 'Blockchain']) }}"
                        class="bg-white rounded-lg border border-primary p-6 hover:shadow-lg transition-shadow cursor-pointer block">
                        <div class="flex flex-col items-center text-center">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20"
                                aria-hidden="true" class="h-20 w-20 font-medium text-primary" height="1em" width="1em"
                                xmlns="http://www.w3.org/2000/svg">
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

    @if (isset($shortcutsProducts) && count($shortcutsProducts) > 0)
        <div class="w-full bg-white pt-12">
            <div class="w-full max-w-[calc(80rem-180px)] mx-auto px-2 md:px-0">
                <x-product-carousel title="Lối tắt" :products="$shortcutsProducts" :carouselId="'shortcutsCarousel'" />
            </div>
        </div>
    @endif

    <div class="w-full py-12 bg-white">
        <div class="w-full max-w-[calc(80rem-180px)] mx-auto px-2 md:px-0">
            <div class="bg-white rounded-xl border border-primary p-6 md:p-8 relative pb-20">
                <h2 class="text-2xl md:text-3xl font-bold text-primary text-center mb-6">
                    Tạp hóa MMO - Chuyên trang thương mại điện tử sản phẩm số - Phục vụ cộng đồng MMO (Kiếm tiền online)
                </h2>

                <div class="mb-6">
                    <p class="text-gray-700 leading-relaxed">
                        Một sản phẩm ra đời với mục đích thuận tiện và an toàn hơn trong các giao dịch mua bán sản phẩm số.
                        Như các bạn đã biết, tình trạng lừa đảo trên mạng xã hội kéo dài bao nhiêu năm nay, mặc dù đã có rất
                        nhiều giải pháp từ cộng đồng như là trung gian hay bảo hiểm, nhưng vẫn rất nhiều người dùng lựa chọn
                        mua bán nhanh gọc bước kiểm tra, hay trung gian, từ đó tạo cơ hội cho s.c.a.m.m.e.r
                        hoạt động. Ở Taphoazalo, bạn sẽ có 1 trải nghiệm mua hàng yên tâm hơn n mà bỏ qua cárất nhiều, chúng
                        tôi sẽ giữ
                        tiền người bán 3 ngày, kiểm tra toàn bộ sản phẩm bán ra có trùng với người khác hay không, nhằm mục
                        đích tạo ra một nơi giao dịch mà người dùng có thể tin tưởng, một trang mà người bán có thể yên tâm
                        đặt kho hàng, và cạnh tranh sòng phẳng.
                    </p>
                </div>

                <div id="expandableContent" class="overflow-hidden transition-all duration-500 ease-in-out"
                    style="max-height: 0; opacity: 0;">
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

        /* Style cho category và subcategory trong dropdown */
        #categoryDropdown option[data-type="category"] {
            font-weight: 600;
            color: #111827;
            background-color: #f9fafb;
        }

        #categoryDropdown option[data-type="subcategory"] {
            font-weight: 400;
            color: #6b7280;
            padding-left: 20px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchButton = document.getElementById('searchButton');
            const searchInput = document.getElementById('searchInput');
            const productDropdown = document.getElementById('productDropdown');
            const categoryDropdown = document.getElementById('categoryDropdown');
            let categoriesData = [];

            function loadCategories() {
                categoryDropdown.innerHTML = '<option value="">Chọn danh mục</option>';

                fetch('{{ route('api.categories') }}')
                    .then(response => response.json())
                    .then(data => {
                        categoriesData = data;
                        data.forEach(category => {
                            const categoryOption = document.createElement('option');
                            categoryOption.value = `cat-${category.slug}`;
                            categoryOption.textContent = category.name;
                            categoryOption.setAttribute('data-type', 'category');
                            categoryOption.setAttribute('data-category-slug', category.slug);
                            categoryDropdown.appendChild(categoryOption);

                            category.subcategories.forEach(subcategory => {
                                const subOption = document.createElement('option');
                                subOption.value = `subcat-${subcategory.slug}`;
                                subOption.textContent = `  └─ ${subcategory.name}`;
                                subOption.setAttribute('data-type', 'subcategory');
                                subOption.setAttribute('data-subcategory-slug', subcategory
                                    .slug);
                                subOption.setAttribute('data-category-slug', subcategory
                                    .category_slug);
                                categoryDropdown.appendChild(subOption);
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading categories:', error);
                    });
            }

            productDropdown.addEventListener('change', function() {
                const selectedValue = this.value;

                if (selectedValue === 'product') {
                    loadCategories();
                } else if (selectedValue === 'service') {
                    categoryDropdown.innerHTML = '<option value="">Chọn danh mục</option>';
                }
            });

            if (productDropdown.value === 'product') {
                loadCategories();
            }

            searchButton.addEventListener('click', function() {
                const searchQuery = searchInput.value.trim();
                const productType = productDropdown.value;
                const categoryValue = categoryDropdown.value;

                const params = new URLSearchParams();
                if (searchQuery) params.append('q', searchQuery);

                if (categoryValue) {
                    const selectedOption = categoryDropdown.options[categoryDropdown.selectedIndex];
                    const type = selectedOption.getAttribute('data-type');

                    if (type === 'category') {
                        const categorySlug = selectedOption.getAttribute('data-category-slug');
                        params.append('category', categorySlug);
                    } else if (type === 'subcategory') {
                        const categorySlug = selectedOption.getAttribute('data-category-slug');
                        const subcategorySlug = selectedOption.getAttribute('data-subcategory-slug');
                        params.append('category', categorySlug);
                        params.append('filters[]', subcategorySlug);
                    }
                }

                const routeName = productType === 'product' ? '{{ route('products.index') }}' :
                    '{{ route('services.index') }}';
                const searchUrl = routeName + '?' + params.toString();
                window.location.href = searchUrl;
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchButton.click();
                }
            });
        });
    </script>

    <script>
        function toggleContent() {
            const content = document.getElementById('expandableContent');
            const toggleBtn = document.getElementById('toggleContentBtn');
            const isExpanded = content.style.maxHeight && content.style.maxHeight !== '0px';

            if (!isExpanded) {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                toggleBtn.textContent = 'Ít hơn';
            } else {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                toggleBtn.textContent = 'Nhiều hơn';
            }
        }
    </script>
@endpush

@extends('client.layouts.app')

@section('title', 'Gian hàng ' . $category . ' - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gray-50 min-h-screen py-6">
        <!-- Container -->
        <div class="w-full px-3 sm:px-4 md:px-6 lg:px-8">


            <!-- Main Content: Sidebar + Products -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left Sidebar - Filter -->
                <aside class="w-full lg:w-56 flex-shrink-0">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sticky-sidebar">
                        <!-- Filter Section -->
                        <div class="mb-5">
                            <h2 class="text-base font-bold text-gray-900 mb-2">Bộ lọc</h2>
                            <p class="text-xs text-gray-500 mb-3">Chọn 1 hoặc nhiều sản phẩm</p>

                            <form id="filterForm" class="space-y-2 mb-4">
                                @foreach ($filterOptions as $option)
                                    <label
                                        class="flex items-center cursor-pointer group hover:bg-gray-50 px-2 py-1.5 rounded-md transition-colors">
                                        <input type="checkbox" name="filters[]" value="{{ $option->slug }}"
                                            class="w-3.5 h-3.5 text-primary border-gray-300 rounded focus:ring-primary focus:ring-1"
                                            {{ in_array($option->slug, $filters) ? 'checked' : '' }}>
                                        <span
                                            class="ml-2.5 text-xs text-gray-700 group-hover:text-primary transition-colors">{{ $option->name }}</span>
                                    </label>
                                @endforeach
                            </form>

                            <button type="button" onclick="applyFilters()"
                                class="w-full py-2 bg-primary hover:bg-primary-6 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1">
                                Tìm kiếm
                            </button>
                        </div>

                        <!-- Reference Articles Section -->
                        <div class="border-t border-gray-100 pt-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Bài viết tham khảo</h3>
                            <div class="space-y-3">
                                <a href="#" class="flex gap-2.5 group hover:bg-gray-50 p-2 rounded-lg transition-all">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                        <img src="{{ asset('images/placeholder.jpg') }}" alt="Article"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-xs font-medium text-gray-800 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                            Cách hạn chế die acc Zalo và...
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Right Content - Product Grid -->
                <div class="flex-1">

                    <!-- Header Section -->
                    <div class="mb-4">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">Gian hàng {{ $category }}</h1>
                        <p class="text-xs text-gray-500">Tổng {{ number_format($totalProducts, 0, ',', '.') }} gian hàng</p>
                    </div>

                    <!-- Policy Banner -->
                    <div class="bg-primary/5 border border-primary/10 rounded-lg p-3 mb-4">
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Đối với gian hàng không trùng, chúng tôi cam kết sản phẩm được bán ra 1 lần duy nhất trên hệ
                            thống, tránh trường hợp sản phẩm đó được bán nhiều lần.
                        </p>
                    </div>

                    <!-- Sort Tabs -->
                    <div class="flex flex-wrap items-center gap-3 mb-5">
                        <div class="flex gap-1 bg-white rounded-lg p-1 border border-gray-200 shadow-sm">
                            <button onclick="changeSort('popular')"
                                class="sort-tab px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $sortBy === 'popular' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                                Phổ biến
                            </button>
                            <button onclick="changeSort('price_asc')"
                                class="sort-tab px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $sortBy === 'price_asc' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                                Giá tăng dần
                            </button>
                            <button onclick="changeSort('price_desc')"
                                class="sort-tab px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $sortBy === 'price_desc' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                                Giá giảm dần
                            </button>
                        </div>
                        <p class="text-xs text-gray-500">Liên hệ với chúng tôi để đặt quảng cáo!</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        @forelse($products as $product)
                            <div
                                class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 group">
                                <!-- Product Label -->
                                <div
                                    class="absolute top-2 left-2 z-10 bg-primary text-white text-[10px] font-semibold px-2 py-0.5 rounded-md shadow-sm">
                                    Sản phẩm
                                </div>

                                <!-- Product Image -->
                                <div class="relative h-40 bg-gray-100 overflow-hidden">
                                    <img src="{{ asset($product['image'] ?? 'images/placeholder.jpg') }}"
                                        alt="{{ $product['title'] }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'300\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                                </div>

                                <!-- Product Info -->
                                <div class="p-3">
                                    <!-- Title -->
                                    <h3
                                        class="text-sm font-semibold text-gray-900 mb-1.5 line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                                        {{ $product['title'] }}
                                    </h3>

                                    <!-- Rating -->
                                    <div class="flex items-center gap-1 mb-1.5">
                                        <div class="flex text-yellow-400">
                                            @php
                                                $fullStars = floor($product['rating']);
                                                $hasHalfStar = $product['rating'] - $fullStars >= 0.5;
                                            @endphp
                                            @for ($i = 0; $i < $fullStars; $i++)
                                                <i class="fas fa-star text-[10px]"></i>
                                            @endfor
                                            @if ($hasHalfStar)
                                                <i class="fas fa-star-half-alt text-[10px]"></i>
                                            @endif
                                            @for ($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++)
                                                <i class="far fa-star text-[10px]"></i>
                                            @endfor
                                        </div>
                                        <span
                                            class="text-[10px] text-gray-500 ml-0.5">{{ number_format($product['rating'], 1) }}</span>
                                    </div>

                                    <!-- Stats -->
                                    <div class="text-[10px] text-gray-500 mb-2">
                                        <div>{{ number_format($product['reviews_count'], 0, ',', '.') }} Reviews | Đã bán:
                                            {{ number_format($product['sold_count'], 0, ',', '.') }} | Khiếu nại:
                                            {{ number_format($product['complaint_rate'], 1) }}%</div>
                                    </div>

                                    <!-- Seller & Category -->
                                    <div class="space-y-1 mb-2">
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Người bán:</span>
                                            <span class="text-primary ml-1">{{ $product['seller'] }}</span>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Sản phẩm:</span>
                                            <span class="text-primary ml-1">{{ $product['category'] }}</span>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-xs text-gray-500 mb-2 line-clamp-2 leading-relaxed">
                                        {{ $product['description'] }}
                                    </p>

                                    <!-- Stock -->
                                    <div class="text-xs text-gray-600 mb-2.5">
                                        <span class="font-medium">Tồn kho:</span>
                                        <span class="{{ $product['stock'] > 0 ? 'text-green-600' : 'text-red-600' }} ml-1">
                                            {{ number_format($product['stock'], 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <!-- Price -->
                                    <div class="flex items-center justify-between pt-2.5 border-t border-gray-100">
                                        <span class="text-lg font-bold text-primary">
                                            {{ number_format($product['price'], 0, ',', '.') }}₫
                                        </span>
                                        <a href="{{ route('products.show', $product['slug'] ?? $product['id']) }}"
                                            class="px-3 py-1.5 bg-primary hover:bg-primary-6 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 inline-block text-center">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-12">
                                <p class="text-gray-500 text-lg">Không tìm thấy sản phẩm nào</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($pagination->hasPages())
                        <div class="mt-6">
                            {{ $pagination->appends(request()->query())->links('components.paginate') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function changeSort(sortBy) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortBy);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function applyFilters() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const filters = formData.getAll('filters[]');

            const url = new URL(window.location.href);
            
            url.searchParams.delete('page');
            
            const keysToDelete = [];
            url.searchParams.forEach((value, key) => {
                if (key.startsWith('filters')) {
                    keysToDelete.push(key);
                }
            });
            keysToDelete.forEach(key => {
                url.searchParams.delete(key);
            });
            
            if (filters.length > 0) {
                filters.forEach(filter => {
                    url.searchParams.append('filters[]', filter);
                });
            }

            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.group');
            
            let animatedCount = 0;
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                        entry.target.classList.add('animated');
                        const delay = Math.min(animatedCount * 30, 150);
                        
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, delay);
                        
                        animatedCount++;
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.05, 
                rootMargin: '50px' 
            });

            productCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px)'; 
                card.style.transition = 'opacity 0.25s ease, transform 0.25s ease'; 
                observer.observe(card);
            });
        });
    </script>
@endpush

@extends('client.layouts.app')

@section('title', $seller['name'] . ' - ' . config('app.name'))

@section('content')
<div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
    <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <!-- Seller Header Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6 animate-fadeIn">
            <div class="bg-gradient-to-r from-primary/10 via-primary/5 to-transparent p-6">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="w-24 h-24 md:w-28 md:h-28 rounded-full overflow-hidden border-4 border-white shadow-xl">
                            @if($seller['avatar'])
                                <img src="{{ $seller['avatar'] }}" alt="{{ $seller['name'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary to-primary-6 flex items-center justify-center">
                                    <span class="text-3xl md:text-4xl font-bold text-white">{{ strtoupper(substr($seller['name'], 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        @if($seller['is_online'])
                            <div class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 rounded-full border-3 border-white shadow-md animate-pulse"></div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-1 text-center md:text-left">
                        <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">{{ $seller['name'] }}</h1>
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium {{ $seller['is_online'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }} rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full {{ $seller['is_online'] ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                {{ $seller['is_online'] ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">@ {{ $seller['name'] }}</p>

                        <!-- Stats -->
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 md:gap-6">
                            <div class="text-center md:text-left">
                                <p class="text-lg font-bold text-primary">{{ number_format($stats['total_products']) }}</p>
                                <p class="text-xs text-gray-500">Sản phẩm</p>
                            </div>
                            <div class="text-center md:text-left">
                                <p class="text-lg font-bold text-green-600">{{ number_format($stats['total_sold']) }}</p>
                                <p class="text-xs text-gray-500">Đã bán</p>
                            </div>
                            <div class="text-center md:text-left">
                                <p class="text-lg font-bold text-yellow-600">{{ number_format($stats['rating'], 1) }}</p>
                                <p class="text-xs text-gray-500">Đánh giá</p>
                            </div>
                            <div class="text-center md:text-left">
                                <p class="text-lg font-bold text-gray-700">{{ $seller['joined_date'] }}</p>
                                <p class="text-xs text-gray-500">Tham gia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="flex flex-col gap-2">
                        @if($seller['facebook_url'])
                            <a href="{{ $seller['facebook_url'] }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all">
                                <i class="fab fa-facebook-f"></i>
                                Facebook
                            </a>
                        @endif
                        @if($seller['telegram_username'])
                            <a href="https://t.me/{{ ltrim($seller['telegram_username'], '@') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-medium rounded-lg hover:bg-sky-600 transition-all">
                                <i class="fab fa-telegram-plane"></i>
                                Telegram
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden animate-fadeIn" style="animation-delay: 0.1s">
            <div class="p-4 md:p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-store text-primary"></i>
                    Sản phẩm của {{ $seller['name'] }}
                    <span class="text-sm font-normal text-gray-500">({{ number_format($stats['total_products']) }} sản phẩm)</span>
                </h2>
            </div>

            @if($products->count() > 0)
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($products as $product)
                            <a href="{{ route('products.show', $product['slug']) }}" class="group bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                <!-- Product Image -->
                                <div class="relative aspect-square overflow-hidden">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';">
                                    @if($product['is_featured'])
                                        <div class="absolute top-2 left-2 px-2 py-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-[10px] font-bold rounded-full shadow-md">
                                            <i class="fas fa-star mr-1"></i>Nổi bật
                                        </div>
                                    @endif
                                    @if($product['stock'] <= 0)
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                            <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">Hết hàng</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="p-3">
                                    <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                                        {{ $product['title'] }}
                                    </h3>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            @if($product['price_min'] == $product['price_max'])
                                                <p class="text-sm font-bold text-primary">{{ number_format($product['price_min']) }}₫</p>
                                            @else
                                                <p class="text-sm font-bold text-primary">{{ number_format($product['price_min']) }}₫ - {{ number_format($product['price_max']) }}₫</p>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-gray-500">Đã bán {{ number_format($product['sold_count']) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($pagination->hasPages())
                        <div class="mt-6 flex justify-center">
                            {{ $pagination->appends(request()->query())->links('components.paginate') }}
                        </div>
                    @endif
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-box-open text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500">Chưa có sản phẩm nào</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }

    .border-3 {
        border-width: 3px;
    }
</style>
@endpush



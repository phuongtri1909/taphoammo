@extends('client.layouts.app')

@section('title', $product['title'] . ' - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-8 md:py-12">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8 transform transition-all duration-300 hover:shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 p-6 md:p-8 lg:p-10">
                    <div class="relative group">
                        <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl overflow-hidden shadow-2xl relative">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent z-10"></div>
                            <img 
                                src="{{ asset($product['image'] ?? 'images/placeholder.jpg') }}" 
                                alt="{{ $product['title'] }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'600\' height=\'600\'%3E%3Crect fill=\'%23f3f4f6\' width=\'600\' height=\'600\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'20\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E';"
                            >
                            <div class="absolute top-4 right-4 z-20">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/95 backdrop-blur-sm text-primary text-xs font-bold rounded-full shadow-lg">
                                    <i class="fas fa-tag"></i>
                                    Sản phẩm
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col justify-between">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 leading-tight animate-fadeIn">
                                {{ $product['title'] }}
                            </h1>

                            <div class="flex flex-wrap items-center gap-4 mb-6">
                                <div class="flex items-center gap-2 bg-yellow-50 px-3 py-1.5 rounded-full">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="fas fa-star text-sm"></i>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 ml-1">
                                        {{ number_format($product['rating'] ?? 5, 1) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs font-medium text-gray-600">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-comment-alt text-primary"></i>
                                        {{ number_format($product['reviews_count'], 0, ',', '.') }} đánh giá
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-shopping-cart text-primary"></i>
                                        Đã bán: {{ number_format($product['sold_count'], 0, ',', '.') }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-exclamation-triangle text-orange-500"></i>
                                        Khiếu nại: {{ number_format($product['complaint_rate'], 1) }}%
                                    </span>
                                </div>
                            </div>

                            <div class="mb-6 p-4 bg-gradient-to-r from-primary/5 to-primary-10 rounded-xl">
                                <p class="text-sm font-medium text-gray-800">{{ $product['name'] ?? 'Sản phẩm chất lượng cao' }}</p>
                            </div>

                            <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-store text-primary text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 truncate">Người bán</p>
                                            <p class="text-xs font-semibold text-gray-900 truncate">{{ $product['seller'] }}</p>
                                        </div>
                                        @if ($product['seller_online'] ?? false)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-tags text-blue-600 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 truncate">Danh mục</p>
                                            <p class="text-xs font-semibold text-primary truncate">{{ $product['category'] }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 {{ ($product['stock'] ?? 0) > 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-box {{ ($product['stock'] ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }} text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 truncate">Tồn kho</p>
                                            <p class="text-xs font-semibold {{ ($product['stock'] ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }} truncate">
                                                {{ number_format($product['stock'] ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8 p-6 bg-gradient-to-br from-primary/10 via-primary/5 to-transparent rounded-2xl">
                                <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">Giá sản phẩm</p>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-4xl md:text-5xl font-extrabold bg-gradient-to-r from-primary to-primary-6 bg-clip-text text-transparent">
                                        {{ number_format($product['price'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-xl font-semibold text-gray-600">vnđ</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Số lượng</label>
                                <div class="flex items-center gap-3">
                                    <button
                                        onclick="decreaseQuantity()"
                                        class="w-12 h-12 flex items-center justify-center bg-white border-2 border-gray-300 rounded-xl hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 group">
                                        <i class="fas fa-minus text-gray-600 group-hover:text-white transition-colors"></i>
                                    </button>
                                    <input
                                        type="number"
                                        id="quantity"
                                        value="1"
                                        min="1"
                                        max="{{ $product['stock'] ?? 1 }}"
                                        class="w-20 h-12 text-center text-lg font-bold border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                    >
                                    <button
                                        onclick="increaseQuantity()"
                                        class="w-12 h-12 flex items-center justify-center bg-white border-2 border-gray-300 rounded-xl hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 group">
                                        <i class="fas fa-plus text-gray-600 group-hover:text-white transition-colors"></i>
                                    </button>
                                </div>
                            </div>

                            <button
                                onclick="handleBuy()"
                                class="w-full py-4 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-primary/50 relative overflow-hidden group">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    <i class="fas fa-shopping-cart"></i>
                                    Mua hàng ngay
                                </span>
                                <div class="absolute inset-0 bg-white/20 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <div class="flex">
                        <button
                            onclick="switchTab('description')"
                            id="tab-description"
                            class="tab-button flex-1 px-6 py-5 text-base font-bold text-center border-b-3 border-primary text-primary bg-gradient-to-b from-primary/10 to-transparent transition-all duration-300 relative">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="fas fa-file-alt"></i>
                                Mô tả sản phẩm
                            </span>
                        </button>
                        <button
                            onclick="switchTab('reviews')"
                            id="tab-reviews"
                            class="tab-button flex-1 px-6 py-5 text-base font-bold text-center border-b-3 border-transparent text-gray-600 hover:text-primary hover:bg-gray-50 transition-all duration-300 relative">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="fas fa-star"></i>
                                Đánh giá ({{ count($product['reviews'] ?? []) }})
                            </span>
                        </button>
                    </div>
                </div>

                <div class="p-6 md:p-8 lg:p-10">
                    <div id="content-description" class="tab-content">
                        <div class="prose prose-lg max-w-none">
                            <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border border-gray-200 shadow-sm">
                                <p class="text-base text-gray-800 leading-relaxed whitespace-pre-line">
                                    {{ $product['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div id="content-reviews" class="tab-content hidden">
                        <div class="space-y-6">
                            @forelse($product['reviews'] ?? [] as $review)
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-6 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-base font-bold text-gray-900">{{ $review['user_name'] ?? 'User' }}</span>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex text-yellow-400">
                                                        @for ($i = 0; $i < ($review['rating'] ?? 5); $i++)
                                                            <i class="fas fa-star text-sm"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-3 font-medium">{{ $review['created_at'] ?? 'Recently' }}</p>
                                            <p class="text-sm text-gray-700 leading-relaxed">{{ $review['comment'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-star text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Chưa có đánh giá nào</p>
                                    <p class="text-sm text-gray-400 mt-2">Hãy là người đầu tiên đánh giá sản phẩm này</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($similarProducts) && count($similarProducts) > 0)
        <x-product-carousel 
            title="Sản phẩm tương tự" 
            :products="$similarProducts" 
            carouselId="similarProductsCarousel"
        />
        @endif
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

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }

    .tab-content {
        animation: fadeIn 0.4s ease-out;
    }

    .tab-button {
        position: relative;
    }

    .tab-button::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        transition: all 0.3s ease;
    }

    .tab-button.border-primary::after {
        background: linear-gradient(to right, var(--color-primary), var(--color-primary-6));
    }

    .border-b-3 {
        border-bottom-width: 3px;
    }
</style>
@endpush

@push('scripts')
<script>
    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.getAttribute('max'));
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }

    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-primary', 'text-primary', 'bg-gradient-to-b', 'from-primary/10', 'to-transparent');
            button.classList.add('border-transparent', 'text-gray-600');
        });

        const targetContent = document.getElementById(`content-${tab}`);
        targetContent.classList.remove('hidden');
        targetContent.style.animation = 'fadeIn 0.4s ease-out';

        const activeTab = document.getElementById(`tab-${tab}`);
        activeTab.classList.remove('border-transparent', 'text-gray-600');
        activeTab.classList.add('border-primary', 'text-primary', 'bg-gradient-to-b', 'from-primary/10', 'to-transparent');
    }

    function handleBuy() {
        const quantity = document.getElementById('quantity').value;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.opacity = '0';
            content.style.transform = 'translateY(10px)';
            content.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            observer.observe(content);
        });
    });
</script>
@endpush


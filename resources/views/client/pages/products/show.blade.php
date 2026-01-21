@extends('client.layouts.app')

@section('title', $product['name'] . ' - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-8 md:py-12">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div
                class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8 transform transition-all duration-300 hover:shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 p-6 md:p-8 lg:p-10">
                    <div class="relative group">
                        <div
                            class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl overflow-hidden shadow-2xl relative">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent z-10"></div>
                            <img src="{{ asset($product['image'] ?? 'images/placeholder.jpg') }}" alt="{{ $product['name'] }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'600\' height=\'600\'%3E%3Crect fill=\'%23f3f4f6\' width=\'600\' height=\'600\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'20\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                            <div class="absolute top-4 right-4 z-20">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/95 backdrop-blur-sm text-primary text-xs font-bold rounded-full shadow-lg">
                                    <i class="fas fa-tag"></i>
                                    Sản phẩm
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight animate-fadeIn flex-1">
                                    {{ $product['name'] }}
                                </h2>
                                @auth
                                    @php
                                        $productModel = \App\Models\Product::where('slug', $product['slug'])->first();
                                        $isFavorited = $productModel ? auth()->user()->hasFavorited($productModel) : false;
                                    @endphp
                                    <button onclick="toggleFavorite('product', '{{ $product['slug'] }}', this)"
                                        class="ml-4 w-10 h-10 flex items-center justify-center rounded-full {{ $isFavorited ? 'bg-red-50' : 'bg-gray-100' }} hover:bg-red-50 transition-all duration-200 favorite-btn flex-shrink-0"
                                        data-type="product"
                                        data-slug="{{ $product['slug'] }}"
                                        data-favorited="{{ $isFavorited ? 'true' : 'false' }}">
                                        <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart text-red-500 text-lg"></i>
                                    </button>
                                @endauth
                            </div>

                            <div class="flex flex-wrap items-center gap-4 mb-6">
                                <div class="flex items-center gap-2 bg-yellow-50 px-3 py-1.5 rounded-full">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($product['rating']))
                                                <i class="fas fa-star text-sm"></i>
                                            @elseif($i - 0.5 <= $product['rating'])
                                                <i class="fas fa-star-half-alt text-sm"></i>
                                            @else
                                                <i class="far fa-star text-sm text-gray-300"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 ml-1">
                                        {{ number_format($product['rating'], 1) }}
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

                            <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-store text-primary text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 truncate">Người bán</p>
                                            @if($product['seller'])
                                                <a href="{{ route('seller.profile', $product['seller']) }}" class="text-xs font-semibold text-primary hover:text-primary-6 truncate block transition-colors">
                                                    {{ $product['seller'] }}
                                                    <i class="fas fa-external-link-alt text-[10px] ml-1"></i>
                                                </a>
                                            @else
                                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $product['seller'] }}</p>
                                            @endif
                                        </div>
                                        @if ($product['seller_online'] ?? false)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-tags text-blue-600 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 truncate">Danh mục</p>
                                            <p class="text-xs font-semibold text-primary truncate">
                                                {{ $product['category'] }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 {{ ($product['stock'] ?? 0) > 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i
                                                class="fas fa-box {{ ($product['stock'] ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }} text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 truncate">Tồn kho</p>
                                            <p
                                                class="text-xs font-semibold {{ ($product['stock'] ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }} truncate">
                                                {{ number_format($product['stock'] ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @php
                                $hasVariants = isset($variants) && count($variants) > 0;
                                $firstAvailableVariant = null;
                                if ($hasVariants) {
                                    $firstAvailableVariant =
                                        collect($variants)->firstWhere('is_available', true) ?? ($variants[0] ?? null);
                                }
                            @endphp
                            @if ($hasVariants)
                                <div>
                                    <label class="block text-xs font-bold text-gray-900 mb-2" id="variantLabel">
                                        Chọn biến
                                        thể{{ $firstAvailableVariant ? ': ' . $firstAvailableVariant['name'] . ' (Còn ' . number_format($firstAvailableVariant['stock_quantity'], 0, ',', '.') . ')' : '' }}
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2" id="variantList">
                                        @foreach ($variants as $index => $variant)
                                            @php
                                                $isFirstAvailable =
                                                    $firstAvailableVariant &&
                                                    $firstAvailableVariant['id'] === $variant['id'];
                                            @endphp
                                            <button type="button" data-variant-slug="{{ $variant['slug'] }}"
                                                data-variant-name="{{ $variant['name'] }}"
                                                data-variant-price="{{ $variant['price'] }}"
                                                data-variant-stock="{{ $variant['stock_quantity'] }}"
                                                class="variant-option {{ $isFirstAvailable ? 'selected' : '' }} {{ !$variant['is_available'] ? 'disabled' : '' }} flex flex-col items-center justify-center p-2 border-2 rounded-lg transition-all duration-200 {{ $variant['is_available'] ? 'border-gray-300 hover:border-primary hover:bg-primary/5 cursor-pointer' : 'border-gray-200 bg-gray-50 cursor-not-allowed opacity-60' }} relative"
                                                {{ !$variant['is_available'] ? 'disabled' : '' }}
                                                onclick="selectVariant(this, {{ json_encode($variant['slug']) }}, {{ json_encode($variant['name']) }}, {{ $variant['price'] }}, {{ $variant['stock_quantity'] }})">
                                                @if ($variant['is_available'])
                                                    <div
                                                        class="variant-check absolute top-1 right-1 {{ $isFirstAvailable ? '' : 'hidden' }}">
                                                        <i class="fas fa-check-circle text-primary text-sm"></i>
                                                    </div>
                                                @endif
                                                <span
                                                    class="text-xs font-semibold text-gray-900 text-center truncate w-full mb-1">{{ $variant['name'] }}</span>
                                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                    <span
                                                        class="text-sm font-bold text-primary whitespace-nowrap">{{ number_format($variant['price'], 0, ',', '.') }}₫</span>
                                                    @if ($variant['is_available'])
                                                        <span class="text-[10px] text-gray-500 whitespace-nowrap">(Còn
                                                            {{ number_format($variant['stock_quantity'], 0, ',', '.') }})</span>
                                                    @else
                                                        <span
                                                            class="text-[10px] text-red-500 font-medium whitespace-nowrap">(Hết
                                                            hàng)</span>
                                                    @endif
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <input type="hidden" id="productSlug" value="{{ $product['slug'] }}">
                            <input type="hidden" id="selectedVariantSlug"
                                value="{{ $firstAvailableVariant['slug'] ?? '' }}">
                            <input type="hidden" id="selectedVariantPrice"
                                value="{{ $firstAvailableVariant['price'] ?? $product['price'] }}">
                            <input type="hidden" id="selectedVariantStock"
                                value="{{ $firstAvailableVariant['stock_quantity'] ?? ($product['stock'] ?? 1) }}">

                            <div class="mb-3 p-3 bg-gradient-to-br from-primary/10 via-primary/5 to-transparent rounded-lg">
                                <p class="text-[10px] font-semibold text-gray-600 mb-1 uppercase tracking-wider">Giá sản
                                    phẩm</p>
                                <div class="flex items-baseline gap-1.5">
                                    <span id="productPrice"
                                        class="text-xl md:text-2xl font-extrabold bg-gradient-to-r from-primary to-primary-6 bg-clip-text text-transparent">
                                        @if (isset($product['price_min']) && isset($product['price_max']) && $product['price_max'] != $product['price_min'])
                                            {{ number_format($product['price_min'], 0, ',', '.') }} -
                                            {{ number_format($product['price_max'], 0, ',', '.') }}
                                        @else
                                            {{ number_format($product['price'], 0, ',', '.') }}
                                        @endif
                                    </span>
                                    <span class="text-xs font-semibold text-gray-600">vnđ</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-900 mb-2">Số lượng</label>
                                <div class="flex items-center gap-2">
                                    <button onclick="decreaseQuantity()"
                                        class="w-9 h-9 flex items-center justify-center bg-white border-2 border-gray-300 rounded-lg hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 group">
                                        <i
                                            class="fas fa-minus text-xs text-gray-600 group-hover:text-white transition-colors"></i>
                                    </button>
                                    <input type="number" id="quantity" value="1" min="1"
                                        max="{{ isset($variants) && count($variants) > 0 ? collect($variants)->firstWhere('is_available', true)['stock_quantity'] ?? ($variants[0]['stock_quantity'] ?? $product['stock']) : $product['stock'] ?? 1 }}"
                                        class="w-16 h-9 text-center text-sm font-bold border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                    <button onclick="increaseQuantity()"
                                        class="w-9 h-9 flex items-center justify-center bg-white border-2 border-gray-300 rounded-lg hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 group">
                                        <i
                                            class="fas fa-plus text-xs text-gray-600 group-hover:text-white transition-colors"></i>
                                    </button>
                                </div>
                            </div>

                            @php
                                $availableStock =
                                    isset($variants) && count($variants) > 0
                                        ? collect($variants)->firstWhere('is_available', true)['stock_quantity'] ??
                                            ($variants[0]['stock_quantity'] ?? $product['stock'])
                                        : $product['stock'] ?? 0;
                            @endphp
                            <button id="buyButton" onclick="handleBuy()"
                                class="w-full py-3 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-bold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-primary/50 relative overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                {{ $availableStock > 0 ? '' : 'disabled' }}>
                                <span class="relative z-10 flex items-center justify-center gap-1.5">
                                    <i class="fas fa-shopping-cart text-sm"></i>
                                    <span
                                        id="buyButtonText">{{ $availableStock > 0 ? 'Mua hàng ngay' : 'Hết hàng' }}</span>
                                </span>
                                <div
                                    class="absolute inset-0 bg-white/20 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <div class="flex">
                        <button onclick="switchTab('description')" id="tab-description"
                            class="tab-button flex-1 px-6 py-5 text-base font-bold text-center border-b-3 border-primary text-primary bg-gradient-to-b from-primary/10 to-transparent transition-all duration-300 relative">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="fas fa-file-alt"></i>
                                Mô tả sản phẩm
                            </span>
                        </button>
                        <button onclick="switchTab('reviews')" id="tab-reviews"
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
                            <div
                                class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border border-gray-200 shadow-sm">
                                <p class="text-base text-gray-800 leading-relaxed whitespace-pre-line">
                                    {{ $product['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div id="content-reviews" class="tab-content hidden">
                        {{-- Review Summary --}}
                        @if(count($product['reviews'] ?? []) > 0)
                            <div class="mb-6 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl border border-yellow-200">
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gray-900">{{ number_format($product['rating'], 1) }}</div>
                                        <div class="flex text-yellow-400 mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($product['rating']))
                                                    <i class="fas fa-star text-sm"></i>
                                                @elseif($i - 0.5 <= $product['rating'])
                                                    <i class="fas fa-star-half-alt text-sm"></i>
                                                @else
                                                    <i class="far fa-star text-sm"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $product['reviews_count'] }} đánh giá</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            @forelse($product['reviews'] ?? [] as $review)
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary-6 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                <span class="text-sm font-bold text-gray-900">{{ $review['user_name'] ?? 'Người dùng' }}</span>
                                                <div class="flex text-yellow-400">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="{{ $i <= $review['rating'] ? 'fas' : 'far' }} fa-star text-xs"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-gray-500 mb-2">
                                                {{ $review['created_at_diff'] ?? $review['created_at'] ?? 'Gần đây' }}
                                            </p>
                                            @if($review['content'])
                                                <p class="text-sm text-gray-700 leading-relaxed">{{ $review['content'] }}</p>
                                            @else
                                                <p class="text-sm text-gray-400 italic">Không có nội dung đánh giá</p>
                                            @endif
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
    </div>

    @if (isset($similarProducts) && count($similarProducts) > 0)
        <div class="w-full bg-white">
            <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <x-product-carousel title="Sản phẩm tương tự" :products="$similarProducts" carouselId="similarProductsCarousel" />
            </div>
        </div>
    @endif
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

        .variant-option.selected {
            border-color: var(--color-primary, #02B3B9);
            background-color: rgba(2, 179, 185, 0.1);
        }

        .variant-option.selected .variant-check {
            display: block !important;
        }

        .variant-option:not(.disabled):hover {
            border-color: var(--color-primary, #02B3B9);
            background-color: rgba(2, 179, 185, 0.05);
        }

        .variant-option.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleFavorite(type, slug, button) {
            fetch('{{ route('favorites.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    type: type,
                    slug: slug
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = button.querySelector('i');
                    if (data.is_favorited) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.classList.remove('bg-gray-100');
                        button.classList.add('bg-red-50');
                        button.setAttribute('data-favorited', 'true');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        button.classList.remove('bg-red-50');
                        button.classList.add('bg-gray-100');
                        button.setAttribute('data-favorited', 'false');
                    }
                    if (typeof showToast !== 'undefined') {
                        showToast(data.message, 'success');
                    }
                } else {
                    if (data.message && data.message.includes('đăng nhập')) {
                        window.location.href = '{{ route('sign-in') }}';
                    } else if (typeof showToast !== 'undefined') {
                        showToast(data.message || 'Có lỗi xảy ra', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showToast !== 'undefined') {
                    showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            });
        }

        @php
            $firstAvailableVariant = null;
            if (isset($variants) && count($variants) > 0) {
                $firstAvailableVariant = collect($variants)->firstWhere('is_available', true) ?? ($variants[0] ?? null);
            }
        @endphp
        let selectedVariantSlug = '{{ $firstAvailableVariant['slug'] ?? '' }}';
        let selectedVariantPrice = {{ $firstAvailableVariant['price'] ?? $product['price'] }};
        let selectedVariantStock = {{ $firstAvailableVariant['stock_quantity'] ?? ($product['stock'] ?? 1) }};

        function selectVariant(button, variantSlug, variantName, price, stock) {
            document.querySelectorAll('.variant-option').forEach(option => {
                option.classList.remove('selected');
                const checkIcon = option.querySelector('.variant-check');
                if (checkIcon) {
                    checkIcon.classList.add('hidden');
                }
            });

            button.classList.add('selected');
            const selectedCheckIcon = button.querySelector('.variant-check');
            if (selectedCheckIcon) {
                selectedCheckIcon.classList.remove('hidden');
            }

            const variantLabel = document.getElementById('variantLabel');
            if (variantLabel) {
                variantLabel.textContent =
                    `Chọn biến thể: ${variantName} (Còn ${new Intl.NumberFormat('vi-VN').format(stock)})`;
            }

            document.getElementById('selectedVariantSlug').value = variantSlug;
            document.getElementById('selectedVariantPrice').value = price;
            document.getElementById('selectedVariantStock').value = stock;

            selectedVariantSlug = variantSlug;
            selectedVariantPrice = price;
            selectedVariantStock = stock;

            const quantityInput = document.getElementById('quantity');
            quantityInput.setAttribute('max', stock);
            const currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity > stock) {
                quantityInput.value = stock;
            }

            updatePriceWithQuantity();

            updateBuyButton(stock);
        }

        function updatePrice(price) {
            const priceElement = document.getElementById('productPrice');
            priceElement.textContent = new Intl.NumberFormat('vi-VN').format(price);
        }

        function updatePriceWithQuantity() {
            const quantityInput = document.getElementById('quantity');
            const quantity = parseInt(quantityInput.value) || 1;
            const totalPrice = selectedVariantPrice * quantity;
            updatePrice(totalPrice);
        }

        function updateBuyButton(stock) {
            const buyButton = document.getElementById('buyButton');
            const buyButtonText = document.getElementById('buyButtonText');

            if (stock > 0) {
                buyButton.disabled = false;
                buyButtonText.textContent = 'Mua hàng ngay';
            } else {
                buyButton.disabled = true;
                buyButtonText.textContent = 'Hết hàng';
            }
        }

        function increaseQuantity() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.getAttribute('max'));
            const current = parseInt(input.value);
            if (current < max) {
                input.value = current + 1;
                updatePriceWithQuantity();
            }
        }

        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            const current = parseInt(input.value);
            if (current > 1) {
                input.value = current - 1;
                updatePriceWithQuantity();
            }
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-primary', 'text-primary', 'bg-gradient-to-b', 'from-primary/10',
                    'to-transparent');
                button.classList.add('border-transparent', 'text-gray-600');
            });

            const targetContent = document.getElementById(`content-${tab}`);
            targetContent.classList.remove('hidden');
            targetContent.style.animation = 'fadeIn 0.4s ease-out';

            const activeTab = document.getElementById(`tab-${tab}`);
            activeTab.classList.remove('border-transparent', 'text-gray-600');
            activeTab.classList.add('border-primary', 'text-primary', 'bg-gradient-to-b', 'from-primary/10',
                'to-transparent');
        }

        function handleBuy() {
            const quantity = parseInt(document.getElementById('quantity').value);
            const productSlug = document.getElementById('productSlug').value;
            const variantSlugInput = document.getElementById('selectedVariantSlug');
            const variantSlug = variantSlugInput ? variantSlugInput.value : null;
            const stock = parseInt(document.getElementById('selectedVariantStock').value);
            const selectedPrice = parseFloat(document.getElementById('selectedVariantPrice').value) || 0;
            const variantName = document.getElementById('variantLabel')?.textContent?.replace('Chọn biến thể:', '')
                .trim() || '';

            if (stock <= 0) {
                showToast('Sản phẩm đã hết hàng!', 'warning');
                return;
            }

            if (quantity <= 0) {
                showToast('Số lượng phải lớn hơn 0!', 'warning');
                return;
            }

            if (quantity > stock) {
                showToast(`Số lượng không được vượt quá ${stock} sản phẩm còn lại!`, 'warning');
                return;
            }

            const hasVariants = {{ isset($variants) && count($variants) > 0 ? 'true' : 'false' }};
            if (hasVariants && (!variantSlug || variantSlug === '')) {
                showToast('Vui lòng chọn biến thể!', 'warning');
                return;
            }

            if (!productSlug) {
                showToast('Thông tin sản phẩm không hợp lệ!', 'error');
                return;
            }

            const totalPrice = selectedPrice * quantity;
            const productImage = '{{ asset($product['image'] ?? 'images/placeholder.jpg') }}';
            const placeholderSvg =
                'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23f3f4f6\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'16\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E';
            const formattedVariantName = variantName.replace(/\(Còn.*?\)/g, '').trim();

            const confirmMessage = `
            <div class="purchase-confirm-modal" style="text-align: left;">
                <!-- Product Image & Info -->
                <div class="flex gap-4 mb-6 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                    <div class="flex-shrink-0">
                        <img src="${productImage}" alt="{{ $product['name'] }}" 
                             class="w-20 h-20 object-cover rounded-lg shadow-sm border-2 border-white"
                             onerror="this.src='${placeholderSvg}'; this.onerror=null;">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 mb-1 text-lg leading-tight line-clamp-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $product['name'] }}
                        </h4>
                        ${formattedVariantName ? `
                                    <p class="text-sm text-gray-600 flex items-center gap-2 mt-2">
                                        <i class="fas fa-tag text-primary" style="font-size: 11px;"></i>
                                        <span class="font-medium">${formattedVariantName}</span>
                                    </p>
                                ` : ''}
                    </div>
                </div>

                <!-- Order Details -->
                <div class="space-y-3 mb-5">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-box text-primary" style="font-size: 12px;"></i>
                            Số lượng
                        </span>
                        <span class="text-sm font-semibold text-gray-900">${quantity} sản phẩm</span>
                    </div>
                    
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-tag text-primary" style="font-size: 12px;"></i>
                            Đơn giá
                        </span>
                        <span class="text-sm font-semibold text-gray-900">${selectedPrice.toLocaleString('vi-VN')}₫</span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 bg-gradient-to-r from-primary/10 to-primary/5 rounded-lg px-4 mt-4 border-2 border-primary/20">
                        <span class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-wallet text-primary"></i>
                            Tổng tiền
                        </span>
                        <span class="text-xl font-extrabold text-primary" style="background: linear-gradient(135deg, #3b82f6, #2563eb); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            ${totalPrice.toLocaleString('vi-VN')}₫
                        </span>
                    </div>
                </div>

                <!-- Info Notice -->
                <div class="flex items-start gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5" style="font-size: 14px;"></i>
                    <p class="text-xs text-blue-700 leading-relaxed m-0">
                        Tiền sẽ được trừ trực tiếp từ ví của bạn. Bạn có chắc chắn muốn mua sản phẩm này?
                    </p>
                </div>
            </div>
        `;

            Swal.fire({
                title: '<div style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Xác nhận mua hàng</div>',
                html: confirmMessage,
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-check-circle mr-2"></i>Đồng ý mua',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
                width: '540px',
                padding: '2rem',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-gray-200',
                    title: 'mb-0 pb-4',
                    confirmButton: 'px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105',
                    cancelButton: 'px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-200',
                    actions: 'gap-3 mt-4'
                },
                buttonsStyling: true,
                focusConfirm: false,
                allowOutsideClick: true,
                allowEscapeKey: true
            }).then((result) => {
                if (result.isConfirmed) {
                    processPurchase(productSlug, variantSlug, quantity);
                }
            });
        }

        function processPurchase(productSlug, variantSlug, quantity) {
            const buyButton = document.getElementById('buyButton');

            buyButton.disabled = true;
            const originalText = buyButton.querySelector('#buyButtonText').textContent;
            buyButton.querySelector('#buyButtonText').textContent = 'Đang xử lý...';

            fetch('{{ route('products.buy') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        product_slug: productSlug,
                        variant_slug: variantSlug || null,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '<div style="font-size: 24px; font-weight: 700; color: #10b981;">Mua hàng thành công!</div>',
                            html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <p style="font-size: 16px; color: #374151; margin-bottom: 0.5rem; font-weight: 600;">${data.message}</p>
                            <p style="font-size: 14px; color: #6b7280; margin: 0;">Đơn hàng của bạn đã được tạo thành công</p>
                        </div>
                    `,
                            confirmButtonColor: '#10b981',
                            confirmButtonText: '<i class="fas fa-eye mr-2"></i>Xem đơn hàng',
                            width: '480px',
                            padding: '2rem',
                            customClass: {
                                popup: 'rounded-2xl shadow-2xl border border-gray-200',
                                confirmButton: 'px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105'
                            },
                            buttonsStyling: true
                        }).then(() => {
                            if (data.order && data.order.slug) {
                                window.location.href = `/orders/${data.order.slug}`;
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        showToast(data.message || 'Có lỗi xảy ra. Vui lòng thử lại!', 'error');
                        buyButton.disabled = false;
                        buyButton.querySelector('#buyButtonText').textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Đã có lỗi xảy ra. Vui lòng thử lại sau!', 'error');
                    buyButton.disabled = false;
                    buyButton.querySelector('#buyButtonText').textContent = originalText;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.opacity = '0';
                content.style.transform = 'translateY(10px)';
                content.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                observer.observe(content);
            });

            const quantityInput = document.getElementById('quantity');
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    const max = parseInt(this.getAttribute('max'));
                    let value = parseInt(this.value);

                    if (isNaN(value) || value < 1 || value === 0) {
                        this.value = 1;
                        value = 1;
                    }

                    if (value > max) {
                        this.value = max;
                    } else {
                        this.value = value;
                    }

                    updatePriceWithQuantity();
                });

                quantityInput.addEventListener('change', function() {
                    const max = parseInt(this.getAttribute('max'));
                    let value = parseInt(this.value);

                    if (isNaN(value) || value < 1 || value === 0) {
                        this.value = 1;
                        value = 1;
                    }

                    if (value > max) {
                        this.value = max;
                    } else {
                        this.value = value;
                    }

                    updatePriceWithQuantity();
                });

                quantityInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const value = parseInt(pastedText);

                    if (!isNaN(value) && value >= 1) {
                        const max = parseInt(this.getAttribute('max'));
                        this.value = Math.min(value, max);
                        updatePriceWithQuantity();
                    } else {
                        this.value = 1;
                        updatePriceWithQuantity();
                    }
                });

                quantityInput.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.which);
                    if (!/[0-9]/.test(char)) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endpush

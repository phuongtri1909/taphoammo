@extends('client.layouts.app')

@section('title', 'Gian hàng yêu thích - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-4 lg:space-y-5">
                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn">
                    <div class="p-3">
                        <div class="flex items-center justify-between mb-2.5">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-heart text-red-500 text-base"></i>
                                Gian hàng yêu thích
                            </h2>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Tổng: <strong class="text-gray-900">{{ count($favoriteItems) }}</strong> mục</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-1.5 mt-2.5">
                            <a href="{{ route('favorites.index', ['type' => 'all']) }}" 
                                class="px-2.5 py-1 text-[10px] font-medium rounded transition-all duration-200 {{ $currentType === 'all' ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                Tất cả
                            </a>
                            <a href="{{ route('favorites.index', ['type' => 'product']) }}" 
                                class="px-2.5 py-1 text-[10px] font-medium rounded transition-all duration-200 {{ $currentType === 'product' ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                Sản phẩm
                            </a>
                            <a href="{{ route('favorites.index', ['type' => 'service']) }}" 
                                class="px-2.5 py-1 text-[10px] font-medium rounded transition-all duration-200 {{ $currentType === 'service' ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                Dịch vụ
                            </a>
                        </div>
                    </div>
                </div>

                @forelse($favoriteItems as $index => $item)
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn" style="animation-delay: {{ $loop->index * 0.05 }}s">
                        <div class="p-3">
                            <div class="flex flex-col md:flex-row md:items-center gap-3">
                                <div class="w-full md:w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                                    <a href="{{ $item['route'] }}" class="block w-full h-full">
                                        @if ($item['image'])
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-{{ $item['type'] === 'product' ? 'box' : 'concierge-bell' }} text-gray-400 text-2xl"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1.5">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <a href="{{ $item['route'] }}" class="text-sm font-bold text-gray-900 hover:text-primary transition-colors line-clamp-1">
                                                    {{ $item['name'] }}
                                                </a>
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $item['type'] === 'product' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                                    {{ $item['type'] === 'product' ? 'Sản phẩm' : 'Dịch vụ' }}
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 text-[10px] text-gray-600">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    {{ \Carbon\Carbon::parse($item['favorited_at'])->format('d/m/Y H:i') }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($item['favorited_at'])->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button onclick="toggleFavorite('{{ $item['type'] }}', '{{ $item['slug'] }}', this)"
                                        class="w-9 h-9 flex items-center justify-center bg-red-50 hover:bg-red-100 rounded-lg transition-all duration-200 favorite-btn"
                                        data-type="{{ $item['type'] }}"
                                        data-slug="{{ $item['slug'] }}"
                                        data-favorited="true"
                                        title="Bỏ yêu thích">
                                        <i class="fas fa-heart text-red-500 text-sm"></i>
                                    </button>
                                    <a href="{{ $item['route'] }}" 
                                        class="inline-block px-3 py-1.5 text-[10px] font-medium bg-primary hover:bg-primary-6 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                        <i class="fas fa-eye mr-1"></i> Xem
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 animate-fadeIn">
                        <div class="p-8 md:p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-heart text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có mục yêu thích nào</h3>
                            <p class="text-gray-600 mb-6">Bạn chưa yêu thích sản phẩm hoặc dịch vụ nào.</p>
                            <div class="flex gap-3 justify-center">
                                <a href="{{ route('products.index') }}" 
                                    class="inline-block px-6 py-3 bg-primary hover:bg-primary-6 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                    <i class="fas fa-box mr-2"></i> Xem sản phẩm
                                </a>
                                <a href="{{ route('services.index') }}" 
                                    class="inline-block px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                    <i class="fas fa-concierge-bell mr-2"></i> Xem dịch vụ
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse

                @if($favorites->hasPages())
                    <div class="flex justify-center mt-6">
                        {{ $favorites->withQueryString()->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

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
                    if (!data.is_favorited) {
                        button.closest('.bg-white.rounded-lg').remove();
                        if (document.querySelectorAll('.favorite-btn[data-favorited="true"]').length === 0) {
                            setTimeout(() => location.reload(), 500);
                        }
                    }
                    if (typeof showToast !== 'undefined') {
                        showToast(data.message, 'success');
                    }
                } else {
                    if (typeof showToast !== 'undefined') {
                        showToast(data.message, 'error');
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
    </script>
@endpush

@push('styles')
    <style>
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@extends('client.layouts.app')

@section('title', 'Đơn hàng của tôi - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-4 lg:space-y-5">
                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn">
                    <div class="p-3">
                        <div class="flex items-center justify-between mb-2.5">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-shopping-bag text-primary text-base"></i>
                                Đơn hàng của tôi
                            </h2>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Tổng: <strong class="text-gray-900">{{ $orders->total() }}</strong> đơn</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-1.5 mt-2.5">
                            <a href="{{ route('orders.index') }}" 
                                class="px-2.5 py-1 text-[10px] font-medium rounded transition-all duration-200 {{ !request('status') ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                Tất cả
                            </a>
                            @foreach([
                                'paid' => 'Đã thanh toán',
                                'completed' => 'Hoàn thành',
                                'disputed' => 'Đang tranh chấp',
                                'refunded' => 'Đã hoàn tiền',
                                'partial_refunded' => 'Hoàn tiền một phần'
                            ] as $status => $label)
                                <a href="{{ route('orders.index', ['status' => $status]) }}" 
                                    class="px-2.5 py-1 text-[10px] font-medium rounded transition-all duration-200 {{ request('status') === $status ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @forelse($orders as $order)
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn" style="animation-delay: {{ $loop->index * 0.05 }}s">
                        <div class="p-3">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2.5 pb-2.5 border-b border-gray-100">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <h3 class="text-sm font-bold text-gray-900 truncate">Đơn hàng #{{ $order->slug }}</h3>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $order->status->badgeColor() === 'success' ? 'bg-green-100 text-green-700' : ($order->status->badgeColor() === 'warning' ? 'bg-yellow-100 text-yellow-700' : ($order->status->badgeColor() === 'danger' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-[10px] text-gray-600">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="flex items-center gap-1 truncate">
                                            <i class="fas fa-store"></i>
                                            <span class="truncate">{{ $order->seller->full_name }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-base font-bold text-primary mb-1">
                                        {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                    </p>
                                    <a href="{{ route('orders.show', $order->slug) }}" 
                                        class="inline-block px-3 py-1.5 text-[10px] font-medium bg-primary hover:bg-primary-6 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                        <i class="fas fa-eye mr-1"></i> Chi tiết
                                    </a>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                @foreach($order->items->take(2) as $item)
                                    <div class="flex items-center gap-2 p-1.5 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate mb-0.5">
                                                {{ $item->productVariant->product->name }}
                                            </p>
                                            <p class="text-[10px] text-gray-600 truncate">
                                                {{ $item->productVariant->name }} × {{ $item->quantity }}
                                            </p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-xs font-semibold text-gray-900">
                                                {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($order->items->count() > 2)
                                    <p class="text-[10px] text-gray-500 text-center pt-0.5">
                                        + {{ $order->items->count() - 2 }} sản phẩm khác
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 animate-fadeIn">
                        <div class="p-8 md:p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có đơn hàng nào</h3>
                            <p class="text-gray-600 mb-6">Bạn chưa có đơn hàng nào trong danh sách này.</p>
                            <a href="{{ route('products.index') }}" 
                                class="inline-block px-6 py-3 bg-primary hover:bg-primary-6 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                <i class="fas fa-shopping-cart mr-2"></i> Mua sắm ngay
                            </a>
                        </div>
                    </div>
                @endforelse

                @if($orders->hasPages())
                    <div class="flex justify-center mt-6">
                        {{ $orders->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

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


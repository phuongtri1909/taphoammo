@extends('client.layouts.app')

@section('title', 'Lịch sử giao dịch - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-4 lg:space-y-5">
                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn">
                    <div class="p-3">
                        <div class="flex items-center justify-between mb-2.5">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-wallet text-primary text-base"></i>
                                Lịch sử giao dịch
                            </h2>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Tổng: <strong class="text-gray-900">{{ $transactions->total() }}</strong> giao dịch</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-1.5 mt-2.5">
                            <a href="{{ route('profile.transactions') }}" 
                                class="px-2.5 py-1 text-[10px] font-medium rounded transition-all duration-200 {{ !request('status') ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                Tất cả
                            </a>
                            @foreach([
                                'completed' => 'Hoàn thành',
                                'pending' => 'Đang xử lý',
                                'failed' => 'Thất bại',
                            ] as $status => $label)
                                <a href="{{ route('profile.transactions', ['status' => $status]) }}" 
                                    class="px-2.5 py-1 text-[10px] font-medium rounded transition-all duration-200 {{ request('status') === $status ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @forelse($transactions as $transaction)
                    @php
                        $isPositive = in_array($transaction->type->value, ['deposit', 'refund', 'sale', 'commission']);
                        $amountClass = $isPositive ? 'text-green-600' : 'text-red-600';
                        $iconClass = $isPositive ? 'fa-arrow-down' : 'fa-arrow-up';
                        $iconBgClass = $isPositive ? 'bg-green-100' : 'bg-red-100';
                    @endphp
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn" style="animation-delay: {{ $loop->index * 0.05 }}s">
                        <div class="p-3">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2.5 pb-2.5 border-b border-gray-100">
                                <div class="flex items-start gap-2.5 flex-1 min-w-0">
                                    <div class="w-10 h-10 {{ $iconBgClass }} rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas {{ $iconClass }} {{ $isPositive ? 'text-green-600' : 'text-red-600' }} text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-sm font-bold text-gray-900 truncate">{{ $transaction->type->label() }}</h3>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $transaction->status->badgeColor() === 'success' ? 'bg-green-100 text-green-700' : ($transaction->status->badgeColor() === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $transaction->status->label() }}
                                            </span>
                                        </div>
                                        @if($transaction->description)
                                            <p class="text-[10px] text-gray-600 truncate mb-0.5">{{ $transaction->description }}</p>
                                        @endif
                                        <div class="flex flex-wrap items-center gap-2 text-[10px] text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-hashtag"></i>
                                                {{ $transaction->slug }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-calendar-alt"></i>
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 md:ml-4">
                                    <p class="text-base font-bold {{ $amountClass }} mb-1">
                                        {{ $isPositive ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }}₫
                                    </p>
                                    <div class="text-[10px] text-gray-500">
                                        <p>Số dư: {{ number_format($transaction->balance_after, 0, ',', '.') }}₫</p>
                                    </div>
                                </div>
                            </div>

                            @if($transaction->reference_type && $transaction->reference_id)
                                <div class="flex items-center gap-1.5 p-1.5 bg-gray-50 rounded text-[10px]">
                                    <i class="fas fa-link text-gray-400"></i>
                                    <span class="text-gray-600">{{ $transaction->reference_type->label() }}:</span>
                                    @if($transaction->reference_url)
                                        <a href="{{ $transaction->reference_url }}" 
                                            class="text-primary hover:text-primary-6 font-mono hover:underline">
                                            #{{ $transaction->reference_slug ?? $transaction->reference_id }}
                                        </a>
                                    @else
                                        <span class="text-gray-900 font-mono">#{{ $transaction->reference_slug ?? $transaction->reference_id }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden p-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-wallet text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-2">Chưa có giao dịch nào</h3>
                            <p class="text-xs text-gray-600 mb-4">Bạn chưa có giao dịch ví nào trong tài khoản.</p>
                        </div>
                    </div>
                @endforelse

                @if($transactions->hasPages())
                    <div class="flex justify-center mt-6">
                        {{ $transactions->appends(request()->query())->links('components.paginate') }}
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


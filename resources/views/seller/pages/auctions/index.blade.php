@extends('seller.layouts.sidebar')

@section('title', 'Đấu giá banner')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Đấu giá banner</h2>
            </div>
            <div class="card-content">
                <!-- Filter -->
                <form action="{{ route('seller.auctions.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="position" class="custom-select">
                                <option value="">Tất cả vị trí</option>
                                <option value="left" {{ request('position') === 'left' ? 'selected' : '' }}>Bên trái</option>
                                <option value="right" {{ request('position') === 'right' ? 'selected' : '' }}>Bên phải</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('seller.auctions.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($auctions->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-gavel"></i></div>
                        <h4>Hiện tại không có phiên đấu giá nào</h4>
                        <p>Vui lòng quay lại sau khi có phiên đấu giá mới.</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($auctions as $auction)
                            @php
                                $userBid = $userBids[$auction->id] ?? null;
                                $topBid = $auction->topBid();
                                $currentHighest = $auction->current_highest_bid;
                                $timeRemaining = now()->diffInSeconds($auction->end_time, false);
                            @endphp
                            <div class="col-md-6">
                                <div class="product-info-card">
                                    <div class="card-header py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                                <i class="fas fa-gavel"></i> {{ $auction->title }}
                                            </h3>
                                            <span class="badge bg-{{ $auction->banner_position === 'left' ? 'info' : 'warning' }}">
                                                {{ $auction->banner_position === 'left' ? 'Trái' : 'Phải' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        @if($auction->description)
                                            <p class="text-muted mb-2" style="font-size: 0.85rem;">{{ Str::limit($auction->description, 100) }}</p>
                                        @endif
                                        
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Bắt đầu:</small>
                                                <p class="mb-0" style="font-size: 0.85rem;">{{ $auction->start_time->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Kết thúc:</small>
                                                <p class="mb-0" style="font-size: 0.85rem;">{{ $auction->end_time->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Giá khởi điểm:</small>
                                                <p class="mb-0">
                                                    <strong class="text-primary">{{ number_format($auction->starting_price, 0, ',', '.') }}₫</strong>
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Giá cao nhất hiện tại:</small>
                                                <p class="mb-0">
                                                    <strong class="text-success">{{ number_format($currentHighest, 0, ',', '.') }}₫</strong>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-2">
                                            <div class="col-12">
                                                <small class="text-muted">Thời gian hạ banner:</small>
                                                <p class="mb-0" style="font-size: 0.85rem;"><strong>{{ $auction->banner_duration_days }} ngày</strong></p>
                                            </div>
                                        </div>

                                        @if($userBid)
                                            <div class="alert alert-info mb-2" style="font-size: 0.85rem;">
                                                <i class="fas fa-info-circle"></i> 
                                                Bạn đã đấu giá: <strong>{{ number_format($userBid->bid_amount, 0, ',', '.') }}₫</strong>
                                                @if($userBid->status === 'active' && $userBid->isTopBid())
                                                    <span class="badge bg-success ms-2">Đang dẫn đầu</span>
                                                @elseif($userBid->status === 'outbid')
                                                    <span class="badge bg-warning ms-2">Đã bị vượt</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if($timeRemaining > 0)
                                            <div class="mb-2">
                                                <small class="text-muted">Còn lại:</small>
                                                <p class="mb-0" id="countdown-{{ $auction->id }}" data-end-time="{{ $auction->end_time->toISOString() }}">
                                                    <strong class="text-danger">Đang tính...</strong>
                                                </p>
                                            </div>
                                        @else
                                            <div class="alert alert-secondary mb-2">
                                                <i class="fas fa-clock"></i> Phiên đấu giá đã kết thúc
                                            </div>
                                        @endif

                                        <div class="d-grid gap-2">
                                            <a href="{{ route('seller.auctions.show', $auction->slug) }}" class="btn action-button">
                                                <i class="fas fa-eye"></i> Xem chi tiết & Đấu giá
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <a href="{{ route('seller.auctions.history') }}" class="btn back-button mt-3">
        <i class="fas fa-history"></i> Lịch sử đấu giá của tôi
    </a>
@endsection

@push('scripts')
<script>
    // Countdown timers
    document.querySelectorAll('[id^="countdown-"]').forEach(function(element) {
        const endTime = new Date(element.dataset.endTime).getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                element.innerHTML = '<strong class="text-secondary">Đã kết thúc</strong>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let timeString = '';
            if (days > 0) timeString += days + ' ngày ';
            if (hours > 0) timeString += hours + ' giờ ';
            if (minutes > 0) timeString += minutes + ' phút ';
            timeString += seconds + ' giây';

            element.innerHTML = '<strong class="text-danger">' + timeString + '</strong>';
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>
@endpush

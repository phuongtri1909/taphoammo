@extends('seller.layouts.sidebar')

@section('title', 'Lịch sử đấu giá')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Lịch sử đấu giá của tôi</h2>
                <a href="{{ route('seller.auctions.index') }}" class="btn back-button">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-content">
                @if ($bids->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-history"></i></div>
                        <h4>Bạn chưa có lượt đấu giá nào</h4>
                        <p>Tham gia đấu giá để sản phẩm/dịch vụ của bạn được hiển thị trên banner.</p>
                        <a href="{{ route('seller.auctions.index') }}" class="action-button">
                            <i class="fas fa-gavel"></i> Xem phiên đấu giá
                        </a>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-stt">STT</th>
                                    <th class="column-large">Phiên đấu giá</th>
                                    <th class="column-medium">Sản phẩm/Dịch vụ</th>
                                    <th class="column-small text-center">Giá đấu</th>
                                    <th class="column-small text-center">Vị trí</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-medium text-center">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bids as $index => $bid)
                                    <tr>
                                        <td class="text-center">{{ $bids->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $bid->auction->title }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $bid->auction->start_time->format('d/m/Y H:i') }} - 
                                                {{ $bid->auction->end_time->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($bid->biddable_type === 'App\Models\Product')
                                                <a href="{{ route('seller.products.show', $bid->biddable->slug) }}" target="_blank">
                                                    {{ $bid->biddable->name }}
                                                </a>
                                            @else
                                                <a href="{{ route('seller.services.show', $bid->biddable->slug) }}" target="_blank">
                                                    {{ $bid->biddable->name }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-primary">{{ number_format($bid->bid_amount, 0, ',', '.') }}₫</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $bid->auction->banner_position === 'left' ? 'info' : 'warning' }}">
                                                {{ $bid->auction->banner_position === 'left' ? 'Trái' : 'Phải' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($bid->status === 'active')
                                                @if($bid->isTopBid())
                                                    <span class="badge bg-success">Đang dẫn đầu</span>
                                                @else
                                                    <span class="badge bg-info">Đang hoạt động</span>
                                                @endif
                                            @elseif($bid->status === 'won')
                                                <span class="badge bg-primary">Thắng</span>
                                            @elseif($bid->status === 'outbid')
                                                <span class="badge bg-warning">Bị vượt</span>
                                            @else
                                                <span class="badge bg-secondary">Không hợp lệ</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <small>{{ $bid->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $bids->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

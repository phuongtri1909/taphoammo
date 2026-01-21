@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết phiên đấu giá - ' . $auction->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.auctions.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-gavel"></i>
                            Thông tin phiên đấu giá
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Tiêu đề:</small>
                                <p class="mb-0"><strong>{{ $auction->title }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    @if($auction->status === 'pending')
                                        <span class="status-badge bg-secondary text-white">Chờ bắt đầu</span>
                                    @elseif($auction->status === 'active')
                                        <span class="status-badge bg-success text-white">Đang diễn ra</span>
                                    @elseif($auction->status === 'ended')
                                        <span class="status-badge bg-primary text-white">Đã kết thúc</span>
                                    @else
                                        <span class="status-badge bg-danger text-white">Đã hủy</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($auction->description)
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <small class="text-muted">Mô tả:</small>
                                    <p class="mb-0">{{ $auction->description }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Thời gian bắt đầu:</small>
                                <p class="mb-0">{{ $auction->start_time->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Thời gian kết thúc:</small>
                                <p class="mb-0">{{ $auction->end_time->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Giá khởi điểm:</small>
                                <p class="mb-0">
                                    <strong class="text-primary" style="font-size: 1.1rem;">
                                        {{ number_format($auction->starting_price, 0, ',', '.') }}₫
                                    </strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Vị trí banner:</small>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $auction->banner_position === 'left' ? 'info' : 'warning' }}">
                                        {{ $auction->banner_position === 'left' ? 'Bên trái' : 'Bên phải' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Thời gian hạ banner:</small>
                                <p class="mb-0"><strong>{{ $auction->banner_duration_days }} ngày</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Người tạo:</small>
                                <p class="mb-0"><strong>{{ $auction->creator->full_name }}</strong></p>
                            </div>
                        </div>
                        @if($auction->winner)
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Người thắng:</small>
                                    <p class="mb-0"><strong>{{ $auction->winner->full_name }}</strong></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Giá thắng:</small>
                                    <p class="mb-0">
                                        <strong class="text-success" style="font-size: 1.1rem;">
                                            {{ number_format($auction->winning_price, 0, ',', '.') }}₫
                                        </strong>
                                    </p>
                                </div>
                            </div>
                            @if($auction->ended_at)
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted">Thời gian kết thúc:</small>
                                        <p class="mb-0">{{ $auction->ended_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-list"></i>
                            Top đấu giá ({{ $topBids->count() }})
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        @if($topBids->isEmpty())
                            <p class="text-muted mb-0">Chưa có lượt đấu giá nào.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Người đấu giá</th>
                                            <th>Sản phẩm/Dịch vụ</th>
                                            <th class="text-center">Giá đấu</th>
                                            <th class="text-center">Thời gian</th>
                                            <th class="text-center">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topBids as $index => $bid)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $bid->seller->full_name }}</strong></td>
                                                <td>
                                                    @if($bid->biddable_type === 'App\Models\Product')
                                                        <a href="{{ route('admin.products.show', $bid->biddable->slug) }}" target="_blank">
                                                            {{ $bid->biddable->name }}
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.services.show', $bid->biddable->slug) }}" target="_blank">
                                                            {{ $bid->biddable->name }}
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <strong class="text-success">{{ number_format($bid->bid_amount, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td class="text-center">
                                                    <small>{{ $bid->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($bid->status === 'active')
                                                        <span class="badge bg-success">Đang dẫn đầu</span>
                                                    @elseif($bid->status === 'won')
                                                        <span class="badge bg-primary">Thắng</span>
                                                    @elseif($bid->status === 'outbid')
                                                        <span class="badge bg-warning">Bị vượt</span>
                                                    @else
                                                        <span class="badge bg-secondary">Không hợp lệ</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-cog"></i>
                            Thao tác
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="d-grid gap-2">
                            @if($auction->status === 'pending' && now()->lt($auction->start_time))
                                <a href="{{ route('admin.auctions.edit', $auction->slug) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                                <form action="{{ route('admin.auctions.start', $auction->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-play"></i> Bắt đầu phiên đấu giá
                                    </button>
                                </form>
                                <form action="{{ route('admin.auctions.destroy', $auction->slug) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa phiên đấu giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            @endif
                            @if($auction->status === 'active')
                                <form action="{{ route('admin.auctions.cancel', $auction->slug) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy phiên đấu giá này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-times"></i> Hủy phiên đấu giá
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @if($auction->banners->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-image"></i>
                                Banner đã tạo
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            @foreach($auction->banners as $banner)
                                <div class="p-2 bg-gray-50 rounded mb-2">
                                    <p class="mb-1">
                                        <strong>
                                            @if($banner->bannerable_type === 'App\Models\Product')
                                                {{ $banner->bannerable->name }}
                                            @else
                                                {{ $banner->bannerable->name }}
                                            @endif
                                        </strong>
                                    </p>
                                    <p class="mb-1 text-muted" style="font-size: 0.85rem;">
                                        Hiển thị từ: {{ $banner->display_from->format('d/m/Y H:i') }}<br>
                                        Đến: {{ $banner->display_until->format('d/m/Y H:i') }}
                                    </p>
                                    <span class="badge bg-{{ $banner->is_active ? 'success' : 'secondary' }}">
                                        {{ $banner->is_active ? 'Đang hiển thị' : 'Đã hết hạn' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

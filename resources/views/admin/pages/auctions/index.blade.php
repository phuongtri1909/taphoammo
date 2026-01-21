@extends('admin.layouts.sidebar')

@section('title', 'Quản lý đấu giá')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Quản lý đấu giá banner</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.auctions.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Tạo phiên đấu giá
                    </a>
                </div>
            </div>
            <div class="card-content">
                <!-- Filter -->
                <form action="{{ route('admin.auctions.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ bắt đầu</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang diễn ra</option>
                                <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Đã kết thúc</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.auctions.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($auctions->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-gavel"></i></div>
                        <h4>Chưa có phiên đấu giá nào</h4>
                        <p>Tạo phiên đấu giá mới để bắt đầu.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-stt">STT</th>
                                    <th class="column-large">Tiêu đề</th>
                                    <th class="column-medium">Thời gian</th>
                                    <th class="column-small text-center">Giá khởi điểm</th>
                                    <th class="column-small text-center">Giá cao nhất</th>
                                    <th class="column-medium">Người thắng</th>
                                    <th class="column-small text-center">Vị trí</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-medium text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($auctions as $index => $auction)
                                    <tr>
                                        <td class="text-center">{{ $auctions->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $auction->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($auction->description, 50) }}</small>
                                        </td>
                                        <td>
                                            <small>
                                                <strong>Bắt đầu:</strong> {{ $auction->start_time->format('d/m/Y H:i') }}<br>
                                                <strong>Kết thúc:</strong> {{ $auction->end_time->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-primary">{{ number_format($auction->starting_price, 0, ',', '.') }}₫</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($auction->topBid())
                                                <strong class="text-success">{{ number_format($auction->topBid()->bid_amount, 0, ',', '.') }}₫</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($auction->winner)
                                                <strong>{{ $auction->winner->full_name }}</strong>
                                            @else
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $auction->banner_position === 'left' ? 'info' : 'warning' }}">
                                                {{ $auction->banner_position === 'left' ? 'Trái' : 'Phải' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($auction->status === 'pending')
                                                <span class="badge bg-secondary">Chờ bắt đầu</span>
                                            @elseif($auction->status === 'active')
                                                <span class="badge bg-success">Đang diễn ra</span>
                                            @elseif($auction->status === 'ended')
                                                <span class="badge bg-primary">Đã kết thúc</span>
                                            @else
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="{{ route('admin.auctions.show', $auction->slug) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($auction->status === 'pending' && now()->lt($auction->start_time))
                                                    <a href="{{ route('admin.auctions.edit', $auction->slug) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.auctions.destroy', $auction->slug) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa phiên đấu giá này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.auctions.start', $auction->slug) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Bắt đầu">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($auction->status === 'active')
                                                    <form action="{{ route('admin.auctions.cancel', $auction->slug) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn hủy phiên đấu giá này?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hủy">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $auctions->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

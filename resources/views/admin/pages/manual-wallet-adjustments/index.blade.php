@extends('admin.layouts.sidebar')

@section('title', 'Quản lý điều chỉnh ví thủ công')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách điều chỉnh ví thủ công</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.manual-wallet-adjustments.create') }}" class="btn action-button">
                        <i class="fas fa-plus"></i> Tạo điều chỉnh
                    </a>
                </div>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.manual-wallet-adjustments.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="user_search" class="custom-input" placeholder="Tìm kiếm theo tên, email..."
                                value="{{ request('user_search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="custom-select">
                                <option value="">Tất cả loại</option>
                                <option value="add" {{ request('type') === 'add' ? 'selected' : '' }}>Cộng tiền</option>
                                <option value="subtract" {{ request('type') === 'subtract' ? 'selected' : '' }}>Trừ tiền</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="custom-input" value="{{ request('date_from') }}" placeholder="Từ ngày">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="custom-input" value="{{ request('date_to') }}" placeholder="Đến ngày">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.manual-wallet-adjustments.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($adjustments->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4>Chưa có điều chỉnh nào</h4>
                        <p>Tạo điều chỉnh mới để cộng hoặc trừ tiền cho người dùng</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Mã điều chỉnh</th>
                                    <th class="column-medium">Người dùng</th>
                                    <th class="column-small text-center">Loại</th>
                                    <th class="column-small text-center">Số tiền</th>
                                    <th class="column-medium">Lý do</th>
                                    <th class="column-medium">Người xử lý</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($adjustments as $key => $adjustment)
                                    <tr>
                                        <td>{{ $adjustments->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.manual-wallet-adjustments.show', $adjustment->slug) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <a class="item-name color-primary" href="{{ route('admin.manual-wallet-adjustments.show', $adjustment->slug) }}">
                                                        <strong>#{{ $adjustment->slug }}</strong>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <strong>{{ $adjustment->user->full_name }}</strong>
                                                    <small class="text-muted d-block">{{ $adjustment->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $adjustment->adjustment_type->badgeColor() }}">
                                                <i class="fas fa-{{ $adjustment->adjustment_type->icon() }}"></i>
                                                {{ $adjustment->adjustment_type->label() }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="{{ $adjustment->adjustment_type === \App\Enums\ManualAdjustmentType::ADD ? 'text-success' : 'text-danger' }}">
                                                {{ $adjustment->adjustment_type === \App\Enums\ManualAdjustmentType::ADD ? '+' : '-' }}{{ number_format($adjustment->amount, 0, ',', '.') }}₫
                                            </strong>
                                        </td>
                                        <td class="text-start">
                                            <small>{{ Str::limit($adjustment->reason, 50) }}</small>
                                        </td>
                                        <td class="text-start">
                                            <small>{{ $adjustment->processedBy->full_name }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $adjustment->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($adjustments->hasPages())
                        <div class="pagination-wrapper mt-4">
                            {{ $adjustments->appends(request()->query())->links('components.paginate') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush

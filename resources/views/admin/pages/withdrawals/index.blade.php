@extends('admin.layouts.sidebar')

@section('title', 'Quản lý rút tiền')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách rút tiền</h2>
                <div class="d-flex gap-2">
                    @if(isset($counts['pending']) && $counts['pending'] > 0)
                        <span class="badge bg-warning text-dark">Chờ xử lý: {{ $counts['pending'] }}</span>
                    @endif
                    @if(isset($counts['processing']) && $counts['processing'] > 0)
                        <span class="badge bg-primary">Đang xử lý: {{ $counts['processing'] }}</span>
                    @endif
                </div>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm theo mã, STK, tên, email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                @foreach(\App\Enums\WithdrawalStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.withdrawals.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($withdrawals->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h4>Không có yêu cầu rút tiền nào</h4>
                        <p>Tất cả yêu cầu đã được xử lý</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Mã yêu cầu</th>
                                    <th class="column-medium">Người dùng</th>
                                    <th class="column-small text-center">Số tiền</th>
                                    <th class="column-medium">Ngân hàng</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($withdrawals as $key => $withdrawal)
                                    <tr>
                                        <td>{{ $withdrawals->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.withdrawals.show', $withdrawal->slug) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <a class="item-name color-primary" href="{{ route('admin.withdrawals.show', $withdrawal->slug) }}">
                                                        <strong>#{{ $withdrawal->slug }}</strong>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <strong>{{ $withdrawal->user->full_name }}</strong>
                                                    <small class="text-muted d-block">{{ $withdrawal->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-danger">{{ $withdrawal->amount_formatted }}</strong>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <strong>{{ $withdrawal->bank_name }}</strong>
                                                    <small class="text-muted d-block">{{ $withdrawal->bank_account_number }}</small>
                                                    <small class="text-muted d-block">{{ $withdrawal->bank_account_name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $withdrawal->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $withdrawal->status->badgeColor() }}">
                                                {{ $withdrawal->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($withdrawals->hasPages())
                        <div class="pagination-wrapper mt-4">
                            {{ $withdrawals->appends(request()->query())->links('components.paginate') }}
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

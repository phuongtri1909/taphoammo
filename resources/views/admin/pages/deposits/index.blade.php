@extends('admin.layouts.sidebar')

@section('title', 'Quản lý nạp tiền')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách nạp tiền</h2>
                <div class="d-flex gap-2">
                    @if(isset($counts['pending']) && $counts['pending'] > 0)
                        <span class="badge bg-warning text-dark">Chờ xử lý: {{ $counts['pending'] }}</span>
                    @endif
                    @if(isset($counts['success']) && $counts['success'] > 0)
                        <span class="badge bg-success">Thành công: {{ $counts['success'] }}</span>
                    @endif
                    @if(isset($counts['failed']) && $counts['failed'] > 0)
                        <span class="badge bg-danger">Thất bại: {{ $counts['failed'] }}</span>
                    @endif
                </div>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.deposits.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-custom small">Tìm kiếm</label>
                            <input type="text" name="search" class="custom-input" placeholder="Mã, mã GD, STK, tên, email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Trạng thái</label>
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                @foreach(\App\Enums\DepositStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : (request('status') === null && $status->value === \App\Enums\DepositStatus::SUCCESS->value ? 'selected' : '') }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Từ ngày</label>
                            <input type="date" name="date_from" class="custom-input" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Đến ngày</label>
                            <input type="date" name="date_to" class="custom-input" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom small">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn action-button">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('admin.deposits.index') }}" class="btn back-button">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                @if ($deposits->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4>Không có giao dịch nạp tiền nào</h4>
                        <p>Tất cả giao dịch sẽ hiển thị ở đây</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Mã giao dịch</th>
                                    <th class="column-medium">Người dùng</th>
                                    <th class="column-small text-center">Số tiền</th>
                                    <th class="column-medium">Ngân hàng</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($deposits as $key => $deposit)
                                    <tr>
                                        <td>{{ $deposits->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.deposits.show', $deposit->slug) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <a class="item-name color-primary" href="{{ route('admin.deposits.show', $deposit->slug) }}">
                                                        <strong>#{{ $deposit->slug }}</strong>
                                                    </a>
                                                    <small class="text-muted d-block">Mã GD: {{ $deposit->transaction_code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <strong>{{ $deposit->user->full_name }}</strong>
                                                    <small class="text-muted d-block">{{ $deposit->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ $deposit->amount_formatted }}</strong>
                                        </td>
                                        <td class="text-start">
                                            <div>
                                                <strong>{{ $deposit->bank_name }}</strong>
                                                <small class="text-muted d-block">{{ $deposit->bank_account_number }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $deposit->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $deposit->status->badgeColor() }} text-white">
                                                {{ $deposit->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $deposits->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

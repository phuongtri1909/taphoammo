@extends('admin.layouts.sidebar')

@section('title', 'Quản lý người dùng')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách người dùng</h2>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.users.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm theo tên, email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="custom-select">
                                <option value="">Tất cả vai trò</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Người dùng</option>
                                <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>Người bán</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Chưa kích hoạt</option>
                                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Đã khóa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn action-button w-100">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <a href="{{ route('admin.users.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($users->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-users-slash"></i>
                        </div>
                        <h4>Không tìm thấy người dùng</h4>
                        <p>Thử thay đổi bộ lọc để tìm kiếm</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Thông tin</th>
                                    <th class="column-small text-center">Vai trò</th>
                                    <th class="column-small text-center">Số dư</th>
                                    <th class="column-small text-center">Đã nạp</th>
                                    @if(request('role') === 'seller' || !request('role'))
                                        <th class="column-small text-center">Đã rút</th>
                                    @endif
                                    <th class="column-small text-center">Đã mua</th>
                                    <th class="column-small text-center">Ngày tham gia</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($users as $key => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.users.show', $user->full_name) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <span class="item-name">{{ $user->full_name }}</span>
                                                    <small class="text-muted d-block">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->role === 'admin')
                                                <span class="badge bg-danger">Admin</span>
                                            @elseif($user->role === 'seller')
                                                <span class="badge bg-primary">Seller</span>
                                            @else
                                                <span class="badge bg-secondary">User</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                {{ number_format($user->wallet?->balance ?? 0, 0, ',', '.') }}₫
                                            </strong>
                                        </td>
                                        <td>
                                            @php
                                                $totalDeposited = \App\Models\Deposit::where('user_id', $user->id)
                                                    ->where('status', \App\Enums\DepositStatus::SUCCESS)
                                                    ->sum('amount');
                                            @endphp
                                            <span class="text-info">
                                                {{ number_format($totalDeposited, 0, ',', '.') }}₫
                                            </span>
                                        </td>
                                        @if(request('role') === 'seller' || !request('role'))
                                            <td>
                                                @if($user->role === 'seller')
                                                    @php
                                                        $totalWithdrawn = \App\Models\Withdrawal::where('user_id', $user->id)
                                                            ->where('status', \App\Enums\WithdrawalStatus::COMPLETED)
                                                            ->sum('amount');
                                                    @endphp
                                                    <span class="text-warning">
                                                        {{ number_format($totalWithdrawn, 0, ',', '.') }}₫
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @php
                                                $totalSpent = \App\Models\Order::where('buyer_id', $user->id)->sum('total_amount')
                                                    + \App\Models\ServiceOrder::where('buyer_id', $user->id)->sum('total_amount');
                                            @endphp
                                            <span class="text-primary">
                                                {{ number_format($totalSpent, 0, ',', '.') }}₫
                                            </span>
                                        </td>
                                        <td>
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if($user->is_seller_banned)
                                                <span class="status-badge banned">
                                                    <i class="fas fa-ban"></i> Đã khóa
                                                </span>
                                            @elseif($user->active)
                                                <span class="status-badge active">
                                                    <i class="fas fa-check-circle"></i> Hoạt động
                                                </span>
                                            @else
                                                <span class="status-badge inactive">
                                                    <i class="fas fa-times-circle"></i> Chưa kích hoạt
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

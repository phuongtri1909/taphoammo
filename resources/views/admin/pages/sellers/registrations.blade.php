@extends('admin.layouts.sidebar')

@section('title', 'Đăng ký người bán chờ duyệt')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Đăng ký người bán chờ duyệt</h2>
                <a href="{{ route('admin.sellers.index') }}" class="action-button">
                    <i class="fas fa-store"></i> Danh sách người bán
                </a>
            </div>
            <div class="card-content">
                @if ($registrations->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4>Không có đơn đăng ký chờ duyệt</h4>
                        <p>Tất cả đơn đăng ký đã được xử lý</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Thông tin người đăng ký</th>
                                    <th class="column-medium">Thông tin ngân hàng</th>
                                    <th class="column-small text-center">Ngày đăng ký</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($registrations as $key => $registration)
                                    <tr class="text-center">
                                        <td>{{ $registrations->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.seller-registrations.review', $registration) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <span class="item-name">{{ $registration->user->full_name }}</span>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-envelope"></i> {{ $registration->user->email }}
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-phone"></i> {{ $registration->phone }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <small class="d-block"><strong>Ngân hàng:</strong> {{ $registration->bank_name }}</small>
                                            <small class="d-block"><strong>STK:</strong> {{ $registration->bank_account_number }}</small>
                                            <small class="d-block"><strong>Chủ TK:</strong> {{ $registration->bank_account_name }}</small>
                                        </td>
                                        <td>
                                            {{ $registration->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $registration->status->value }}">
                                                {{ $registration->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $registrations->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush


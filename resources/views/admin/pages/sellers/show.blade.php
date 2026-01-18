@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết người bán - ' . $seller->full_name)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.sellers.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-show-header mb-4">
                    <div class="product-header-content">
                        <div class="product-image-wrapper">
                            @if ($seller->avatar)
                                <img src="{{ Storage::url($seller->avatar) }}" alt="{{ $seller->full_name }}">
                            @else
                                <div class="image-placeholder">
                                    <span style="font-size: 48px;">{{ strtoupper(substr($seller->full_name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="product-info-wrapper">
                            <h1 class="product-title">{{ $seller->full_name }}</h1>
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $seller->email }}</span>
                                </div>
                                @if($seller->sellerRegistration)
                                    <div class="product-meta-item">
                                        <i class="fas fa-phone"></i>
                                        <span>{{ $seller->sellerRegistration->phone }}</span>
                                    </div>
                                    <div class="product-meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Đăng ký: {{ $seller->sellerRegistration->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                                <div class="product-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Tham gia: {{ $seller->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            @if($seller->is_seller_banned)
                                <span class="product-status-badge status-badge banned">
                                    <i class="fas fa-ban"></i> Đã khóa
                                </span>
                                @if($seller->seller_ban_reason)
                                    <div class="mt-2">
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Lý do: {{ $seller->seller_ban_reason }}
                                        </small>
                                    </div>
                                @endif
                            @else
                                <span class="product-status-badge status-badge active">
                                    <i class="fas fa-check-circle"></i> Đang hoạt động
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-chart-bar"></i>
                            Thống kê
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="fw-bold text-primary mb-1" style="font-size: 1.1rem;">{{ $stats['total_products'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Sản phẩm</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="fw-bold text-success mb-1" style="font-size: 1.1rem;">{{ number_format($stats['total_sold']) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Đã bán</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="fw-bold text-info mb-1" style="font-size: 1.1rem;">{{ number_format($stats['total_stock']) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Tồn kho</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($seller->sellerRegistration)
                    <div class="product-info-card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-id-card"></i>
                                Thông tin đăng ký
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Ngân hàng:</strong> {{ $seller->sellerRegistration->bank_name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Số tài khoản:</strong> {{ $seller->sellerRegistration->bank_account_number }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Tên chủ tài khoản:</strong> {{ $seller->sellerRegistration->bank_account_name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Số điện thoại:</strong> {{ $seller->sellerRegistration->phone }}
                                </div>
                            </div>
                            @if($seller->sellerRegistration->facebook_url)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Facebook:</strong> 
                                        <a href="{{ $seller->sellerRegistration->facebook_url }}" target="_blank">
                                            {{ $seller->sellerRegistration->facebook_url }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if($seller->sellerRegistration->telegram_username)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Telegram:</strong> {{ $seller->sellerRegistration->telegram_username }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($products->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-box"></i>
                                Sản phẩm ({{ $stats['total_products'] }})
                            </h3>
                        </div>
                        <div class="card-body p-2">
                            <div class="data-table-container">
                                <table class="data-table table-sm">
                                    <thead>
                                        <tr class="text-center" style="font-size: 0.8rem;">
                                            <th class="column-medium text-start">Sản phẩm</th>
                                            <th class="column-small text-center">Đã bán</th>
                                            <th class="column-small text-center">Tồn kho</th>
                                            <th class="column-small text-center">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.85rem;">
                                        @foreach($products as $product)
                                            <tr>
                                                <td class="text-start">
                                                    <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none">
                                                        {{ Str::limit($product->name, 40) }}
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark">{{ number_format($product->variants_sum_sold_count ?? 0) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark">{{ number_format($product->variants_sum_stock_quantity ?? 0) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="status-badge {{ $product->status->value }}" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                                                        {{ $product->status->label() }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($products->hasPages())
                                <div class="mt-3">
                                    {{ $products->appends(request()->except('products_page'))->links('components.paginate') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="product-info-card mb-3">
                        <div class="card-body text-center py-3">
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Chưa có sản phẩm nào</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-cog"></i>
                            Thao tác
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if($seller->is_seller_banned)
                            <form id="unbanForm" class="mb-4">
                                @csrf
                                <button type="submit" class="btn-modern success w-100">
                                    <i class="fas fa-unlock"></i> Mở khóa seller
                                </button>
                            </form>
                        @else
                            <form id="banForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Lý do khóa <span class="required-mark">*</span></label>
                                    <textarea name="reason" id="banReason" class="custom-input" rows="3" required placeholder="Nhập lý do khóa seller..."></textarea>
                                </div>
                                <button type="submit" class="btn-modern danger w-100">
                                    <i class="fas fa-ban"></i> Khóa seller
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
    @vite('resources/assets/admin/css/product-show.css')
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const banForm = document.getElementById('banForm');
        const unbanForm = document.getElementById('unbanForm');

        if (banForm) {
            banForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const reason = document.getElementById('banReason').value.trim();
                if (!reason) {
                    alert('Vui lòng nhập lý do khóa.');
                    return;
                }

                if (!confirm('Bạn có chắc chắn muốn khóa seller này?')) {
                    return;
                }

                fetch('{{ route("admin.sellers.ban", $seller->full_name) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi khóa seller.');
                });
            });
        }

        if (unbanForm) {
            unbanForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!confirm('Bạn có chắc chắn muốn mở khóa seller này?')) {
                    return;
                }

                fetch('{{ route("admin.sellers.unban", $seller->full_name) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi mở khóa seller.');
                });
            });
        }
    });
</script>
@endpush


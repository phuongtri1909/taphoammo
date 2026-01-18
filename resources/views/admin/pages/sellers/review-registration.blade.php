@extends('admin.layouts.sidebar')

@section('title', 'Duyệt đơn đăng ký - ' . $registration->user->full_name)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.seller-registrations.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-show-header mb-4">
                    <div class="product-header-content">
                        <div class="product-image-wrapper">
                            @if ($registration->user->avatar)
                                <img src="{{ Storage::url($registration->user->avatar) }}" alt="{{ $registration->user->full_name }}">
                            @else
                                <div class="image-placeholder">
                                    <span style="font-size: 48px;">{{ strtoupper(substr($registration->user->full_name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="product-info-wrapper">
                            <h1 class="product-title">{{ $registration->user->full_name }}</h1>
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $registration->user->email }}</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $registration->phone }}</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Đăng ký: {{ $registration->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="product-status-badge status-badge pending">Chờ duyệt</span>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i>
                            Thông tin tài khoản
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Họ và tên:</strong> {{ $registration->user->full_name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $registration->user->email }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Ngày tạo tài khoản:</strong> {{ $registration->user->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-6">
                                <strong>2FA:</strong> 
                                @if($registration->user->hasTwoFactorEnabled())
                                    <span class="badge bg-success">Đã bật</span>
                                @else
                                    <span class="badge bg-danger">Chưa bật</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-university"></i>
                            Thông tin ngân hàng
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Ngân hàng:</strong> {{ $registration->bank_name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Số tài khoản:</strong> {{ $registration->bank_account_number }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Tên chủ tài khoản:</strong> {{ $registration->bank_account_name }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($registration->facebook_url || $registration->telegram_username)
                    <div class="product-info-card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-share-alt"></i>
                                Mạng xã hội
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($registration->facebook_url)
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <strong>Facebook:</strong> 
                                        <a href="{{ $registration->facebook_url }}" target="_blank">
                                            {{ $registration->facebook_url }}
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if($registration->telegram_username)
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <strong>Telegram:</strong> {{ $registration->telegram_username }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-check-circle"></i>
                            Duyệt đơn đăng ký
                        </h3>
                    </div>
                    <div class="summary-body">
                        <form id="approveForm" class="mb-4">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label-custom">Ghi chú (tùy chọn)</label>
                                <textarea name="note" id="approveNote" class="custom-input" rows="2" placeholder="Ghi chú cho seller..."></textarea>
                            </div>
                            <button type="submit" class="btn-modern primary w-100">
                                <i class="fas fa-check"></i> Duyệt đơn đăng ký
                            </button>
                        </form>

                        <hr class="my-4">

                        <form id="rejectForm">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label-custom">Lý do từ chối <span class="required-mark">*</span></label>
                                <textarea name="note" id="rejectNote" class="custom-input" rows="3" required placeholder="Nhập lý do từ chối đơn đăng ký..."></textarea>
                            </div>
                            <button type="submit" class="btn-modern danger w-100">
                                <i class="fas fa-times"></i> Từ chối đơn đăng ký
                            </button>
                        </form>
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
        const approveForm = document.getElementById('approveForm');
        const rejectForm = document.getElementById('rejectForm');

        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const note = document.getElementById('approveNote').value.trim();

                Swal.fire({
                    title: 'Xác nhận duyệt đơn đăng ký',
                    html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Bạn có chắc chắn muốn duyệt đơn đăng ký của <strong>{{ $registration->user->full_name }}</strong>?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-check-circle mr-2"></i>Xác nhận duyệt',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
                    width: '480px',
                    padding: '2rem',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-gray-200',
                        confirmButton: 'px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all',
                        cancelButton: 'px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route("admin.seller-registrations.approve", $registration->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ note: note })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message}</p>
                                        </div>
                                    `,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                }).then(() => {
                                    window.location.href = '{{ route("admin.seller-registrations.index") }}';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                                <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message || 'Có lỗi xảy ra.'}</p>
                                        </div>
                                    `,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Lỗi',
                                html: `
                                    <div style="text-align: center; padding: 1rem 0;">
                                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                            <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                        </div>
                                        <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Có lỗi xảy ra khi duyệt đơn đăng ký.</p>
                                    </div>
                                `,
                                icon: 'error',
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                width: '480px'
                            });
                        });
                    }
                });
            });
        }

        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const note = document.getElementById('rejectNote').value.trim();
                if (!note) {
                    Swal.fire({
                        title: 'Thông báo',
                        html: `
                            <div style="text-align: center; padding: 1rem 0;">
                                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                                </div>
                                <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Vui lòng nhập lý do từ chối.</p>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                        width: '480px'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận từ chối đơn đăng ký',
                    html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <p style="font-size: 16px; color: #374151; margin: 0 0 1rem 0; font-weight: 600;">Bạn có chắc chắn muốn từ chối đơn đăng ký của <strong>{{ $registration->user->full_name }}</strong>?</p>
                            <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 0.75rem; border-radius: 0.5rem; text-align: left;">
                                <strong style="color: #991b1b; display: block; margin-bottom: 0.5rem;">Lý do từ chối:</strong>
                                <p style="color: #7f1d1d; margin: 0; font-size: 0.9rem;">${note}</p>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-times-circle mr-2"></i>Xác nhận từ chối',
                    cancelButtonText: '<i class="fas fa-arrow-left mr-2"></i>Hủy',
                    width: '520px',
                    padding: '2rem',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-gray-200',
                        confirmButton: 'px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all',
                        cancelButton: 'px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route("admin.seller-registrations.reject", $registration->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ note: note })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message}</p>
                                        </div>
                                    `,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                }).then(() => {
                                    window.location.href = '{{ route("admin.seller-registrations.index") }}';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                                <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message || 'Có lỗi xảy ra.'}</p>
                                        </div>
                                    `,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Lỗi',
                                html: `
                                    <div style="text-align: center; padding: 1rem 0;">
                                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                            <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                        </div>
                                        <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Có lỗi xảy ra khi từ chối đơn đăng ký.</p>
                                    </div>
                                `,
                                icon: 'error',
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                width: '480px'
                            });
                        });
                    }
                });
            });
        }
    });
</script>
@endpush


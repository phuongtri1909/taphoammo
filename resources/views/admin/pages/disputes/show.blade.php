@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết tranh chấp - ' . $dispute->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.disputes.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Thông tin tranh chấp
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Đơn hàng:</small>
                                <p class="mb-0">
                                    <a class="color-primary" href="{{ route('admin.orders.show', $dispute->order->slug) }}">
                                        <strong>#{{ $dispute->order->slug }}</strong>
                                    </a>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $dispute->status->badgeColor() }} text-white">
                                        {{ $dispute->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $dispute->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $dispute->buyer->email }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Người bán:</small>
                                <p class="mb-0"><strong>{{ $dispute->seller->full_name }}</strong></p>
                                <small class="text-muted">{{ $dispute->seller->email }}</small>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $dispute->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-box"></i>
                            Sản phẩm
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <a class="mb-1 color-primary" href="{{ route('admin.products.show', $dispute->orderItem->productVariant->product->slug) }}"><strong>{{ $dispute->orderItem->productVariant->product->name }}</strong></a>
                        <p class="mb-1 text-muted" style="font-size: 0.85rem;">Biến thể: {{ $dispute->orderItem->productVariant->name }}</p>
                        <p class="mb-2 text-muted" style="font-size: 0.85rem;">
                            Số lượng: {{ $dispute->orderItem->quantity }} × {{ number_format($dispute->orderItem->price, 0, ',', '.') }}₫ = 
                            <strong class="text-primary">{{ number_format($dispute->orderItem->price * $dispute->orderItem->quantity, 0, ',', '.') }}₫</strong>
                        </p>
                        @if($disputedProductValues && $disputedProductValues->count() > 0)
                            <div class="mt-2 pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Giá trị sản phẩm đã khiếu nại ({{ $disputedProductValues->count() }}):</small>
                                    <button type="button" class="btn btn-sm action-button" id="viewAllValuesBtn" style="font-size: 0.75rem;">
                                        <i class="fas fa-eye"></i> Xem tất cả
                                    </button>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($disputedProductValues as $value)
                                        <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">#{{ $value->slug }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-user"></i>
                            Lý do khiếu nại (từ người mua)
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->reason }}</p>
                    </div>
                </div>

                @if($dispute->seller_note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 bg-warning-subtle">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-store"></i>
                                Phản hồi từ Seller (từ chối)
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->seller_note }}</p>
                        </div>
                    </div>
                @endif

                @if($dispute->admin_note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 bg-info-subtle">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-user-shield"></i>
                                Ghi chú từ Admin
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->admin_note }}</p>
                        </div>
                    </div>
                @endif

                @if($dispute->evidence || $dispute->evidence_files)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-paperclip"></i>
                                Bằng chứng
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            @if($dispute->evidence)
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1">URLs:</small>
                                    @foreach($dispute->evidence as $url)
                                        <a href="{{ $url }}" target="_blank" class="d-block mb-1 text-primary" style="font-size: 0.85rem;">
                                            <i class="fas fa-external-link-alt"></i> {{ $url }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            @if($dispute->evidence_files)
                                <div>
                                    <small class="text-muted d-block mb-1">Files:</small>
                                    @foreach($dispute->evidence_files as $filePath)
                                        @php
                                            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp']);
                                        @endphp
                                        <div class="mb-2">
                                            @if($isImage)
                                                <a href="{{ Storage::url($filePath) }}" target="_blank" class="d-block">
                                                    <img src="{{ Storage::url($filePath) }}" alt="Evidence" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                </a>
                                            @else
                                                <a href="{{ Storage::url($filePath) }}" target="_blank" class="d-block text-primary" style="font-size: 0.85rem;">
                                                    <i class="fas fa-file"></i> {{ basename($filePath) }}
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
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
                            <i class="fas fa-gavel"></i>
                            Quyết định của Admin
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if(in_array($dispute->status, [\App\Enums\DisputeStatus::OPEN, \App\Enums\DisputeStatus::REVIEWING]))
                            @php
                                $refundAmount = $disputedProductValues->count() * $dispute->orderItem->price;
                                $totalOrderItemValues = $dispute->orderItem->quantity;
                                $nonDisputedCount = $totalOrderItemValues - $disputedProductValues->count();
                                $commissionRate = (float) \App\Models\Config::getConfig('commission_rate', 10);
                                $sellerAmount = $nonDisputedCount * $dispute->orderItem->price * (1 - $commissionRate / 100);
                            @endphp
                            
                            <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                                <strong><i class="fas fa-info-circle"></i> Thông tin hoàn tiền nếu chấp nhận:</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>Số giá trị bị khiếu nại: <strong>{{ $disputedProductValues->count() }}</strong></li>
                                    <li>Hoàn cho người mua: <strong class="text-danger">{{ number_format($refundAmount, 0, ',', '.') }}₫</strong></li>
                                    @if($nonDisputedCount > 0)
                                        <li>Seller nhận được ({{ $nonDisputedCount }} giá trị): <strong class="text-success">{{ number_format($sellerAmount, 0, ',', '.') }}₫</strong> <small class="text-muted">(sau {{ $commissionRate }}% phí sàn)</small></li>
                                    @endif
                                </ul>
                            </div>

                            <form id="approveDisputeForm" class="mb-4">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Ghi chú (tùy chọn)</label>
                                    <textarea name="admin_note" class="custom-input" rows="2" placeholder="Ghi chú khi chấp nhận..."></textarea>
                                </div>
                                <button type="submit" class="btn-modern success w-100">
                                    <i class="fas fa-check"></i> Chấp nhận & Hoàn tiền
                                </button>
                            </form>

                            <hr class="my-3">

                            <form id="rejectDisputeForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Lý do từ chối <span class="required-mark">*</span></label>
                                    <textarea name="admin_note" class="custom-input" rows="3" required placeholder="Nhập lý do từ chối..." minlength="10" maxlength="1000"></textarea>
                                </div>
                                <button type="submit" class="btn-modern danger w-100">
                                    <i class="fas fa-times"></i> Từ chối tranh chấp
                                </button>
                            </form>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-2">Tranh chấp đã được xử lý</p>
                                <span class="badge bg-{{ $dispute->status->badgeColor() }}" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                    {{ $dispute->status->label() }}
                                </span>
                                @if($dispute->resolvedBy)
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            Xử lý bởi: {{ $dispute->resolvedBy->full_name }}<br>
                                            {{ $dispute->resolved_at ? $dispute->resolved_at->format('d/m/Y H:i') : '' }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="valueModal" class="modal fade" tabindex="-1" aria-labelledby="valueModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="valueModalLabel">
                            <i class="fas fa-eye"></i> Chi tiết giá trị sản phẩm đã khiếu nại
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="valueModalContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn back-button" data-bs-dismiss="modal">Đóng</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const approveForm = document.getElementById('approveDisputeForm');
        const rejectForm = document.getElementById('rejectDisputeForm');

        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const adminNote = document.querySelector('#approveDisputeForm textarea[name="admin_note"]').value.trim();

                Swal.fire({
                    title: 'Xác nhận chấp nhận tranh chấp',
                    html: `
                        <div style="text-align: left; padding: 1rem 0;">
                            <p style="font-size: 14px; color: #374151; margin-bottom: 1rem;">Khi bạn chấp nhận:</p>
                            <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                                <li>Người mua sẽ được hoàn tiền cho các giá trị bị khiếu nại</li>
                                <li>Seller sẽ nhận tiền cho các giá trị không bị khiếu nại (sau % sàn)</li>
                            </ul>
                            <p style="font-size: 14px; color: #374151; margin-top: 1rem;"><strong>Bạn có chắc chắn?</strong></p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-check-circle mr-2"></i>Xác nhận chấp nhận',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
                    width: '480px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route("admin.disputes.approve", $dispute->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ admin_note: adminNote })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    text: data.message || 'Có lỗi xảy ra.',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Lỗi',
                                text: 'Có lỗi xảy ra khi xử lý tranh chấp.',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        });
                    }
                });
            });
        }

        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const adminNote = document.querySelector('#rejectDisputeForm textarea[name="admin_note"]').value.trim();
                if (!adminNote || adminNote.length < 10) {
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Vui lòng nhập lý do từ chối (ít nhất 10 ký tự).',
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận từ chối tranh chấp',
                    html: `
                        <div style="text-align: left; padding: 1rem 0;">
                            <p style="font-size: 14px; color: #374151; margin-bottom: 1rem;">Khi bạn từ chối:</p>
                            <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                                <li>Không có thay đổi gì về tiền</li>
                                <li>Đơn hàng sẽ được tự động hoàn thành sau</li>
                            </ul>
                            <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 0.75rem; border-radius: 0.5rem; margin-top: 1rem;">
                                <strong style="color: #991b1b; display: block; margin-bottom: 0.5rem;">Lý do từ chối:</strong>
                                <p style="color: #7f1d1d; margin: 0; font-size: 0.9rem;">${adminNote}</p>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-times-circle mr-2"></i>Xác nhận từ chối',
                    cancelButtonText: '<i class="fas fa-arrow-left mr-2"></i>Hủy',
                    width: '520px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route("admin.disputes.reject", $dispute->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ admin_note: adminNote })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    text: data.message || 'Có lỗi xảy ra.',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Lỗi',
                                text: 'Có lỗi xảy ra khi từ chối tranh chấp.',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        });
                    }
                });
            });
        }

        const viewAllValuesBtn = document.getElementById('viewAllValuesBtn');
        if (viewAllValuesBtn) {
            viewAllValuesBtn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('valueModal'));
                const content = document.getElementById('valueModalContent');
                
                content.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted small">Đang tải dữ liệu...</p>
                    </div>
                `;
                
                modal.show();
                
                const slugs = [
                    @foreach($disputedProductValues as $value)
                        '{{ $value->slug }}'{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ];
                
                const promises = slugs.map(slug => 
                    fetch(`/product-values/${slug}/data`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(response => response.json())
                );
                
                Promise.all(promises)
                    .then(results => {
                        let html = `<div class="list-group list-group-flush">`;
                        
                        results.forEach((data, index) => {
                            if (data.success) {
                                const valueText = data.data?.value || '';
                                const passwordText = data.data?.password || '';
                                const otherFields = [];
                                
                                if (data.data) {
                                    Object.keys(data.data).forEach(key => {
                                        if (!['value', 'password'].includes(key)) {
                                            const val = typeof data.data[key] === 'object' 
                                                ? JSON.stringify(data.data[key])
                                                : data.data[key];
                                            otherFields.push(`${key}: ${val}`);
                                        }
                                    });
                                }
                                
                                const displayText = [
                                    valueText,
                                    passwordText ? `Pass: ${passwordText}` : '',
                                    ...otherFields
                                ].filter(Boolean).join(' | ') || 'Không có dữ liệu';
                                
                                html += `
                                    <div class="list-group-item px-2 py-1.5 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary" style="font-size: 0.7rem; min-width: 80px;">#${data.value.slug}</span>
                                            <code class="flex-grow-1 text-break" style="font-size: 0.75rem; background: transparent; padding: 0;">${displayText}</code>
                                        </div>
                                    </div>
                                `;
                            }
                        });
                        
                        html += `</div>`;
                        content.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        content.innerHTML = `
                            <div class="text-center py-4">
                                <p class="text-danger mb-0">Có lỗi xảy ra khi tải dữ liệu.</p>
                            </div>
                        `;
                    });
            });
        }
    });
</script>
@endpush

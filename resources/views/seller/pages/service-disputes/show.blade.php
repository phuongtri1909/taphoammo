@extends('seller.layouts.sidebar')

@section('title', 'Chi tiết khiếu nại dịch vụ - ' . $dispute->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('seller.service-disputes.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Thông tin khiếu nại
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Mã khiếu nại:</small>
                                <p class="mb-0"><strong>#{{ $dispute->slug }}</strong></p>
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
                                <small class="text-muted">Đơn hàng:</small>
                                <p class="mb-0">
                                    <a href="{{ route('seller.service-orders.show', $dispute->serviceOrder->slug) }}" class="text-primary">
                                        <strong>#{{ $dispute->serviceOrder->slug }}</strong>
                                    </a>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Giá trị đơn hàng:</small>
                                <p class="mb-0">
                                    <strong class="text-primary">{{ number_format($dispute->serviceOrder->total_amount, 0, ',', '.') }}₫</strong>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $dispute->serviceOrder->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $dispute->serviceOrder->buyer->email }}</small>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $dispute->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            @if($dispute->resolved_at)
                                <div class="col-6">
                                    <small class="text-muted">Ngày xử lý:</small>
                                    <p class="mb-0">{{ $dispute->resolved_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-concierge-bell"></i>
                            Dịch vụ
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        @if($dispute->serviceOrder->serviceVariant && $dispute->serviceOrder->serviceVariant->service)
                            <div class="p-2 bg-gray-50 rounded">
                                <div class="flex-1">
                                    <strong>{{ $dispute->serviceOrder->serviceVariant->service->name }}</strong>
                                    <p class="mb-1 text-muted" style="font-size: 0.85rem;">Biến thể: {{ $dispute->serviceOrder->serviceVariant->name }}</p>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                        Giá: <strong class="text-primary">{{ number_format($dispute->serviceOrder->serviceVariant->price, 0, ',', '.') }}₫</strong>
                                    </p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Dịch vụ không tồn tại</p>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-comment"></i>
                            Lý do khiếu nại
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="p-2 bg-warning-subtle rounded border border-warning">
                            <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->reason }}</p>
                        </div>
                    </div>
                </div>

                @if($dispute->evidence && is_array($dispute->evidence) && count($dispute->evidence) > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-link"></i>
                                Bằng chứng URL
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="space-y-1">
                                @foreach($dispute->evidence as $url)
                                    <a href="{{ $url }}" target="_blank" class="d-block text-primary" style="font-size: 0.85rem;">
                                        <i class="fas fa-external-link-alt me-1"></i>{{ $url }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($dispute->evidence_files && is_array($dispute->evidence_files) && count($dispute->evidence_files) > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-paperclip"></i>
                                Tệp đính kèm
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($dispute->evidence_files as $index => $file)
                                    @if(isset($file['path']))
                                        @php
                                            $isImage = in_array(strtolower(pathinfo($file['path'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <a href="javascript:void(0)" onclick="openFileModal('{{ asset('storage/' . $file['path']) }}', '{{ $file['name'] ?? 'Image' }}', true)">
                                                <img src="{{ asset('storage/' . $file['path']) }}" alt="{{ $file['name'] ?? 'Evidence' }}" 
                                                    class="rounded border" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" onclick="openFileModal('{{ asset('storage/' . $file['path']) }}', '{{ $file['name'] ?? 'File' }}', false)"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-file me-1"></i>
                                                <span class="text-truncate" style="max-width: 120px; display: inline-block;">{{ $file['name'] ?? 'File' }}</span>
                                            </a>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($dispute->seller_note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-reply"></i>
                                Phản hồi của bạn
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="p-2 bg-info-subtle rounded border border-info">
                                <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->seller_note }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($dispute->admin_note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-user-shield"></i>
                                Ghi chú Admin
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="p-2 bg-primary-subtle rounded border border-primary">
                                <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->admin_note }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card mb-3" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-cog"></i>
                            Thao tác
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if($dispute->status === \App\Enums\ServiceDisputeStatus::OPEN)
                            <div class="alert alert-warning mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Khiếu nại đang chờ bạn xử lý</strong>
                            </div>
                            
                            <form id="responseForm">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Phản hồi của bạn (tùy chọn)</label>
                                    <textarea name="seller_note" id="sellerNote" class="custom-input" rows="3" 
                                        placeholder="Nhập phản hồi cho khách hàng..." maxlength="1000"></textarea>
                                </div>
                            </form>
                            
                            <button type="button" id="acceptBtn" class="btn-modern success w-100 mb-2">
                                <i class="fas fa-check"></i> Chấp nhận giải quyết
                            </button>
                            <small class="text-muted d-block text-center mb-3">Bạn thừa nhận cần điều chỉnh theo yêu cầu</small>
                            
                            <hr class="my-3">
                            
                            <button type="button" id="rejectBtn" class="btn-modern danger w-100 mb-2">
                                <i class="fas fa-times"></i> Từ chối (Chuyển Admin)
                            </button>
                            <small class="text-muted d-block text-center">Khiếu nại sẽ được gửi cho Admin xem xét</small>
                            
                        @elseif($dispute->status === \App\Enums\ServiceDisputeStatus::REVIEWING)
                            <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-hourglass-half"></i> 
                                <strong>Đang chờ Admin xem xét</strong>
                                <p class="mb-0 mt-1">Khiếu nại đã được chuyển cho Admin. Vui lòng chờ quyết định.</p>
                            </div>
                            
                        @elseif($dispute->status === \App\Enums\ServiceDisputeStatus::APPROVED)
                            <div class="alert alert-success mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-check-circle"></i> 
                                <strong>Khiếu nại đã được chấp nhận</strong>
                                @if($dispute->resolvedBy)
                                    <p class="mb-0 mt-1">Xử lý bởi: {{ $dispute->resolvedBy->full_name }}</p>
                                @endif
                            </div>
                            
                        @elseif($dispute->status === \App\Enums\ServiceDisputeStatus::REJECTED)
                            <div class="alert alert-danger mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-times-circle"></i> 
                                <strong>Khiếu nại đã bị từ chối</strong>
                                @if($dispute->resolvedBy)
                                    <p class="mb-0 mt-1">Xử lý bởi: {{ $dispute->resolvedBy->full_name }}</p>
                                @endif
                            </div>
                            
                        @elseif($dispute->status === \App\Enums\ServiceDisputeStatus::WITHDRAWN)
                            <div class="alert alert-secondary mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-undo"></i> 
                                <strong>Khiếu nại đã được rút</strong>
                                <p class="mb-0 mt-1">Người mua đã rút khiếu nại này.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div id="fileModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalTitle">Xem file</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="fileModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <a href="#" id="downloadLink" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Tải xuống
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
    const acceptBtn = document.getElementById('acceptBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    
    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            const sellerNote = document.getElementById('sellerNote')?.value || '';
            
            Swal.fire({
                title: 'Chấp nhận giải quyết khiếu nại',
                html: `
                    <div style="text-align: left; padding: 1rem 0;">
                        <p style="font-size: 14px; color: #374151;">Khi bạn chấp nhận:</p>
                        <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                            <li>Bạn thừa nhận cần điều chỉnh theo yêu cầu</li>
                            <li>Thời gian sẽ được reset để bạn thực hiện</li>
                            <li>Sau khi hoàn thành, nhấn "Báo lại đã hoàn thành"</li>
                        </ul>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-check"></i> Xác nhận chấp nhận',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('{{ route("seller.service-disputes.accept", $dispute->slug) }}', { seller_note: sellerNote });
                }
            });
        });
    }
    
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            const sellerNote = document.getElementById('sellerNote')?.value || '';
            
            Swal.fire({
                title: 'Từ chối giải quyết khiếu nại',
                html: `
                    <div style="text-align: left; padding: 1rem 0;">
                        <p style="font-size: 14px; color: #374151;">Khi bạn từ chối:</p>
                        <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                            <li>Khiếu nại sẽ được chuyển cho Admin xem xét</li>
                            <li>Admin sẽ đưa ra quyết định cuối cùng</li>
                        </ul>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-times"></i> Xác nhận từ chối',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('{{ route("seller.service-disputes.reject", $dispute->slug) }}', { seller_note: sellerNote });
                }
            });
        });
    }
});

function submitAction(url, data = {}) {
    Swal.fire({
        title: 'Đang xử lý...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Thành công!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => window.location.reload());
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
            text: 'Có lỗi xảy ra khi xử lý.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    });
}

function openFileModal(url, name, isImage) {
    const modal = new bootstrap.Modal(document.getElementById('fileModal'));
    document.getElementById('fileModalTitle').textContent = name;
    document.getElementById('downloadLink').href = url;
    
    if (isImage) {
        document.getElementById('fileModalBody').innerHTML = `
            <img src="${url}" alt="${name}" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
        `;
    } else {
        // Check if it's a text file or PDF
        const extension = url.split('.').pop().toLowerCase();
        if (extension === 'pdf') {
            document.getElementById('fileModalBody').innerHTML = `
                <iframe src="${url}" style="width: 100%; height: 70vh; border: none;"></iframe>
            `;
        } else if (['txt', 'rtf'].includes(extension)) {
            document.getElementById('fileModalBody').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-5x text-muted mb-3"></i>
                    <p class="text-muted">File: ${name}</p>
                    <a href="${url}" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Tải xuống để xem
                    </a>
                </div>
            `;
        } else {
            document.getElementById('fileModalBody').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-file fa-5x text-muted mb-3"></i>
                    <p class="text-muted">File: ${name}</p>
                    <a href="${url}" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Tải xuống để xem
                    </a>
                </div>
            `;
        }
    }
    
    modal.show();
}
</script>
@endpush

@extends('seller.layouts.sidebar')

@section('title', 'Chi tiết khiếu nại - ' . $dispute->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('seller.refunds.index') }}" class="btn back-button">
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
                                <small class="text-muted">Đơn hàng:</small>
                                <a class="mb-0 color-primary" href="{{ route('seller.orders.show', $dispute->order->slug) }}"><strong>#{{ $dispute->order->slug }}</strong></a>
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
                            <div class="col-12">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $dispute->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $dispute->buyer->email }}</small>
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
                        <a href="{{ route('seller.products.show', $dispute->orderItem->productVariant->product->slug) }}" class="mb-1"><strong>{{ $dispute->orderItem->productVariant->product->name }}</strong></a>
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
                                        <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">
                                            #{{ $value->slug }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-comment-alt"></i>
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
                                <i class="fas fa-reply"></i>
                                Phản hồi của bạn (Seller)
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
                                    <small class="text-muted d-block mb-1">Files đã tải lên ({{ count($dispute->evidence_files) }}):</small>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($dispute->evidence_files as $index => $filePath)
                                            @php
                                                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp']);
                                                $fileName = basename($filePath);
                                                $fileUrl = Storage::url($filePath);
                                            @endphp
                                            <div class="evidence-file-item" style="cursor: pointer;" onclick="viewEvidenceFile('{{ $fileUrl }}', '{{ $fileName }}', {{ $isImage ? 'true' : 'false' }}, '{{ strtolower($extension) }}')">
                                                @if($isImage)
                                                    <div class="position-relative" style="width: 120px; height: 120px; border: 1px solid #dee2e6; border-radius: 0.375rem; overflow: hidden; background: #f8f9fa;">
                                                        <img src="{{ $fileUrl }}" alt="{{ $fileName }}" 
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                                                            style="background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.2s;">
                                                            <i class="fas fa-search-plus text-white" style="font-size: 1.5rem;"></i>
                                                        </div>
                                                    </div>
                                                    <small class="d-block mt-1 text-center text-muted" style="font-size: 0.75rem; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $fileName }}</small>
                                                @else
                                                    <div class="d-flex flex-column align-items-center p-2 border rounded" 
                                                        style="width: 120px; min-height: 100px; background: #f8f9fa; transition: background 0.2s;">
                                                        <i class="fas fa-file-{{ $extension === 'pdf' ? 'pdf' : ($extension === 'doc' || $extension === 'docx' ? 'word' : 'alt') }} text-primary mb-2" style="font-size: 2rem;"></i>
                                                        <small class="text-center text-muted" style="font-size: 0.7rem; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $fileName }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
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
                            <i class="fas fa-cog"></i>
                            Xử lý khiếu nại
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if($dispute->status === \App\Enums\DisputeStatus::OPEN)
                            @php
                                $refundAmount = $disputedProductValues->count() * $dispute->orderItem->price;
                                $totalOrderItemValues = $dispute->orderItem->quantity;
                                $nonDisputedCount = $totalOrderItemValues - $disputedProductValues->count();
                                $commissionRate = (float) \App\Models\Config::getConfig('commission_rate', 10);
                                $sellerAmount = $nonDisputedCount * $dispute->orderItem->price * (1 - $commissionRate / 100);
                            @endphp
                            
                            <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                                <strong><i class="fas fa-info-circle"></i> Thông tin hoàn tiền:</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>Số giá trị bị khiếu nại: <strong>{{ $disputedProductValues->count() }}</strong></li>
                                    <li>Hoàn cho người mua: <strong class="text-danger">{{ number_format($refundAmount, 0, ',', '.') }}₫</strong></li>
                                    @if($nonDisputedCount > 0)
                                        <li>Bạn nhận được ({{ $nonDisputedCount }} giá trị): <strong class="text-success">{{ number_format($sellerAmount, 0, ',', '.') }}₫</strong> <small class="text-muted">(sau {{ $commissionRate }}% phí sàn)</small></li>
                                    @endif
                                </ul>
                            </div>

                            <button type="button" id="acceptBtn" class="btn-modern success w-100 mb-3">
                                <i class="fas fa-check"></i> Chấp nhận khiếu nại
                            </button>

                            <hr class="my-3">

                            <form id="rejectDisputeForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Lý do từ chối <span class="required-mark">*</span></label>
                                    <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Admin sẽ xem xét và đưa ra quyết định cuối cùng</small>
                                    <textarea name="seller_note" class="custom-input" rows="3" required placeholder="Nhập lý do bạn không chấp nhận khiếu nại này..." minlength="10" maxlength="1000"></textarea>
                                </div>
                                <button type="submit" class="btn-modern danger w-100">
                                    <i class="fas fa-times"></i> Từ chối & Chuyển Admin
                                </button>
                            </form>
                        @elseif($dispute->status === \App\Enums\DisputeStatus::REVIEWING)
                            <div class="text-center py-3">
                                <div class="mb-3">
                                    <span class="badge bg-info" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-hourglass-half"></i> Đang chờ Admin xem xét
                                    </span>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                    Khiếu nại đã được chuyển cho Admin. Vui lòng chờ quyết định cuối cùng.
                                </p>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-2">Khiếu nại đã được xử lý</p>
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

        <div id="evidenceFileModal" class="modal fade" tabindex="-1" aria-labelledby="evidenceFileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="evidenceFileModalLabel">
                            <i class="fas fa-file"></i> <span id="evidenceFileName"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="evidenceFileContent" style="max-height: 70vh; overflow: auto;">
                    </div>
                    <div class="modal-footer">
                        <a id="evidenceFileDownload" href="#" download class="btn action-button">
                            <i class="fas fa-download"></i> Tải xuống
                        </a>
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
        const acceptBtn = document.getElementById('acceptBtn');
        const rejectForm = document.getElementById('rejectDisputeForm');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Xác nhận chấp nhận khiếu nại',
                    html: `
                        <div style="text-align: left; padding: 1rem 0;">
                            <p style="font-size: 14px; color: #374151; margin-bottom: 1rem;">Khi bạn chấp nhận:</p>
                            <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                                <li>Người mua sẽ được hoàn tiền cho các giá trị bị khiếu nại</li>
                                <li>Bạn sẽ nhận tiền cho các giá trị không bị khiếu nại (sau % sàn)</li>
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

                        fetch('{{ route("seller.refunds.dispute.accept", $dispute->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
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
                                text: 'Có lỗi xảy ra khi xử lý khiếu nại.',
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
                
                const sellerNote = document.querySelector('#rejectDisputeForm textarea[name="seller_note"]').value.trim();
                if (!sellerNote || sellerNote.length < 10) {
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Vui lòng nhập lý do từ chối (ít nhất 10 ký tự).',
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận từ chối khiếu nại',
                    html: `
                        <div style="text-align: left; padding: 1rem 0;">
                            <p style="font-size: 14px; color: #374151; margin-bottom: 1rem;">Khi bạn từ chối:</p>
                            <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                                <li>Khiếu nại sẽ được chuyển cho Admin xem xét</li>
                                <li>Admin sẽ đưa ra quyết định cuối cùng</li>
                            </ul>
                            <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 0.75rem; border-radius: 0.5rem; margin-top: 1rem;">
                                <strong style="color: #991b1b; display: block; margin-bottom: 0.5rem;">Lý do của bạn:</strong>
                                <p style="color: #7f1d1d; margin: 0; font-size: 0.9rem;">${sellerNote}</p>
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

                        fetch('{{ route("seller.refunds.dispute.reject", $dispute->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ seller_note: sellerNote })
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
                                text: 'Có lỗi xảy ra khi từ chối khiếu nại.',
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

    function viewEvidenceFile(url, fileName, isImage, extension) {
        const modal = new bootstrap.Modal(document.getElementById('evidenceFileModal'));
        const content = document.getElementById('evidenceFileContent');
        const title = document.getElementById('evidenceFileName');
        const downloadBtn = document.getElementById('evidenceFileDownload');
        
        title.textContent = fileName;
        downloadBtn.href = url;
        
        if (isImage) {
            content.innerHTML = `
                <div class="text-center">
                    <img src="${url}" alt="${fileName}" class="img-fluid" style="max-height: 65vh;">
                </div>
            `;
        } else if (extension === 'pdf') {
            content.innerHTML = `
                <div class="w-100" style="height: 65vh;">
                    <iframe src="${url}" class="w-100 h-100 border-0"></iframe>
                </div>
            `;
        } else if (['txt', 'rtf'].includes(extension)) {
            fetch(url)
                .then(response => response.text())
                .then(text => {
                    content.innerHTML = `
                        <pre class="p-3 bg-light rounded" style="white-space: pre-wrap; word-wrap: break-word; font-size: 0.9rem;">${text}</pre>
                    `;
                })
                .catch(error => {
                    content.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">Không thể đọc nội dung file. Vui lòng tải xuống để xem.</p>
                        </div>
                    `;
                });
        } else {
            content.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-file text-primary" style="font-size: 4rem;"></i>
                    <p class="mt-3">Loại file này không thể xem trực tiếp.</p>
                    <p class="text-muted">Vui lòng tải xuống để xem nội dung.</p>
                </div>
            `;
        }
        
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const evidenceItems = document.querySelectorAll('.evidence-file-item');
        evidenceItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                const overlay = this.querySelector('[style*="opacity"]');
                if (overlay) {
                    overlay.style.opacity = '1';
                }
                const bg = this.querySelector('[style*="background"]');
                if (bg && !bg.querySelector('img')) {
                    bg.style.background = '#e9ecef';
                }
            });
            item.addEventListener('mouseleave', function() {
                const overlay = this.querySelector('[style*="opacity"]');
                if (overlay) {
                    overlay.style.opacity = '0';
                }
                const bg = this.querySelector('[style*="background"]');
                if (bg && !bg.querySelector('img')) {
                    bg.style.background = '#f8f9fa';
                }
            });
        });
    });
</script>
<style>
.evidence-file-item:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
</style>
@endpush

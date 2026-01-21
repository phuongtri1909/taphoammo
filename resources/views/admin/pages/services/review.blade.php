@extends('admin.layouts.sidebar')

@section('title', 'Duyệt dịch vụ - ' . $service->name)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.services.pending') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <!-- Service Info -->
            <div class="col-lg-8">
                <!-- Service Header -->
                <div class="product-show-header mb-4">
                    <div class="product-header-content">
                        <div class="product-image-wrapper">
                            @if ($service->image)
                                <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-info-wrapper">
                            <h1 class="product-title">{{ $service->name }}</h1>
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <i class="fas fa-folder"></i>
                                    <span>{{ $service->serviceSubCategory->serviceCategory->name ?? 'N/A' }} > {{ $service->serviceSubCategory->name ?? 'N/A' }}</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-user"></i>
                                    <span>Seller: <strong>{{ $service->seller->full_name }}</strong> ({{ $service->seller->email }})</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $service->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="product-status-badge status-badge pending">Chờ duyệt</span>
                        </div>
                    </div>
                </div>

                @if ($service->description || $service->long_description)
                    <div class="product-info-card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Thông tin dịch vụ
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($service->description)
                                <div class="product-description-section mb-3">
                                    <h5><i class="fas fa-align-left"></i> Mô tả ngắn</h5>
                                    <p class="mb-0">{{ $service->description }}</p>
                                </div>
                            @endif

                            @if ($service->long_description)
                                <div class="product-description-section mb-3">
                                    <h5><i class="fas fa-file-alt"></i> Mô tả chi tiết</h5>
                                    <div class="long-description-display">{!! nl2br(e($service->long_description)) !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Variants -->
                <div class="variants-accordion">
                    @foreach ($service->variants as $index => $variant)
                        <div class="variant-card-accordion mb-3">
                            <div class="variant-accordion-header">
                                <div class="variant-header-content">
                                    <div class="variant-header-info" data-bs-toggle="collapse" data-bs-target="#variantCollapse{{ $variant->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="variantCollapse{{ $variant->id }}">
                                        <h4 class="variant-name">{{ $variant->name }}</h4>
                                        <div class="variant-badges-compact">
                                            <span class="variant-badge price">{{ number_format($variant->price, 0, ',', '.') }} VNĐ</span>
                                            <span class="variant-badge status-{{ $variant->status->value }}">
                                                {{ $variant->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="variant-header-actions">
                                        <i class="fas fa-chevron-down accordion-icon"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="collapse {{ $index === 0 ? 'show' : '' }}" id="variantCollapse{{ $variant->id }}">
                                <div class="variant-accordion-body">
                                    <div class="variant-field-name">
                                        <i class="fas fa-tag"></i>
                                        <span>Slug:</span>
                                        <code>{{ $variant->slug }}</code>
                                    </div>
                                    <div class="variant-field-name mt-2">
                                        <i class="fas fa-sort-numeric-up"></i>
                                        <span>Thứ tự:</span>
                                        <code>{{ $variant->order }}</code>
                                    </div>
                                    <div class="variant-field-name mt-2">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Đã bán:</span>
                                        <code>{{ $variant->sold_count }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-check-circle"></i>
                            Duyệt dịch vụ
                        </h3>
                    </div>
                    <div class="summary-body">
                        <form id="approveServiceForm" class="mb-4">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label-custom">Ghi chú (tùy chọn)</label>
                                <textarea id="approveServiceNote" name="admin_note" class="custom-input" rows="2" placeholder="Ghi chú cho seller..."></textarea>
                            </div>
                            <button type="submit" class="btn-modern primary w-100">
                                <i class="fas fa-check"></i> Duyệt dịch vụ
                            </button>
                        </form>

                        <hr class="my-4">

                        <form id="rejectServiceForm">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label-custom">Lý do từ chối <span class="required-mark">*</span></label>
                                <textarea id="rejectServiceNote" name="admin_note" class="custom-input" rows="3" required placeholder="Nhập lý do từ chối dịch vụ..."></textarea>
                            </div>
                            <button type="submit" class="btn-modern danger w-100">
                                <i class="fas fa-times"></i> Từ chối dịch vụ
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
        const approveForm = document.getElementById('approveServiceForm');
        const rejectForm = document.getElementById('rejectServiceForm');

        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const note = document.getElementById('approveServiceNote').value.trim();

                Swal.fire({
                    title: 'Xác nhận duyệt dịch vụ',
                    html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Bạn có chắc chắn muốn duyệt dịch vụ <strong>"{{ $service->name }}"</strong>?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-check-circle mr-2"></i>Xác nhận duyệt',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
                    width: '480px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("admin.services.approve", $service->slug) }}';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        if (note) {
                            const noteField = document.createElement('input');
                            noteField.type = 'hidden';
                            noteField.name = 'admin_note';
                            noteField.value = note;
                            form.appendChild(noteField);
                        }
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }

        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const note = document.getElementById('rejectServiceNote').value.trim();
                if (!note) {
                    Swal.fire({
                        title: 'Thông báo',
                        text: 'Vui lòng nhập lý do từ chối.',
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận từ chối dịch vụ',
                    html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <p style="font-size: 16px; color: #374151; margin: 0 0 1rem 0; font-weight: 600;">Bạn có chắc chắn muốn từ chối dịch vụ <strong>"{{ $service->name }}"</strong>?</p>
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
                    width: '520px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("admin.services.reject", $service->slug) }}';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        const noteField = document.createElement('input');
                        noteField.type = 'hidden';
                        noteField.name = 'admin_note';
                        noteField.value = note;
                        form.appendChild(noteField);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endpush

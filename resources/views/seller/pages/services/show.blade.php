@extends('seller.layouts.sidebar')

@section('title', 'Chi tiết dịch vụ - ' . $service->name)

@section('main-content')
    <div class="category-container">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('seller.services.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        @if ($service->admin_note)
            <div class="alert alert-{{ $service->status === \App\Enums\ServiceStatus::REJECTED || $service->status === \App\Enums\ServiceStatus::BANNED ? 'danger' : 'info' }} mb-4">
                <h5><i class="fas fa-sticky-note"></i> Ghi chú từ Admin:</h5>
                <p class="mb-0">{{ $service->admin_note }}</p>
            </div>
        @endif

        <div class="row">
            <!-- Service Info -->
            <div class="col-lg-8">
                <!-- Service Header -->
                <div class="product-show-header mb-4">
                    <div class="product-header-content">
                        <div class="product-image-wrapper" style="cursor: pointer; position: relative;" onclick="document.getElementById('imageInput').click()">
                            @if ($service->image)
                                <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" id="serviceImagePreview">
                            @else
                                <div class="image-placeholder" id="serviceImagePreview">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            <div class="image-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); border-radius: 16px; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; pointer-events: none;">
                                <i class="fas fa-camera" style="color: white; font-size: 24px;"></i>
                            </div>
                            <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
                        </div>
                        <div class="product-info-wrapper">
                            <h1 class="product-title">{{ $service->name }}</h1>
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <i class="fas fa-folder"></i>
                                    <span>{{ $service->serviceSubCategory->serviceCategory->name }} > {{ $service->serviceSubCategory->name }}</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $service->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="product-status-badge status-badge {{ $service->status->value }}">
                                {{ $service->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>

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

                        @if (in_array($service->status, [\App\Enums\ServiceStatus::PENDING, \App\Enums\ServiceStatus::REJECTED]))
                            <div class="product-actions">
                                <a href="{{ route('seller.services.edit', $service) }}" class="btn-modern primary">
                                    <i class="fas fa-edit"></i> Chỉnh sửa dịch vụ
                                </a>
                                @include('components.delete-form', [
                                    'id' => $service->slug,
                                    'route' => route('seller.services.destroy', $service),
                                    'message' => "Bạn có chắc chắn muốn xóa dịch vụ '{$service->name}'?",
                                ])
                            </div>
                        @endif

                        @if (in_array($service->status, [\App\Enums\ServiceStatus::APPROVED, \App\Enums\ServiceStatus::HIDDEN]))
                            <div class="product-actions">
                                <form action="{{ route('seller.services.update-status', $service) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if ($service->status === \App\Enums\ServiceStatus::APPROVED)
                                        <input type="hidden" name="status" value="hidden">
                                        <button type="submit" class="btn-modern warning">
                                            <i class="fas fa-eye-slash"></i> Ẩn dịch vụ
                                        </button>
                                    @else
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-modern primary">
                                            <i class="fas fa-eye"></i> Hiển thị dịch vụ
                                        </button>
                                    @endif
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Variants -->
                <div class="variants-accordion">
                    @foreach ($service->variants as $index => $variant)
                        <div class="variant-card-accordion mb-3">
                            <div class="variant-accordion-header">
                                    <div class="variant-header-content">
                                        <div class="variant-header-info">
                                            <h4 class="variant-name">{{ $variant->name }}</h4>
                                            <div class="variant-badges-compact">
                                                @if ($service->status === \App\Enums\ServiceStatus::APPROVED || $service->status === \App\Enums\ServiceStatus::HIDDEN)
                                                    <span class="variant-badge price" id="price-badge-{{ $variant->id }}">
                                                        {{ number_format($variant->price, 0, ',', '.') }} VNĐ
                                                    </span>
                                                @else
                                                    <span class="variant-badge price">{{ number_format($variant->price, 0, ',', '.') }} VNĐ</span>
                                                @endif
                                                <span class="variant-badge sold">Đã bán: {{ $variant->sold_count }}</span>
                                                <span class="variant-badge status-{{ $variant->status->value }}">
                                                    {{ $variant->status->label() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="variant-header-actions">
                                            @if ($service->status === \App\Enums\ServiceStatus::APPROVED || $service->status === \App\Enums\ServiceStatus::HIDDEN)
                                                <button type="button" class="btn-variant-toggle info" title="Chỉnh sửa giá" onclick="event.stopPropagation(); editVariantPrice('{{ $variant->slug }}', {{ $variant->price }});">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('seller.services.update-variant-status', $variant) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($variant->status === \App\Enums\CommonStatus::ACTIVE)
                                                        <input type="hidden" name="status" value="inactive">
                                                        <button type="submit" class="btn-variant-toggle warning" title="Tắt biến thể">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" class="btn-variant-toggle primary" title="Bật biến thể">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-chart-bar"></i>
                            Tổng quan
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="summary-item-modern">
                            <span class="label">Tổng biến thể:</span>
                            <span class="value">{{ $service->variants->count() }}</span>
                        </div>
                        <div class="summary-item-modern">
                            <span class="label">Tổng đã bán:</span>
                            <span class="value">{{ $service->variants->sum('sold_count') }}</span>
                        </div>
                        <div class="summary-item-modern">
                            <span class="label">Giá thấp nhất:</span>
                            <span class="value price">{{ number_format($service->variants->min('price'), 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="summary-item-modern">
                            <span class="label">Giá cao nhất:</span>
                            <span class="value price">{{ number_format($service->variants->max('price'), 0, ',', '.') }} VNĐ</span>
                        </div>
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
        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Kích thước ảnh tối đa 5MB!'
                });
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Định dạng ảnh không hợp lệ! Chỉ chấp nhận: jpeg, jpg, png, webp.'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.querySelector('.product-image-wrapper');
                if (previewContainer) {
                    let previewImg = document.getElementById('serviceImagePreview');
                    if (!previewImg || previewImg.tagName === 'DIV') {
                        previewContainer.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" id="serviceImagePreview">
                            <div class="image-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); border-radius: 16px; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; pointer-events: none;">
                                <i class="fas fa-camera" style="color: white; font-size: 24px;"></i>
                            </div>
                            <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
                        `;
                    } else {
                        previewImg.src = e.target.result;
                    }
                }
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("seller.services.update-image", $service) }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const previewImg = document.getElementById('serviceImagePreview');
                    if (previewImg && data.image_url) {
                        previewImg.src = data.image_url;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: data.message || 'Đã cập nhật ảnh dịch vụ!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Có lỗi xảy ra khi cập nhật ảnh!'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi cập nhật ảnh!'
                });
            });
        }

        const imageWrapper = document.querySelector('.product-image-wrapper');
        if (imageWrapper) {
            imageWrapper.addEventListener('mouseenter', function() {
                const overlay = this.querySelector('.image-overlay');
                if (overlay) overlay.style.opacity = '1';
            });

            imageWrapper.addEventListener('mouseleave', function() {
                const overlay = this.querySelector('.image-overlay');
                if (overlay) overlay.style.opacity = '0';
            });
        }

        function editVariantPrice(variantSlug, currentPrice) {
            Swal.fire({
                title: 'Chỉnh sửa giá',
                html: `
                    <input type="number" id="variant-price-input" class="swal2-input" value="${currentPrice}" min="0" step="1000" placeholder="Nhập giá mới">
                `,
                showCancelButton: true,
                confirmButtonText: 'Cập nhật',
                cancelButtonText: 'Hủy',
                preConfirm: () => {
                    const price = document.getElementById('variant-price-input').value;
                    if (!price || price < 0) {
                        Swal.showValidationMessage('Vui lòng nhập giá hợp lệ!');
                        return false;
                    }
                    return price;
                },
                didOpen: () => {
                    document.getElementById('variant-price-input').focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('seller.services.update-variant-price', ':slug') }}`.replace(':slug', variantSlug);
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    form.appendChild(methodField);
                    
                    const priceField = document.createElement('input');
                    priceField.type = 'hidden';
                    priceField.name = 'price';
                    priceField.value = result.value;
                    form.appendChild(priceField);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush

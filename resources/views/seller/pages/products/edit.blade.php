@extends('seller.layouts.sidebar')

@section('title', 'Chỉnh sửa sản phẩm - ' . $product->name)

@section('main-content')
    <div class="category-container">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('seller.products.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        @if ($product->status === \App\Enums\ProductStatus::REJECTED && $product->admin_note)
            <div class="alert alert-danger mb-4">
                <h5><i class="fas fa-exclamation-circle"></i> Lý do từ chối:</h5>
                <p class="mb-0">{{ $product->admin_note }}</p>
            </div>
        @endif

        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            id="productForm">
            @csrf
            @method('PUT')
            <div class="row">
                <!-- Product Info -->
                <div class="col-lg-8">
                    <div class="content-card mb-4">
                        <div class="card-top">
                            <h2 class="page-title">Thông tin sản phẩm</h2>
                            <span class="status-badge {{ $product->status->value }}">
                                {{ $product->status->value === 'pending' ? 'Chờ duyệt' : 'Bị từ chối' }}
                            </span>
                        </div>
                        <div class="card-content">
                            <div class="form-group mb-3">
                                <label for="sub_category_id" class="form-label-custom">Danh mục <span
                                        class="required-mark">*</span></label>
                                <select id="sub_category_id" name="sub_category_id" class="custom-select" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($categories as $category)
                                        <optgroup label="{{ $category->name }}">
                                            @foreach ($category->subCategories as $subCategory)
                                                <option value="{{ $subCategory->id }}"
                                                    data-field-name="{{ $subCategory->field_name }}"
                                                    {{ old('sub_category_id', $product->sub_category_id) == $subCategory->id ? 'selected' : '' }}>
                                                    {{ $subCategory->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('sub_category_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="name" class="form-label-custom">Tên sản phẩm <span
                                        class="required-mark">*</span></label>
                                <input type="text" id="name" name="name" class="custom-input"
                                    value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description" class="form-label-custom">Mô tả ngắn</label>
                                <textarea id="description" name="description" class="custom-input" rows="3">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="long_description" class="form-label-custom">Mô tả chi tiết</label>
                                <textarea id="long_description" name="long_description" class="custom-input" rows="6">{{ old('long_description', $product->long_description) }}</textarea>
                                @error('long_description')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Variants -->
                    <div class="content-card">
                        <div class="card-top">
                            <h2 class="page-title">Biến thể sản phẩm</h2>
                            <button type="button" class="action-button" id="addVariantBtn">
                                <i class="fas fa-plus"></i> Thêm biến thể
                            </button>
                        </div>
                        <div class="card-content">
                            <div id="variantsContainer">
                                @foreach ($product->variants as $index => $variant)
                                    <div class="variant-item" data-index="{{ $index }}">
                                        <div class="variant-header">
                                            <span class="variant-number">Biến thể #{{ $index + 1 }}</span>
                                            <button type="button" class="btn btn-sm btn-danger remove-variant-btn"
                                                style="{{ $product->variants->count() <= 1 ? 'display: none;' : '' }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="variants[{{ $index }}][id]"
                                            value="{{ $variant->id }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label-custom">Tên biến thể <span
                                                            class="required-mark">*</span></label>
                                                    <input type="text" name="variants[{{ $index }}][name]"
                                                        class="custom-input"
                                                        value="{{ old("variants.{$index}.name", $variant->name) }}"
                                                        required placeholder="VD: Gói 1 tháng">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label-custom">Giá (VNĐ) <span
                                                            class="required-mark">*</span></label>
                                                    <input type="number" name="variants[{{ $index }}][price]"
                                                        class="custom-input"
                                                        value="{{ old("variants.{$index}.price", $variant->price) }}"
                                                        required min="0" step="1" placeholder="VD: 29900">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label-custom">Field Name <span
                                                            class="required-mark">*</span></label>
                                                    <input type="text" name="variants[{{ $index }}][field_name]"
                                                        class="custom-input variant-field-name"
                                                        value="{{ old("variants.{$index}.field_name", $variant->field_name) }}"
                                                        required placeholder="VD: email|password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Upload & Submit -->
                <div class="col-lg-4">
                    <div class="content-card sticky-top" style="top: 20px;">
                        <div class="card-content">
                            <!-- Image Upload -->
                            <div class="form-group mb-4">
                                <label class="form-label-custom d-block mb-3">Hình ảnh sản phẩm</label>
                                <div class="image-upload-container">
                                    <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/webp" style="display: none;">
                                    <div class="image-upload-area" id="imageUploadArea">
                                        <div class="image-upload-content" id="imageUploadContent" style="{{ $product->image ? 'display: none;' : '' }}">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p class="mb-1">Kéo thả ảnh vào đây</p>
                                            <p class="text-muted small mb-0">hoặc click để chọn</p>
                                        </div>
                                        <img id="imagePreview" class="image-preview" src="{{ $product->image ? Storage::url($product->image) : '' }}" style="{{ $product->image ? 'display: block;' : 'display: none;' }}">
                                        <button type="button" class="btn-remove-image" id="btnRemoveImage" style="{{ $product->image ? 'display: block;' : 'display: none;' }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0 text-center">JPEG, JPG, PNG, WebP - Tối đa 5MB</p>
                                </div>
                                @error('image')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle"></i>
                                Sau khi cập nhật, sản phẩm sẽ được gửi đến admin để duyệt lại.
                            </div>

                            <button type="submit" class="btn action-button w-100">
                                <i class="fas fa-paper-plane"></i> Gửi duyệt lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-form.css')
    @vite('resources/assets/admin/css/product-common.css')
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let variantIndex = {{ $product->variants->count() }};

            // Add variant
            $('#addVariantBtn').click(function() {
                const template = `
                    <div class="variant-item" data-index="${variantIndex}">
                        <div class="variant-header">
                            <span class="variant-number">Biến thể #${variantIndex + 1}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-variant-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Tên biến thể <span class="required-mark">*</span></label>
                                    <input type="text" name="variants[${variantIndex}][name]" class="custom-input" required placeholder="VD: Gói 1 tháng">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Giá (VNĐ) <span class="required-mark">*</span></label>
                                    <input type="number" name="variants[${variantIndex}][price]" class="custom-input" required min="0" step="1" placeholder="VD: 29900">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Field Name <span class="required-mark">*</span></label>
                                    <input type="text" name="variants[${variantIndex}][field_name]" class="custom-input variant-field-name" required placeholder="VD: email|password">
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#variantsContainer').append(template);
                variantIndex++;
                updateRemoveButtons();
            });

            // Remove variant
            $(document).on('click', '.remove-variant-btn', function() {
                $(this).closest('.variant-item').remove();
                updateVariantNumbers();
                updateRemoveButtons();
            });

            // Update variant numbers
            function updateVariantNumbers() {
                $('.variant-item').each(function(index) {
                    $(this).find('.variant-number').text('Biến thể #' + (index + 1));
                });
            }

            // Update remove buttons visibility
            function updateRemoveButtons() {
                const count = $('.variant-item').length;
                if (count > 1) {
                    $('.remove-variant-btn').show();
                } else {
                    $('.remove-variant-btn').hide();
                }
            }

            // Image Upload Handler
            const imageInput = document.getElementById('image');
            const imageUploadArea = document.getElementById('imageUploadArea');
            const imageUploadContent = document.getElementById('imageUploadContent');
            const imagePreview = document.getElementById('imagePreview');
            const btnRemoveImage = document.getElementById('btnRemoveImage');

            // Click to select image
            imageUploadArea.addEventListener('click', function(e) {
                if (e.target !== btnRemoveImage && !btnRemoveImage.contains(e.target)) {
                    imageInput.click();
                }
            });

            // Drag and drop
            imageUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                imageUploadArea.classList.add('dragover');
            });

            imageUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                imageUploadArea.classList.remove('dragover');
            });

            imageUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                imageUploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleImageFile(files[0]);
                }
            });

            // File input change
            imageInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleImageFile(e.target.files[0]);
                }
            });

            // Handle image file
            function handleImageFile(file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Vui lòng chọn file ảnh hợp lệ (JPEG, JPG, PNG, WebP)');
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File ảnh không được vượt quá 5MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imageUploadContent.style.display = 'none';
                    btnRemoveImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }

            // Remove image
            const originalImageSrc = '{{ $product->image ? Storage::url($product->image) : '' }}';
            btnRemoveImage.addEventListener('click', function(e) {
                e.stopPropagation();
                imageInput.value = '';
                // If it's a newly selected image (different from original), remove preview
                if (imagePreview.src !== originalImageSrc && originalImageSrc) {
                    imagePreview.src = originalImageSrc;
                } else if (!originalImageSrc) {
                    // No original image, reset to upload area
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                    imageUploadContent.style.display = 'block';
                    btnRemoveImage.style.display = 'none';
                }
            });
        });
    </script>
@endpush


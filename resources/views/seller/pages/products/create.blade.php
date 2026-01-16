@extends('seller.layouts.sidebar')

@section('title', 'Thêm sản phẩm mới')

@section('main-content')
    <div class="category-container">
        <div class="card-top">
            <h2 class="page-title">Thêm sản phẩm mới</h2>
            <a href="{{ route('seller.products.index') }}" class="btn back-button">
                <i class="fas fa-plus"></i> Quay lại danh sách
            </a>
        </div>
        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            <div class="row">
                <!-- Product Info -->
                <div class="col-lg-8">
                    <div class="content-card mb-4">

                       
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
                                                    {{ old('sub_category_id') == $subCategory->id ? 'selected' : '' }}>
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
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description" class="form-label-custom">Mô tả ngắn</label>
                                <textarea id="description" name="description" class="custom-input" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="long_description" class="form-label-custom">Mô tả chi tiết</label>
                                <textarea id="long_description" name="long_description" class="custom-input" rows="6">{{ old('long_description') }}</textarea>
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
                            <div class="form-hint mb-3">
                                <i class="fas fa-info-circle"></i> Mỗi sản phẩm cần có ít nhất 1 biến thể. Field Name là tên
                                trường dữ liệu (VD: email, password, token...)
                            </div>

                            <div id="variantsContainer">
                                <!-- Variant Template -->
                                <div class="variant-item" data-index="0">
                                    <div class="variant-header">
                                        <span class="variant-number">Biến thể #1</span>
                                        <button type="button" class="btn btn-sm btn-danger remove-variant-btn"
                                            style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-label-custom">Tên biến thể <span
                                                        class="required-mark">*</span></label>
                                                <input type="text" name="variants[0][name]" class="custom-input"
                                                    value="{{ old('variants.0.name') }}" required
                                                    placeholder="VD: Gói 1 tháng">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-label-custom">Giá (VNĐ) <span
                                                        class="required-mark">*</span></label>
                                                <input type="number" name="variants[0][price]" class="custom-input"
                                                    value="{{ old('variants.0.price') }}" required min="0"
                                                    step="1000" placeholder="VD: 50000">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-label-custom">Field Name <span
                                                        class="required-mark">*</span></label>
                                                <input type="text" name="variants[0][field_name]"
                                                    class="custom-input variant-field-name"
                                                    value="{{ old('variants.0.field_name') }}" required
                                                    placeholder="VD: email|password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                        <div class="image-upload-content" id="imageUploadContent">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p class="mb-1">Kéo thả ảnh vào đây</p>
                                            <p class="text-muted small mb-0">hoặc click để chọn</p>
                                        </div>
                                        <img id="imagePreview" class="image-preview" style="display: none;">
                                        <button type="button" class="btn-remove-image" id="btnRemoveImage" style="display: none;">
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
                                Sản phẩm sẽ được gửi đến admin để duyệt. Sau khi được duyệt, bạn mới có thể thêm giá trị
                                (tài khoản) vào sản phẩm.
                            </div>

                            <button type="submit" class="btn action-button w-100">
                                <i class="fas fa-paper-plane"></i> Gửi duyệt sản phẩm
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
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let variantIndex = 1;

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
                                    <input type="number" name="variants[${variantIndex}][price]" class="custom-input" required min="0" step="1000" placeholder="VD: 50000">
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
                updateSuggestedFieldName();
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

            // Suggest field name based on subcategory
            $('#sub_category_id').change(function() {
                updateSuggestedFieldName();
            });

            function updateSuggestedFieldName() {
                const selectedOption = $('#sub_category_id option:selected');
                const fieldName = selectedOption.data('field-name');
                if (fieldName) {
                    $('.variant-field-name').each(function() {
                        if (!$(this).val()) {
                            $(this).val(fieldName);
                        }
                    });
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
            btnRemoveImage.addEventListener('click', function(e) {
                e.stopPropagation();
                imageInput.value = '';
                imagePreview.src = '';
                imagePreview.style.display = 'none';
                imageUploadContent.style.display = 'block';
                btnRemoveImage.style.display = 'none';
            });

            // Initial update
            updateRemoveButtons();
        });
    </script>
@endpush


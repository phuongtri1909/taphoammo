@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa bài viết')

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.shares.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Chỉnh sửa bài viết</h2>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.shares.update', $share) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group mb-4">
                                <label for="title" class="form-label-custom">Tiêu đề <span class="required-mark">*</span></label>
                                <input type="text" id="title" name="title" class="custom-input" value="{{ old('title', $share->title) }}" required>
                                @error('title')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="excerpt" class="form-label-custom">Mô tả ngắn</label>
                                <textarea id="excerpt" name="excerpt" class="custom-textarea custom-input" rows="2">{{ old('excerpt', $share->excerpt) }}</textarea>
                                @error('excerpt')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="content" class="form-label-custom">Nội dung <span class="required-mark">*</span></label>
                                <textarea id="content" name="content" class="custom-textarea custom-input">{{ old('content', $share->content) }}</textarea>
                                @error('content')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($share->isRejected() || $share->status === \App\Enums\ShareStatus::REJECTED)
                                <div class="form-group mb-4">
                                    <label for="rejection_reason" class="form-label-custom">Lý do từ chối</label>
                                    <textarea id="rejection_reason" name="rejection_reason" class="custom-textarea" rows="2">{{ old('rejection_reason', $share->rejection_reason) }}</textarea>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label for="share_category_id" class="form-label-custom">Danh mục <span class="required-mark">*</span></label>
                                <select id="share_category_id" name="share_category_id" class="custom-select" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('share_category_id', $share->share_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('share_category_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="status" class="form-label-custom">Trạng thái <span class="required-mark">*</span></label>
                                <select id="status" name="status" class="custom-select" required>
                                    <option value="{{ \App\Enums\ShareStatus::DRAFT->value }}" {{ old('status', $share->status->value) === \App\Enums\ShareStatus::DRAFT->value ? 'selected' : '' }}>Nháp</option>
                                    <option value="{{ \App\Enums\ShareStatus::PENDING->value }}" {{ old('status', $share->status->value) === \App\Enums\ShareStatus::PENDING->value ? 'selected' : '' }}>Chờ duyệt</option>
                                    <option value="{{ \App\Enums\ShareStatus::APPROVED->value }}" {{ old('status', $share->status->value) === \App\Enums\ShareStatus::APPROVED->value ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="{{ \App\Enums\ShareStatus::REJECTED->value }}" {{ old('status', $share->status->value) === \App\Enums\ShareStatus::REJECTED->value ? 'selected' : '' }}>Từ chối</option>
                                    <option value="{{ \App\Enums\ShareStatus::HIDDEN->value }}" {{ old('status', $share->status->value) === \App\Enums\ShareStatus::HIDDEN->value ? 'selected' : '' }}>Ẩn</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label-custom">Ảnh đại diện</label>
                                <div class="image-upload-wrapper">
                                    <div id="imagePreview" class="image-preview mb-2" style="width: 100%; height: 150px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if ($share->image)
                                            <img src="{{ $share->image_url }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <i class="fas fa-image text-muted" style="font-size: 40px;"></i>
                                        @endif
                                    </div>
                                    <input type="file" id="image" name="image" accept="image/*" class="d-none" onchange="previewImage(this)">
                                    <button type="button" class="btn action-button w-100" onclick="document.getElementById('image').click()">
                                        <i class="fas fa-upload"></i> Đổi ảnh
                                    </button>
                                    <small class="text-muted d-block mt-1">PNG, JPG, GIF, WebP. Tối đa 5MB.</small>
                                </div>
                                @error('image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn action-button w-100">
                                    <i class="fas fa-save"></i> Cập nhật bài viết
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('content', {
            language: 'vi',
            height: 400,
            filebrowserUploadUrl: '{{ route('admin.shares.upload-image') }}?_token={{ csrf_token() }}',
            filebrowserUploadMethod: 'form',
            extraPlugins: 'image2,uploadimage,justify,colorbutton,font',
            removePlugins: 'image',
            image2_alignClasses: ['image-left', 'image-center', 'image-right'],
            image2_disableResizer: false,
            toolbarGroups: [
                { name: 'document', groups: ['mode', 'document', 'doctools'] },
                { name: 'clipboard', groups: ['clipboard', 'undo'] },
                { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                { name: 'forms' },
                '/',
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
                { name: 'links' },
                { name: 'insert' },
                '/',
                { name: 'styles' },
                { name: 'colors' },
                { name: 'tools' },
                { name: 'others' }
            ]
        });

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush

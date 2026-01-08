@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa SEO')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">Quản lý SEO</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa SEO</h5>
                    <small class="text-muted">Cập nhật SEO settings</small>
                </div>
                <div class="category-meta">
                    <div class="category-created">
                        <i class="fas fa-globe"></i>
                        <span>Trang: {{ $pageKeys[$seo->page_key] ?? $seo->page_key }}</span>
                    </div>
                    <div class="category-created">
                        <i class="fas fa-clock"></i>
                        <span>Ngày tạo: {{ $seo->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
            <div class="form-body">

                <form action="{{ route('admin.seo.update', $seo) }}" method="POST" class="category-form"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="form-group">
                        <label for="title" class="form-label-custom">
                            Title <span class="required-mark">*</span>
                        </label>
                        <input type="text" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}"
                            id="title" name="title" value="{{ old('title', $seo->title) }}" required maxlength="255">
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Tiêu đề trang (tối đa 255 ký tự).</span>
                        </div>
                        @error('title')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label-custom">
                            Description <span class="required-mark">*</span>
                        </label>
                        <textarea class="custom-input {{ $errors->has('description') ? 'input-error' : '' }}" id="description"
                            name="description" rows="3" required maxlength="500">{{ old('description', $seo->description) }}</textarea>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Mô tả trang (tối đa 500 ký tự).</span>
                        </div>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Keywords -->
                    <div class="form-group">
                        <label for="keywords" class="form-label-custom">
                            Keywords <span class="required-mark">*</span>
                        </label>
                        <input type="text" class="custom-input {{ $errors->has('keywords') ? 'input-error' : '' }}"
                            id="keywords" name="keywords" value="{{ old('keywords', $seo->keywords) }}" required
                            maxlength="500">
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Từ khóa, phân cách bằng dấu phẩy (tối đa 500 ký tự).</span>
                        </div>
                        @error('keywords')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="thumbnail" class="form-label-custom">
                            Thumbnail
                        </label>
                        @if ($seo->thumbnail)
                            <div class="current-thumbnail mb-3">
                                <img src="{{ $seo->thumbnail_url }}" alt="Thumbnail hiện tại"
                                    style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                            </div>
                        @endif
                        <input type="file" class="custom-input {{ $errors->has('thumbnail') ? 'input-error' : '' }}"
                            id="thumbnail" name="thumbnail" accept="image/*">
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Để trống nếu không muốn thay đổi thumbnail. Chấp nhận định dạng: JPG, PNG, GIF. Tối đa
                                2MB.</span>
                        </div>
                        @error('thumbnail')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $seo->is_active) ? 'checked' : '' }}>
                            <label for="is_active">Kích hoạt SEO</label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.seo.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>

                        <div class="action-group">

                            <button type="submit" class="save-button">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .current-thumbnail {
            text-align: center;
        }
    </style>
@endpush

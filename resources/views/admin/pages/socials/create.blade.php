@extends('admin.layouts.sidebar')

@section('title', 'Thêm mạng xã hội')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.socials.index') }}">Mạng xã hội</a></li>
            <li class="breadcrumb-item current">Thêm mạng xã hội</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-plus-circle icon-title"></i>
                <h5>Thêm mạng xã hội mới</h5>
            </div>
        </div>
        <div class="form-body">
            <form action="{{ route('admin.socials.store') }}" method="POST" class="category-form">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label-custom">
                        Tên mạng xã hội <span class="required-mark">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="custom-input @error('name') input-error @enderror" 
                        placeholder="Ví dụ: Facebook" value="{{ old('name') }}">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="url" class="form-label-custom">
                        Đường dẫn <span class="required-mark">*</span>
                    </label>
                    <input type="text" id="url" name="url" class="custom-input @error('url') input-error @enderror" 
                        placeholder="Ví dụ: https://www.facebook.com/tencuaban" value="{{ old('url') }}">
                    @error('url')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i> Có thể sử dụng URL thông thường (https://...) hoặc định dạng đặc biệt như mailto:email@domain.com, tel:+1234567890
                    </div>
                </div>

                <div class="form-group">
                    <label for="icon" class="form-label-custom">
                        Icon <span class="required-mark">*</span>
                    </label>
                    <select id="icon" name="icon" class="custom-select @error('icon') input-error @enderror" required>
                        <option value="">Chọn icon</option>
                        @foreach($fontAwesomeIcons as $iconClass => $iconName)
                            <option value="{{ $iconClass }}" data-icon="{{ $iconClass }}" {{ old('icon') === $iconClass ? 'selected' : '' }}>
                                {{ $iconName }}
                            </option>
                        @endforeach
                    </select>
                    @error('icon')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="sort_order" class="form-label-custom">
                        Thứ tự hiển thị
                    </label>
                    <input type="number" id="sort_order" name="sort_order" class="custom-input @error('sort_order') input-error @enderror" 
                        value="{{ old('sort_order', 0) }}" min="0">
                    @error('sort_order')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i> Số thứ tự càng thấp sẽ hiển thị càng trước
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-switch-wrapper">
                        <input type="checkbox" id="is_active" name="is_active" class="custom-switch" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="custom-switch-label">Hiển thị</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label-custom">
                        Xem trước icon
                    </label>
                    <div class="icon-preview-container">
                        <div class="icon-preview" id="iconPreview"></div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.socials.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Lưu lại
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-preview-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .icon-preview {
        width: 80px;
        height: 80px;
        border: 1px solid #e0e0e0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        background-color: #f8f9fa;
    }
    
    /* Custom Zalo Icon */
    .custom-zalo {
        display: inline-block;
        width: 1em;
        height: 1em;
        background-image: url("https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Icon_of_Zalo.svg/50px-Icon_of_Zalo.svg.png");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Hiển thị icon khi chọn
    $('#icon').on('change', function() {
        const iconClass = $(this).find(':selected').data('icon');
        const iconName = $(this).find(':selected').text().trim();
        
        // Hiển thị icon preview
        if (iconClass) {
            if (iconClass.startsWith('custom-')) {
                $('#iconPreview').html(`<span class="${iconClass}"></span>`);
            } else {
                $('#iconPreview').html(`<i class="${iconClass}"></i>`);
            }
        } else {
            $('#iconPreview').html('');
        }
        
        // Gợi ý URL dựa trên icon được chọn
        const urlInput = $('#url');
        if (!urlInput.val() || urlInput.data('auto-filled')) {
            let suggestedUrl = '';
            
            switch(iconName) {
                case 'Facebook':
                    suggestedUrl = 'https://www.facebook.com/';
                    break;
                case 'Instagram':
                    suggestedUrl = 'https://www.instagram.com/';
                    break;
                case 'Twitter':
                    suggestedUrl = 'https://twitter.com/';
                    break;
                case 'LinkedIn':
                    suggestedUrl = 'https://www.linkedin.com/in/';
                    break;
                case 'YouTube':
                    suggestedUrl = 'https://www.youtube.com/channel/';
                    break;
                case 'TikTok':
                    suggestedUrl = 'https://www.tiktok.com/@';
                    break;
                case 'Pinterest':
                    suggestedUrl = 'https://www.pinterest.com/';
                    break;
                case 'Email (mailto:)':
                    suggestedUrl = 'mailto:contact@domain.com';
                    break;
                case 'Phone (tel:)':
                    suggestedUrl = 'tel:+0123456789';
                    break;
                case 'SMS (sms:)':
                    suggestedUrl = 'sms:+0123456789';
                    break;
                case 'Website':
                    suggestedUrl = 'https://www.';
                    break;
                default:
                    suggestedUrl = '';
            }
            
            if (suggestedUrl) {
                urlInput.val(suggestedUrl).data('auto-filled', true);
            }
        }
    });
    
    // Reset auto-filled flag khi user bắt đầu chỉnh sửa
    $('#url').on('input', function() {
        $(this).data('auto-filled', false);
    });
    
    // Khởi tạo hiển thị icon nếu đã chọn
    const initialIcon = $('#icon').find(':selected').data('icon');
    if (initialIcon) {
        if (initialIcon.startsWith('custom-')) {
            $('#iconPreview').html(`<span class="${initialIcon}"></span>`);
        } else {
            $('#iconPreview').html(`<i class="${initialIcon}"></i>`);
        }
    }
});
</script>
@endpush
@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa Logo Site')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Cấu hình Logo và Favicon</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-image icon-title"></i>
                <h5>Cấu hình Logo và Favicon</h5>
                <small class="text-muted">Quản lý logo và favicon cho website</small>
            </div>
        </div>
        <div class="form-body">
            
            <form action="{{ route('admin.logo-site.update') }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Logo Section -->
                <div class="form-group">
                    <label for="logo" class="form-label-custom">
                        Logo Trang Web
                    </label>
                    <input type="file" class="custom-input {{ $errors->has('logo') ? 'input-error' : '' }}" 
                        id="logo" name="logo" accept="image/*">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Chiều cao sẽ được điều chỉnh thành 50px. Hình ảnh sẽ được chuyển đổi thành định dạng WebP để tối ưu.</span>
                    </div>
                    @error('logo')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                @if (isset($logoSite) && $logoSite->logo)
                    <div class="form-group">
                        <label class="form-label-custom">Logo hiện tại</label>
                        <div class="current-thumbnail mb-3">
                            <img src="{{ Storage::url($logoSite->logo) }}" alt="Logo hiện tại" 
                                style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteLogo()">
                                <i class="fas fa-trash"></i> Xóa Logo
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Favicon Section -->
                <div class="form-group">
                    <label for="favicon" class="form-label-custom">
                        Favicon
                    </label>
                    <input type="file" class="custom-input {{ $errors->has('favicon') ? 'input-error' : '' }}" 
                        id="favicon" name="favicon" accept="image/*,image/x-icon">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Favicon sẽ được điều chỉnh về kích thước 32x32px và chuyển đổi thành định dạng WebP. Đề xuất dùng hình ảnh vuông.</span>
                    </div>
                    @error('favicon')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                @if (isset($logoSite) && $logoSite->favicon)
                    <div class="form-group">
                        <label class="form-label-custom">Favicon hiện tại</label>
                        <div class="current-thumbnail mb-3">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="width: 32px; height: 32px; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ Storage::url($logoSite->favicon) }}" alt="Favicon" style="max-width: 30px; max-height: 30px;">
                                </div>
                                <div style="width: 64px; height: 64px; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ Storage::url($logoSite->favicon) }}" alt="Favicon" style="max-width: 60px; max-height: 60px;">
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Kích thước thực (32x32) và phóng to 2x (64x64)</small>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteFavicon()">
                                <i class="fas fa-trash"></i> Xóa Favicon
                            </button>
                        </div>
                    </div>
                @endif
                
                <div class="form-actions">
                    <a href="{{ route('admin.dashboard') }}" class="back-button">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteLogo() {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc chắn muốn xóa logo này? Logo sẽ được thay thế bằng logo mặc định.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.logo-site.delete-logo') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function deleteFavicon() {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc chắn muốn xóa favicon này? Favicon sẽ được thay thế bằng favicon mặc định.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.logo-site.delete-favicon') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush

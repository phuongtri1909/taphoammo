@extends('seller.layouts.sidebar')

@section('title', 'Dịch vụ của tôi')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Dịch vụ của tôi</h2>
                <a href="{{ route('seller.services.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm dịch vụ
                </a>
            </div>
            <div class="card-content">
                <form action="{{ route('seller.services.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}"
                                        {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('seller.services.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($services->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <h4>Bạn chưa có dịch vụ nào</h4>
                        <p>Tạo dịch vụ đầu tiên để bắt đầu</p>
                        <a href="{{ route('seller.services.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm dịch vụ
                        </a>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Dịch vụ</th>
                                    <th class="column-medium">Danh mục</th>
                                    <th class="column-small text-center">Biến thể</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($services as $key => $service)
                                    <tr class="text-center">
                                        <td>{{ $services->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @if (in_array($service->status, [\App\Enums\ServiceStatus::PENDING, \App\Enums\ServiceStatus::REJECTED]))
                                                    <a href="{{ route('seller.services.edit', $service) }}"
                                                        class="action-icon edit-icon border-0" title="Chỉnh sửa">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                @endif
                                                @if (!$service->hasOrders())
                                                    @include('components.delete-form', [
                                                        'id' => $service->slug,
                                                        'route' => route('seller.services.destroy', $service),
                                                        'message' => "Bạn có chắc chắn muốn xóa dịch vụ '{$service->name}'?",
                                                    ])
                                                @endif
                                                @if ($service->status === \App\Enums\ServiceStatus::APPROVED)
                                                    <a href="{{ route('seller.services.show', $service) }}"
                                                        class="action-icon view-icon border-0" title="Quản lý">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('seller.services.show', $service) }}"
                                                        class="action-icon view-icon" title="Xem">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                @if ($service->image)
                                                    <img src="{{ Storage::url($service->image) }}"
                                                        alt="{{ $service->name }}" class="product-thumb">
                                                @else
                                                    <div class="product-thumb-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                                <div class="product-details">
                                                    <span class="item-name">{{ $service->name }}</span>
                                                    <small
                                                        class="text-muted d-block">{{ Str::limit($service->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary">{{ $service->serviceSubCategory->serviceCategory->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $service->serviceSubCategory->name }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $service->variants->count() }}</span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $service->status->value }}">
                                                {{ $service->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $services->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush

<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Đổi ảnh dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="imageForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Chọn ảnh mới</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <small class="text-muted">Định dạng: jpeg, jpg, png, webp. Tối đa 5MB.</small>
                    </div>
                    <div id="imagePreview" class="text-center mb-3" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openImageModal(slug, name) {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        const form = document.getElementById('imageForm');
        form.action = '{{ route("seller.services.update-image", ":slug") }}'.replace(':slug', slug);
        document.getElementById('imageModalLabel').textContent = `Đổi ảnh: ${name}`;
        document.getElementById('image').value = '';
        document.getElementById('imagePreview').style.display = 'none';
        modal.show();
    }

    document.getElementById('image')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

@extends('admin.layouts.sidebar')

@section('title', 'Quản lý danh mục chia sẻ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh mục chia sẻ</h2>
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i> Thêm danh mục
                </button>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.share-categories.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm danh mục..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('admin.share-categories.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($categories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                        <h4>Chưa có danh mục nào</h4>
                        <p>Thêm danh mục để phân loại bài viết chia sẻ.</p>
                        <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus"></i> Thêm danh mục
                        </button>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Tên danh mục</th>
                                    <th class="column-medium">Mô tả</th>
                                    <th class="column-small text-center">Số bài viết</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($categories as $key => $category)
                                    <tr>
                                        <td>{{ $categories->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon border-0" data-bs-toggle="modal"
                                                    data-bs-target="#editCategoryModal{{ $category->id }}" title="Chỉnh sửa">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                @if ($category->shares_count == 0)
                                                    @include('components.delete-form', [
                                                        'id' => $category->id,
                                                        'route' => route('admin.share-categories.destroy', $category),
                                                        'message' => 'Bạn có chắc chắn muốn xóa danh mục này?',
                                                    ])
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <strong>{{ $category->name }}</strong>
                                            <div class="text-muted small">{{ $category->slug }}</div>
                                        </td>
                                        <td class="text-start">{{ Str::limit($category->description, 50) }}</td>
                                        <td>{{ $category->shares_count }}</td>
                                        <td>{{ $category->order }}</td>
                                        <td>
                                            <span class="status-badge {{ $category->is_active ? 'active' : 'inactive' }}">
                                                {{ $category->is_active ? 'Hiển thị' : 'Ẩn' }}
                                            </span>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa danh mục</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.share-categories.update', $category->slug) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label class="form-label-custom">Tên danh mục <span class="required-mark">*</span></label>
                                                            <input type="text" name="name" class="custom-input" value="{{ $category->name }}" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="form-label-custom">Mô tả</label>
                                                            <textarea name="description" class="custom-textarea custom-input" rows="3">{{ $category->description }}</textarea>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="form-label-custom">Thứ tự</label>
                                                            <input type="number" name="order" class="custom-input" value="{{ $category->order }}" min="0">
                                                        </div>
                                                        <div class="form-group form-check form-switch mb-3">
                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label">Hiển thị</label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn back-button" data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn action-button">Lưu thay đổi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper">
                            {{ $categories->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title color-primary-6">Thêm danh mục mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.share-categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Tên danh mục <span class="required-mark">*</span></label>
                            <input type="text" name="name" class="custom-input" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Mô tả</label>
                            <textarea name="description" class="custom-textarea custom-input" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Thứ tự</label>
                            <input type="number" name="order" class="custom-input" value="{{ old('order', 0) }}" min="0">
                        </div>
                        <div class="form-group form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">Hiển thị</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn back-button" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn action-button">Thêm mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

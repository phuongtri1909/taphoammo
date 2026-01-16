@extends('admin.layouts.sidebar')

@section('title', 'Quản lý danh mục')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách danh mục</h2>
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i> Thêm danh mục
                </button>
            </div>
            <div class="card-content">
                @if ($categories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h4>Chưa có danh mục nào</h4>
                        <p>Thêm danh mục để bắt đầu quản lý sản phẩm</p>
                        <button type="button" class="action-button" data-bs-toggle="modal"
                            data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus"></i> Thêm danh mục
                        </button>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center text-sm">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Tên danh mục</th>
                                    <th class="column-medium">Slug</th>
                                    <th class="column-small text-center">Danh mục con</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-sm">
                                @foreach ($categories as $key => $category)
                                    <tr class="text-center">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editCategoryModal{{ $category->id }}"
                                                    title="Chỉnh sửa" style="border: none;">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                @if ($category->sub_categories_count === 0)
                                                    @include('components.delete-form', [
                                                        'id' => $category->slug,
                                                        'route' => route('admin.categories.destroy', $category),
                                                        'message' => "Bạn có chắc chắn muốn xóa danh mục '{$category->name}'?",
                                                    ])
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $category->name }}</span>
                                        </td>
                                        <td>
                                            <code class="slug-text">{{ $category->slug }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->sub_categories_count }}</span>
                                        </td>
                                        <td>{{ $category->order }}</td>
                                        <td>
                                            <span class="status-badge {{ $category->status->value }}">
                                                {{ $category->status->label() }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Edit Category Modal -->
                                    <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa danh mục</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.categories.update', $category) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label for="name{{ $category->id }}"
                                                                class="form-label-custom">Tên danh mục <span
                                                                    class="required-mark">*</span></label>
                                                            <input type="text" id="name{{ $category->id }}" name="name"
                                                                class="custom-input" value="{{ $category->name }}"
                                                                required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="description{{ $category->id }}"
                                                                class="form-label-custom">Mô tả</label>
                                                            <textarea id="description{{ $category->id }}" name="description" class="custom-input" rows="3">{{ $category->description }}</textarea>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="order{{ $category->id }}"
                                                                        class="form-label-custom">Thứ tự</label>
                                                                    <input type="number" id="order{{ $category->id }}"
                                                                        name="order" class="custom-input"
                                                                        value="{{ $category->order }}" min="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="status{{ $category->id }}"
                                                                        class="form-label-custom">Trạng thái <span
                                                                            class="required-mark">*</span></label>
                                                                    <select id="status{{ $category->id }}" name="status"
                                                                        class="custom-select" required>
                                                                        <option value="active"
                                                                            {{ $category->status->value === 'active' ? 'selected' : '' }}>
                                                                            Hoạt động</option>
                                                                        <option value="inactive"
                                                                            {{ $category->status->value === 'inactive' ? 'selected' : '' }}>
                                                                            Không hoạt động</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn back-button"
                                                            data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn action-button">Cập nhật</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title color-primary-6">Thêm danh mục mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label-custom">Tên danh mục <span
                                    class="required-mark">*</span></label>
                            <input type="text" id="name" name="name" class="custom-input" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label-custom">Mô tả</label>
                            <textarea id="description" name="description" class="custom-input" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="order" class="form-label-custom">Thứ tự</label>
                                    <input type="number" id="order" name="order" class="custom-input" value="0"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label-custom">Trạng thái <span
                                            class="required-mark">*</span></label>
                                    <select id="status" name="status" class="custom-select" required>
                                        <option value="active" selected>Hoạt động</option>
                                        <option value="inactive">Không hoạt động</option>
                                    </select>
                                </div>
                            </div>
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

@push('styles')
    <style>
        .slug-text {
            background-color: #f8f9fa;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
@endpush


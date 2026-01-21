@extends('admin.layouts.sidebar')

@section('title', 'Quản lý danh mục con dịch vụ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách danh mục con dịch vụ</h2>
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addSubCategoryModal">
                    <i class="fas fa-plus"></i> Thêm danh mục con
                </button>
            </div>
            <div class="card-content">
                @if ($subCategories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h4>Chưa có danh mục con nào</h4>
                        <p>Thêm danh mục con để phân loại dịch vụ chi tiết hơn</p>
                        <button type="button" class="action-button" data-bs-toggle="modal"
                            data-bs-target="#addSubCategoryModal">
                            <i class="fas fa-plus"></i> Thêm danh mục con
                        </button>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center text-sm">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Danh mục cha</th>
                                    <th class="column-medium">Tên danh mục con</th>
                                    <th class="column-small text-center">Dịch vụ</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-sm">
                                @foreach ($subCategories as $key => $subCategory)
                                    <tr class="text-center">
                                        <td>{{ $subCategories->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editSubCategoryModal{{ $subCategory->id }}"
                                                    title="Chỉnh sửa" style="border: none;">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                @if ($subCategory->services_count === 0)
                                                    @include('components.delete-form', [
                                                        'id' => $subCategory->slug,
                                                        'route' => route('admin.service-subcategories.destroy', $subCategory),
                                                        'message' => "Bạn có chắc chắn muốn xóa danh mục con '{$subCategory->name}'?",
                                                    ])
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $subCategory->serviceCategory->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="item-name text-sm">{{ $subCategory->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $subCategory->services_count }}</span>
                                        </td>
                                        <td>{{ $subCategory->order }}</td>
                                        <td>
                                            <span class="status-badge {{ $subCategory->status->value }}">
                                                {{ $subCategory->status->label() }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Edit SubCategory Modal -->
                                    <div class="modal fade" id="editSubCategoryModal{{ $subCategory->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa danh mục con</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form
                                                    action="{{ route('admin.service-subcategories.update', $subCategory) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label for="service_category_id{{ $subCategory->id }}"
                                                                class="form-label-custom">Danh mục cha <span
                                                                    class="required-mark">*</span></label>
                                                            <select id="service_category_id{{ $subCategory->id }}"
                                                                name="service_category_id" class="custom-select" required>
                                                                <option value="">Chọn danh mục cha</option>
                                                                @foreach ($categories as $category)
                                                                    <option value="{{ $category->id }}"
                                                                        {{ $subCategory->service_category_id === $category->id ? 'selected' : '' }}>
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="name{{ $subCategory->id }}"
                                                                class="form-label-custom">Tên danh mục con <span
                                                                    class="required-mark">*</span></label>
                                                            <input type="text" id="name{{ $subCategory->id }}"
                                                                name="name" class="custom-input"
                                                                value="{{ $subCategory->name }}" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="description{{ $subCategory->id }}"
                                                                class="form-label-custom">Mô tả</label>
                                                            <textarea id="description{{ $subCategory->id }}" name="description" class="custom-input" rows="3">{{ $subCategory->description }}</textarea>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="order{{ $subCategory->id }}"
                                                                        class="form-label-custom">Thứ tự</label>
                                                                    <input type="number"
                                                                        id="order{{ $subCategory->id }}" name="order"
                                                                        class="custom-input"
                                                                        value="{{ $subCategory->order }}" min="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="status{{ $subCategory->id }}"
                                                                        class="form-label-custom">Trạng thái <span
                                                                            class="required-mark">*</span></label>
                                                                    <select id="status{{ $subCategory->id }}"
                                                                        name="status" class="custom-select" required>
                                                                        <option value="active"
                                                                            {{ $subCategory->status->value === 'active' ? 'selected' : '' }}>
                                                                            Hoạt động</option>
                                                                        <option value="inactive"
                                                                            {{ $subCategory->status->value === 'inactive' ? 'selected' : '' }}>
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
                    <div class="mt-4">
                        {{ $subCategories->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add SubCategory Modal -->
    <div class="modal fade" id="addSubCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title color-primary-6">Thêm danh mục con dịch vụ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.service-subcategories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="service_category_id" class="form-label-custom">Danh mục cha <span
                                    class="required-mark">*</span></label>
                            <select id="service_category_id" name="service_category_id" class="custom-select" required>
                                <option value="">Chọn danh mục cha</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label-custom">Tên danh mục con <span
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

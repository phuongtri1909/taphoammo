@extends('admin.layouts.sidebar')

@section('title', 'Điều khoản sử dụng')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Điều khoản sử dụng</h2>
            </div>

            <div class="card-content">

                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr class="text-center">
                                <th class="column-stt text-center">STT</th>
                                <th class="column-small text-center">Thao tác</th>
                                <th class="column-medium">Tiêu đề</th>
                                <th class="column-small text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="action-buttons-wrapper">
                                        <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                            data-bs-target="#editModal" title="Chỉnh sửa"
                                            style="border: none;">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-name">{{ $terms->title }}</span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $terms->is_active ? 'active' : 'inactive' }}">
                                        {{ $terms->is_active ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content modal-content-custom">
                            <div class="modal-header">
                                <h5 class="modal-title color-primary-6">Chỉnh sửa điều khoản sử dụng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.terms-of-service.update', $terms) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="title" class="form-label-custom">
                                            Tiêu đề <span class="required-mark">*</span>
                                        </label>
                                        <input type="text" id="title" name="title"
                                            class="custom-input form-control"
                                            value="{{ old('title', $terms->title) }}" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom d-flex align-items-center">
                                            <input type="checkbox" name="is_active" value="1" class="me-2"
                                                {{ $terms->is_active ? 'checked' : '' }}>
                                            Kích hoạt
                                        </label>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="summary" class="form-label-custom">
                                            Tóm tắt (hiển thị trong popup)
                                        </label>
                                        <textarea id="summary" name="summary"
                                            class="custom-input form-control" rows="3"
                                            placeholder="Tóm tắt điều khoản hiển thị trong popup xác nhận..."
                                            maxlength="500">{{ old('summary', $terms->summary) }}</textarea>
                                        <small class="form-text text-muted">Tối đa 500 ký tự</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="content" class="form-label-custom">
                                            Nội dung đầy đủ <span class="required-mark">*</span>
                                        </label>
                                        <textarea id="content" name="content"
                                            class="custom-input form-control" rows="15" required>{{ old('content', $terms->content) }}</textarea>
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
            </div>
        </div>
    </div>
@endsection

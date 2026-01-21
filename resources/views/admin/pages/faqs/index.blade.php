@extends('admin.layouts.sidebar')

@section('title', 'Quản lý FAQ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách FAQ</h2>
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Thêm FAQ
                </button>
            </div>

            <div class="card-content">
                <form action="{{ route('admin.faqs.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm theo câu hỏi, câu trả lời..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.faqs.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($faqs->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h4>Chưa có FAQ nào</h4>
                        <p>Thêm FAQ để hiển thị trên website của bạn</p>
                        <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus"></i> Thêm FAQ
                        </button>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-large">Câu hỏi</th>
                                    <th class="column-extra-large">Câu trả lời</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($faqs as $key => $faq)
                                    <tr>
                                        <td>{{ $faqs->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $faq->id }}" title="Chỉnh sửa"
                                                    style="border: none;">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                @include('components.delete-form', [
                                                    'id' => $faq->id,
                                                    'route' => route('admin.faqs.destroy', $faq),
                                                    'message' => "Bạn có chắc chắn muốn xóa FAQ này?",
                                                ])
                                            </div>
                                        </td>
                                        <td>{{ $faq->order }}</td>
                                        <td class="text-start">
                                            <span class="item-name">{{ Str::limit($faq->question, 80) }}</span>
                                        </td>
                                        <td class="text-start">
                                            <span>{{ Str::limit(strip_tags($faq->answer), 100) }}</span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $faq->is_active ? 'active' : 'inactive' }}">
                                                {{ $faq->is_active ? 'Hiển thị' : 'Ẩn' }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $faq->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa FAQ</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label for="question{{ $faq->id }}" class="form-label-custom">
                                                                Câu hỏi <span class="required-mark">*</span>
                                                            </label>
                                                            <input type="text" id="question{{ $faq->id }}" name="question"
                                                                class="custom-input form-control" 
                                                                value="{{ old('question', $faq->question) }}" required maxlength="500">
                                                            <small class="text-muted">Tối đa 500 ký tự</small>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="answer{{ $faq->id }}" class="form-label-custom">
                                                                Câu trả lời <span class="required-mark">*</span>
                                                            </label>
                                                            <textarea id="answer{{ $faq->id }}" name="answer"
                                                                class="custom-textarea custom-input" rows="6" required maxlength="5000">{{ old('answer', $faq->answer) }}</textarea>
                                                            <small class="text-muted">Tối đa 5000 ký tự</small>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="order{{ $faq->id }}" class="form-label-custom">Thứ tự</label>
                                                            <input type="number" id="order{{ $faq->id }}" name="order"
                                                                class="custom-input" value="{{ old('order', $faq->order) }}" min="0">
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" name="is_active" class="form-check-input"
                                                                    {{ old('is_active', $faq->is_active) ? 'checked' : '' }} value="1">
                                                                Hiển thị
                                                            </label>
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
                    </div>

                    <div class="mt-3">
                        {{ $faqs->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title color-primary-6">Thêm FAQ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.faqs.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="question" class="form-label-custom">
                                Câu hỏi <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="question" name="question" class="custom-input form-control" required maxlength="500">
                            <small class="text-muted">Tối đa 500 ký tự</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="answer" class="form-label-custom">
                                Câu trả lời <span class="required-mark">*</span>
                            </label>
                            <textarea id="answer" name="answer" class="custom-textarea custom-input" rows="6" required maxlength="5000"></textarea>
                            <small class="text-muted">Tối đa 5000 ký tự</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="order" class="form-label-custom">Thứ tự</label>
                            <input type="number" id="order" name="order" class="custom-input" value="0" min="0">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_active" class="form-check-input" checked value="1">
                                Hiển thị
                            </label>
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

@extends('admin.layouts.sidebar')

@section('title', 'Quản lý liên kết liên hệ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách liên kết liên hệ</h2>
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Thêm liên kết
                </button>
            </div>

            <div class="card-content">
                @if ($links->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <h4>Chưa có liên kết liên hệ nào</h4>
                        <p>Thêm liên kết liên hệ để hiển thị trên website của bạn</p>
                        <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus"></i> Thêm liên kết
                        </button>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-small">Icon</th>
                                    <th class="column-medium">Tên</th>
                                    <th class="column-large">Đường dẫn</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($links as $key => $link)
                                    <tr>
                                        <td>{{ ($links->currentPage() - 1) * $links->perPage() + $key + 1 }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $link->id }}" title="Chỉnh sửa"
                                                    style="border: none;">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                @include('components.delete-form', [
                                                    'id' => $link->id,
                                                    'route' => route('admin.contact-links.destroy', $link),
                                                    'message' => "Bạn có chắc chắn muốn xóa liên kết '{$link->name}'?",
                                                ])
                                            </div>
                                        </td>
                                        <td>
                                            <div class="social-icon-preview">
                                                @if ($link->icon)
                                                    <i class="{{ $link->icon }}"></i>
                                                @else
                                                    <i class="fas fa-link"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $link->name }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ $link->url }}" target="_blank" class="social-link">
                                                {{ Str::limit($link->url, 50) }}
                                                <i class="fas fa-external-link-alt link-icon"></i>
                                            </a>
                                        </td>
                                        <td>{{ $link->order }}</td>
                                        <td>
                                            <span class="status-badge {{ $link->is_active ? 'active' : 'inactive' }}">
                                                {{ $link->is_active ? 'Hiển thị' : 'Ẩn' }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa liên kết liên hệ</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.contact-links.update', $link) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label for="name{{ $link->id }}" class="form-label-custom">
                                                                Tên liên kết <span class="required-mark">*</span>
                                                            </label>
                                                            <input type="text" id="name{{ $link->id }}" name="name"
                                                                class="custom-input form-control" value="{{ old('name', $link->name) }}" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="url{{ $link->id }}" class="form-label-custom">
                                                                Đường dẫn <span class="required-mark">*</span>
                                                            </label>
                                                            <input type="text" id="url{{ $link->id }}" name="url"
                                                                class="custom-input" value="{{ old('url', $link->url) }}" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="iconSelect{{ $link->id }}" class="form-label-custom">Icon</label>
                                                            @php
                                                                $isCustomIcon = $link->icon && !in_array($link->icon, array_keys($fontAwesomeIcons));
                                                            @endphp
                                                            <select id="iconSelect{{ $link->id }}" name="icon_select"
                                                                class="form-select icon-select custom-select mb-2"
                                                                data-preview="iconPreview{{ $link->id }}"
                                                                data-icon-input="iconInput{{ $link->id }}">
                                                                <option value="">Chọn icon</option>
                                                                @foreach ($fontAwesomeIcons as $iconClass => $iconName)
                                                                    <option value="{{ $iconClass }}"
                                                                        {{ $link->icon === $iconClass ? 'selected' : '' }}>
                                                                        {{ $iconName }}
                                                                    </option>
                                                                @endforeach
                                                                <option value="__custom__" {{ $isCustomIcon ? 'selected' : '' }}>Tự nhập icon</option>
                                                            </select>
                                                            <div id="iconInputWrapper{{ $link->id }}" class="{{ $isCustomIcon ? '' : 'd-none' }}">
                                                                <input type="text" id="iconInput{{ $link->id }}"
                                                                    class="custom-input mb-2" 
                                                                    value="{{ old('icon', $isCustomIcon ? $link->icon : '') }}"
                                                                    placeholder="Ví dụ: fas fa-envelope, fab fa-facebook, fas fa-comment-dots">
                                                                <small class="text-muted d-block">
                                                                    <i class="fas fa-info-circle"></i> 
                                                                    <a href="https://fontawesome.com/search" target="_blank" class="text-primary">
                                                                        Tìm icon tại FontAwesome <i class="fas fa-external-link-alt" style="font-size: 0.7em;"></i>
                                                                    </a>
                                                                    <span class="ms-2">| Ví dụ: <code>fas fa-envelope</code>, <code>fab fa-facebook</code></span>
                                                                </small>
                                                            </div>
                                                            <input type="hidden" id="iconHidden{{ $link->id }}" name="icon" 
                                                                value="{{ old('icon', $link->icon ?? '') }}">
                                                            <div class="icon-preview-container text-center mt-2">
                                                                <small class="text-muted d-block mb-1">Xem trước:</small>
                                                                <div class="icon-preview" id="iconPreview{{ $link->id }}">
                                                                    @if ($link->icon)
                                                                        <i class="{{ $link->icon }}"></i>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="order{{ $link->id }}" class="form-label-custom">Thứ tự</label>
                                                            <input type="number" id="order{{ $link->id }}" name="order"
                                                                class="custom-input" value="{{ old('order', $link->order) }}" min="0">
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" name="is_active" class="form-check-input"
                                                                    {{ old('is_active', $link->is_active) ? 'checked' : '' }}>
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
                        {{ $links->appends(request()->query())->links('components.paginate') }}
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
                    <h5 class="modal-title color-primary-6">Thêm liên kết liên hệ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.contact-links.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label-custom">
                                Tên liên kết <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="custom-input form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="url" class="form-label-custom">
                                Đường dẫn <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="url" name="url" class="custom-input" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="icon" class="form-label-custom">Icon</label>
                            <select id="iconSelect" name="icon_select"
                                class="form-select icon-select custom-select mb-2"
                                data-preview="iconPreview"
                                data-icon-input="iconInput">
                                <option value="">Chọn icon</option>
                                @foreach ($fontAwesomeIcons as $iconClass => $iconName)
                                    <option value="{{ $iconClass }}">{{ $iconName }}</option>
                                @endforeach
                                <option value="__custom__">Tự nhập icon</option>
                            </select>
                            <div id="iconInputWrapper" class="d-none">
                                <input type="text" id="iconInput" name="icon"
                                    class="custom-input mb-2" 
                                    placeholder="Ví dụ: fas fa-envelope, fab fa-facebook, fas fa-comment-dots">
                                <small class="text-muted d-block">
                                    <i class="fas fa-info-circle"></i> 
                                    <a href="https://fontawesome.com/search" target="_blank" class="text-primary">
                                        Tìm icon tại FontAwesome
                                    </a>
                                    <span class="ms-2">| Ví dụ: <code>fas fa-envelope</code>, <code>fab fa-facebook</code></span>
                                </small>
                            </div>
                            <input type="hidden" id="iconHidden" name="icon" value="">
                            <div class="icon-preview-container text-center mt-2">
                                <small class="text-muted d-block mb-1">Xem trước:</small>
                                <div class="icon-preview" id="iconPreview"></div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="order" class="form-label-custom">Thứ tự</label>
                            <input type="number" id="order" name="order" class="custom-input" value="0" min="0">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_active" class="form-check-input" checked>
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

@push('styles')
    <style>
        .social-icon-preview {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 50%;
            padding: 8px;
        }

        .social-link {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            max-width: 100%;
        }

        .social-link:hover {
            text-decoration: underline;
        }

        .link-icon {
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .icon-preview-container {
            margin-top: 10px;
        }

        .icon-preview {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary-color);
        }
    </style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    function updateIconPreview(selectId, previewId, hiddenInputId, textInputId, wrapperId) {
        const select = $('#' + selectId);
        const preview = $('#' + previewId);
        const hiddenInput = $('#' + hiddenInputId);
        const textInput = $('#' + textInputId);
        const wrapper = $('#' + wrapperId);
        const selectedValue = select.val();

        if (selectedValue === '__custom__') {
            wrapper.removeClass('d-none');
            const currentValue = textInput.val();
            if (currentValue) {
                hiddenInput.val(currentValue);
                preview.html('<i class="' + currentValue + '"></i>');
            }
        } else if (selectedValue) {
            wrapper.addClass('d-none');
            hiddenInput.val(selectedValue);
            preview.html('<i class="' + selectedValue + '"></i>');
        } else {
            wrapper.addClass('d-none');
            hiddenInput.val('');
            preview.html('');
        }
    }

    $(document).on('input', '[id^="iconInput"]', function() {
        const inputId = $(this).attr('id');
        let linkId = '';

        if (inputId === 'iconInput') {
            linkId = '';
        } else {
            linkId = inputId.replace('iconInput', '');
        }
        
        const hiddenInputId = 'iconHidden' + linkId;
        const previewId = 'iconPreview' + linkId;
        const value = $(this).val();
        
        $('#' + hiddenInputId).val(value);
        if (value) {
            $('#' + previewId).html('<i class="' + value + '"></i>');
        } else {
            $('#' + previewId).html('');
        }
    });

    $('#iconSelect').on('change', function() {
        updateIconPreview('iconSelect', 'iconPreview', 'iconHidden', 'iconInput', 'iconInputWrapper');
    });

    @foreach ($links as $link)
        $('#iconSelect{{ $link->id }}').on('change', function() {
            updateIconPreview('iconSelect{{ $link->id }}', 'iconPreview{{ $link->id }}', 'iconHidden{{ $link->id }}', 'iconInput{{ $link->id }}', 'iconInputWrapper{{ $link->id }}');
        });
    @endforeach
});
</script>
@endpush

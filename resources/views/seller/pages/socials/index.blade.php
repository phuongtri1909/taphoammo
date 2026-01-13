@extends('seller.layouts.sidebar')

@section('title', 'Quản lý mạng xã hội')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách mạng xã hội</h2>
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addSocialModal">
                    <i class="fas fa-plus"></i> Thêm mạng xã hội
                </button>
            </div>
            <div class="card-content">

                @if ($socials->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h4>Chưa có mạng xã hội nào</h4>
                        <p>Thêm mạng xã hội để hiển thị trên website của bạn</p>
                        <button type="button" class="action-button" data-bs-toggle="modal"
                            data-bs-target="#addSocialModal">
                            <i class="fas fa-plus"></i> Thêm mạng xã hội
                        </button>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-small text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-small">Icon</th>
                                    <th class="column-medium">Tên</th>
                                    <th class="column-large">Đường dẫn</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($socials as $key => $social)
                                    <tr class="text-center">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon " data-bs-toggle="modal"
                                                    data-bs-target="#editSocialModal{{ $social->id }}" title="Chỉnh sửa"
                                                    style="border: none;">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                @include('components.delete-form', [
                                                    'id' => $social->id,
                                                    'route' => route('socials.destroy', $social),
                                                    'message' => "Bạn có chắc chắn muốn xóa social '{$social->name}'?",
                                                ])
                                            </div>
                                        </td>
                                        <td>
                                            <div class="social-icon-preview">
                                                @if (Str::startsWith($social->icon, 'custom-'))
                                                    <span class="{{ $social->icon }}"></span>
                                                @else
                                                    <i class="{{ $social->icon }}"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $social->name }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ $social->url }}" target="_blank" class="social-link">
                                                {{ $social->url }}
                                                <i class="fas fa-external-link-alt link-icon"></i>
                                            </a>
                                        </td>
                                        <td>
                                            {{ $social->sort_order }}
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $social->is_active ? 'active' : 'inactive' }}">
                                                {{ $social->is_active ? 'Hiển thị' : 'Ẩn' }}
                                            </span>
                                        </td>

                                    </tr>

                                    <!-- Edit Social Modal -->
                                    <div class="modal fade" id="editSocialModal{{ $social->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa mạng xã hội</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('socials.update', $social->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label for="name{{ $social->id }}"
                                                                class="form-label-custom">Tên mạng xã hội <span
                                                                    class="required-mark">*</span></label>
                                                            <input type="text" id="name{{ $social->id }}"
                                                                name="name" class="custom-input form-control"
                                                                value="{{ $social->name }}" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="url{{ $social->id }}"
                                                                class="form-label-custom">Đường dẫn <span
                                                                    class="required-mark">*</span></label>
                                                            <input type="text" id="url{{ $social->id }}"
                                                                name="url" class="custom-input"
                                                                value="{{ $social->url }}" required>
                                                            <div class="form-hint mt-1">
                                                                <i class="fas fa-info-circle"></i> URL thông thường
                                                                (https://...)
                                                                hoặc định dạng đặc biệt như
                                                                mailto:email@domain.com, tel:+1234567890
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="icon{{ $social->id }}"
                                                                class="form-label-custom">Icon <span
                                                                    class="required-mark">*</span></label>
                                                            <select id="icon{{ $social->id }}" name="icon"
                                                                class="form-select icon-select custom-select" required
                                                                data-preview="iconPreview{{ $social->id }}">
                                                                <option value="">Chọn icon</option>
                                                                @foreach ($fontAwesomeIcons as $iconClass => $iconName)
                                                                    <option value="{{ $iconClass }}"
                                                                        data-icon="{{ $iconClass }}"
                                                                        {{ $social->icon === $iconClass ? 'selected' : '' }}>
                                                                        {{ $iconName }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="sort_order{{ $social->id }}"
                                                                class="form-label-custom">Thứ tự</label>
                                                            <input type="number" id="sort_order{{ $social->id }}"
                                                                name="sort_order" class="custom-input"
                                                                value="{{ $social->sort_order }}" min="0">
                                                        </div>

                                                        <div class="form-check mb-3 custom-switch-wrapper">
                                                            <input type="checkbox" id="is_active{{ $social->id }}"
                                                                name="is_active" class="custom-switch"
                                                                {{ $social->is_active ? 'checked' : '' }}>
                                                            <label for="is_active{{ $social->id }}"
                                                                class="custom-switch-label form-check-label">Hiển
                                                                thị</label>
                                                        </div>

                                                        <div class="icon-preview-container text-center">
                                                            <p>Xem trước icon</p>
                                                            <div class="icon-preview"
                                                                id="iconPreview{{ $social->id }}">
                                                                @if (Str::startsWith($social->icon, 'custom-'))
                                                                    <span class="{{ $social->icon }}"></span>
                                                                @else
                                                                    <i class="{{ $social->icon }}"></i>
                                                                @endif
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

    <!-- Add Social Modal -->
    <div class="modal fade" id="addSocialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title color-primary-6">Thêm mạng xã hội mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('socials.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label-custom">Tên mạng xã hội <span
                                    class="required-mark">*</span></label>
                            <input type="text" id="name" name="name" class="custom-input" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="url" class="form-label-custom">Đường dẫn <span
                                    class="required-mark">*</span></label>
                            <input type="text" id="url" name="url" class="custom-input" required>
                            <div class="form-hint mt-1">
                                <i class="fas fa-info-circle"></i> URL thông thường (https://...) hoặc định dạng đặc biệt
                                như mailto:email@domain.com, tel:+1234567890
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="icon" class="form-label-custom">Icon <span
                                    class="required-mark">*</span></label>
                            <select id="icon" name="icon" class="form-select icon-select custom-select" required
                                data-preview="iconPreview">
                                <option value="">Chọn icon</option>
                                @foreach ($fontAwesomeIcons as $iconClass => $iconName)
                                    <option value="{{ $iconClass }}" data-icon="{{ $iconClass }}">
                                        {{ $iconName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="sort_order" class="form-label-custom">Thứ tự</label>
                            <input type="number" id="sort_order" name="sort_order" class="custom-input" value="0"
                                min="0">
                        </div>

                        <div class="form-check mb-3 custom-switch-wrapper">
                            <input type="checkbox" id="is_active" name="is_active" class="custom-switch" checked>
                            <label for="is_active" class="custom-switch-label form-check-label">Hiển thị</label>
                        </div>

                        <div class="icon-preview-container text-center">
                            <p>Xem trước icon</p>
                            <div class="icon-preview" id="iconPreview"></div>
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
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .social-link:hover {
            text-decoration: underline;
        }

        .link-icon {
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .icon-preview-container {
            margin-top: 20px;
        }

        .icon-preview {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto;
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

                    switch (iconName) {
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

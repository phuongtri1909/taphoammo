@extends('admin.layouts.sidebar')

@section('title', 'Cấu hình Header')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Cấu hình Header</h2>
            </div>

            <div class="card-content">
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr class="text-center">
                                <th class="column-stt text-center">STT</th>
                                <th class="column-small text-center">Thao tác</th>
                                <th class="column-medium">Phần</th>
                                <th class="column-medium">Tên</th>
                                <th class="column-small text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="action-buttons-wrapper">
                                        <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                            data-bs-target="#editSupportBarModal" title="Chỉnh sửa"
                                            style="border: none;">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-name">
                                        <i class="fas fa-headset me-2"></i>Thanh hỗ trợ trực tuyến
                                    </span>
                                </td>
                                <td>{{ $supportBar->label ?? 'Thanh hỗ trợ' }}</td>
                                <td>
                                    <span class="status-badge {{ ($supportBar->is_active ?? true) ? 'active' : 'inactive' }}">
                                        {{ ($supportBar->is_active ?? true) ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>
                                    <div class="action-buttons-wrapper">
                                        <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                            data-bs-target="#editPromotionalBannerModal" title="Chỉnh sửa"
                                            style="border: none;">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-name">
                                        <i class="fas fa-bullhorn me-2"></i>Banner quảng cáo
                                    </span>
                                </td>
                                <td>{{ $promotionalBanner->label ?? 'Banner quảng cáo' }}</td>
                                <td>
                                    <span class="status-badge {{ ($promotionalBanner->is_active ?? true) ? 'active' : 'inactive' }}">
                                        {{ ($promotionalBanner->is_active ?? true) ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>
                                    <div class="action-buttons-wrapper">
                                        <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                            data-bs-target="#editSearchBackgroundModal" title="Chỉnh sửa"
                                            style="border: none;">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-name">
                                        <i class="fas fa-search me-2"></i>Background tìm kiếm
                                    </span>
                                </td>
                                <td>{{ $searchBackground->label ?? 'Background tìm kiếm' }}</td>
                                <td>
                                    <span class="status-badge {{ ($searchBackground->is_active ?? true) ? 'active' : 'inactive' }}">
                                        {{ ($searchBackground->is_active ?? true) ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal fade" id="editSupportBarModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content modal-content-custom">
                            <div class="modal-header">
                                <h5 class="modal-title color-primary-6">Chỉnh sửa thanh hỗ trợ trực tuyến</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.header-configs.update-support-bar') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="support_bar_label" class="form-label-custom">
                                            Tên hiển thị (Admin)
                                        </label>
                                        <input type="text" id="support_bar_label" name="label"
                                            class="custom-input form-control"
                                            value="{{ old('label', $supportBar->label ?? 'Thanh hỗ trợ') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom d-flex align-items-center">
                                            <input type="checkbox" name="is_active" value="1" class="me-2"
                                                {{ ($supportBar->is_active ?? true) ? 'checked' : '' }}>
                                            Kích hoạt
                                        </label>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="support_bar_facebook_url" class="form-label-custom">
                                            URL Facebook
                                        </label>
                                        <input type="url" id="support_bar_facebook_url" name="facebook_url"
                                            class="custom-input form-control"
                                            value="{{ old('facebook_url', $supportBar->getConfig('facebook_url') ?? '') }}"
                                            placeholder="https://facebook.com/shoptaphoazalo">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="support_bar_facebook_text" class="form-label-custom">
                                            Text hiển thị Facebook
                                        </label>
                                        <input type="text" id="support_bar_facebook_text" name="facebook_text"
                                            class="custom-input form-control"
                                            value="{{ old('facebook_text', $supportBar->getConfig('facebook_text') ?? '') }}"
                                            placeholder="facebook.com/shoptaphoazalo">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="support_bar_email" class="form-label-custom">
                                            Email
                                        </label>
                                        <input type="email" id="support_bar_email" name="email"
                                            class="custom-input form-control"
                                            value="{{ old('email', $supportBar->getConfig('email') ?? '') }}"
                                            placeholder="shoptaphoazalo@gmail.com">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="support_bar_email_text" class="form-label-custom">
                                            Text hiển thị Email
                                        </label>
                                        <input type="text" id="support_bar_email_text" name="email_text"
                                            class="custom-input form-control"
                                            value="{{ old('email_text', $supportBar->getConfig('email_text') ?? '') }}"
                                            placeholder="shoptaphoazalo@gmail.com">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="support_bar_operating_hours" class="form-label-custom">
                                            Thời gian hoạt động
                                        </label>
                                        <input type="text" id="support_bar_operating_hours" name="operating_hours_text"
                                            class="custom-input form-control"
                                            value="{{ old('operating_hours_text', $supportBar->getConfig('operating_hours_text') ?? '') }}"
                                            placeholder="Thời gian hoạt động của sàn 24/7">
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

                <div class="modal fade" id="editPromotionalBannerModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content modal-content-custom">
                            <div class="modal-header">
                                <h5 class="modal-title color-primary-6">Chỉnh sửa banner quảng cáo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.header-configs.update-promotional-banner') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="banner_label" class="form-label-custom">
                                            Tên hiển thị (Admin)
                                        </label>
                                        <input type="text" id="banner_label" name="label"
                                            class="custom-input form-control"
                                            value="{{ old('label', $promotionalBanner->label ?? 'Banner quảng cáo') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom d-flex align-items-center">
                                            <input type="checkbox" name="is_active" value="1" class="me-2"
                                                {{ ($promotionalBanner->is_active ?? true) ? 'checked' : '' }}>
                                            Kích hoạt
                                        </label>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="banner_content" class="form-label-custom">
                                            Nội dung banner <span class="required-mark">*</span>
                                        </label>
                                        <textarea id="banner_content" name="content"
                                            class="custom-input form-control" rows="5" required
                                            placeholder="Nhập nội dung banner...">{{ old('content', $promotionalBanner->getConfig('content') ?? '') }}</textarea>
                                        <small class="form-text text-muted">Tối đa 2000 ký tự</small>
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

                <div class="modal fade" id="editSearchBackgroundModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content modal-content-custom">
                            <div class="modal-header">
                                <h5 class="modal-title color-primary-6">Chỉnh sửa background tìm kiếm</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.header-configs.update-search-background') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="search_bg_label" class="form-label-custom">
                                            Tên hiển thị (Admin)
                                        </label>
                                        <input type="text" id="search_bg_label" name="label"
                                            class="custom-input form-control"
                                            value="{{ old('label', $searchBackground->label ?? 'Background tìm kiếm') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom d-flex align-items-center">
                                            <input type="checkbox" name="is_active" value="1" class="me-2"
                                                {{ ($searchBackground->is_active ?? true) ? 'checked' : '' }}>
                                            Kích hoạt
                                        </label>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="search_bg_image" class="form-label-custom">
                                            Hình nền tìm kiếm
                                        </label>
                                        @if($searchBackground && $searchBackground->getConfig('background_image'))
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($searchBackground->getConfig('background_image')) }}" 
                                                    alt="Background hiện tại" 
                                                    class="img-thumbnail" 
                                                    style="max-width: 100%; height: auto; max-height: 200px;">
                                                <p class="text-muted small mt-1">Hình nền hiện tại</p>
                                            </div>
                                        @endif
                                        <input type="file" id="search_bg_image" name="background_image"
                                            class="custom-input form-control" accept="image/*">
                                        <small class="form-text text-muted">
                                            Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP). Kích thước tối đa 10MB. 
                                            Ảnh sẽ được tự động tối ưu và resize. Nếu không upload, sẽ giữ nguyên hình nền hiện tại hoặc dùng mặc định.
                                        </small>
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

@php
    use Illuminate\Support\Facades\Storage;
@endphp

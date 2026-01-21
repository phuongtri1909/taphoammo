@extends('admin.layouts.sidebar')

@section('title', 'Quản lý nội dung Footer')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Quản lý nội dung Footer</h2>
            </div>

            <div class="card-content">
                @if ($contents->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-window-restore"></i>
                        </div>
                        <h4>Chưa có nội dung footer</h4>
                        <p>Nội dung footer sẽ được tạo tự động khi bạn truy cập trang này lần đầu.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Phần</th>
                                    <th class="column-medium">Tiêu đề</th>
                                    <th class="column-large">Mô tả</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($contents as $key => $content)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $content->id }}" title="Chỉnh sửa"
                                                    style="border: none;">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="item-name">
                                                @if($content->section === 'contact')
                                                    Liên hệ
                                                @elseif($content->section === 'information')
                                                    Thông tin
                                                @elseif($content->section === 'seller_registration')
                                                    Đăng ký bán hàng
                                                @else
                                                    {{ $content->section }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $content->title }}</td>
                                        <td class="text-left">
                                            <span class="text-muted small">
                                                {{ Str::limit($content->description ?? 'Không có mô tả', 100) }}
                                            </span>
                                        </td>
                                        <td>{{ $content->order }}</td>
                                    </tr>

                                    <div class="modal fade" id="editModal{{ $content->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa nội dung Footer</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.footer-contents.update', $content) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label class="form-label-custom">Phần</label>
                                                            <input type="text" class="custom-input" 
                                                                value="@if($content->section === 'contact') Liên hệ @elseif($content->section === 'information') Thông tin @elseif($content->section === 'seller_registration') Đăng ký bán hàng @endif" 
                                                                disabled>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="title{{ $content->id }}" class="form-label-custom">
                                                                Tiêu đề <span class="required-mark">*</span>
                                                            </label>
                                                            <input type="text" id="title{{ $content->id }}" name="title"
                                                                class="custom-input form-control" value="{{ old('title', $content->title) }}" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="description{{ $content->id }}" class="form-label-custom">
                                                                Mô tả
                                                            </label>
                                                            <textarea id="description{{ $content->id }}" name="description"
                                                                class="custom-input form-control" rows="4">{{ old('description', $content->description) }}</textarea>
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
                @endif
            </div>
        </div>
    </div>
@endsection

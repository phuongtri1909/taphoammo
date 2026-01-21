@extends('admin.layouts.sidebar')

@section('title', 'Quản lý SEO')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <h2 class="page-title">Danh sách SEO Settings</h2>
                </div>
            </div>

            <div class="card-content">
                @if ($seoSettings->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>Chưa có SEO settings nào</h4>
                        <p>Chạy seeder để tạo dữ liệu SEO mẫu.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-stt">STT</th>
                                    <th class="column-medium text-center">Thao tác</th>
                                    <th class="column-large">Trang</th>
                                    <th class="column-large">Title</th>
                                    <th class="column-small">Thumbnail</th>
                                    <th class="column-large">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($seoSettings as $index => $seo)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.seo.edit', $seo) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="page-info">
                                                <div class="page-name">{{ $pageKeys[$seo->page_key] ?? $seo->page_key }}
                                                </div>
                                                <div class="page-key text-muted">{{ $seo->page_key }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="seo-title">{{ $seo->title }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if ($seo->thumbnail)
                                                <img src="{{ $seo->thumbnail_url }}" alt="Thumbnail"
                                                    class="thumbnail-preview">
                                            @else
                                                <div class="no-thumbnail">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($seo->is_active)
                                                <span class="status-badge active">Hoạt động</span>
                                            @else
                                                <span class="status-badge inactive">Tạm khóa</span>
                                            @endif
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .page-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .page-key {
            font-size: 12px;
            color: #6c757d;
        }

        .thumbnail-preview {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .no-thumbnail {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
@endpush

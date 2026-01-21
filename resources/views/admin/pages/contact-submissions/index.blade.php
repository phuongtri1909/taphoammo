@extends('admin.layouts.sidebar')

@section('title', 'Quản lý tin nhắn liên hệ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách tin nhắn liên hệ</h2>
            </div>

            <div class="card-content">
                <form action="{{ route('admin.contact-submissions.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label-custom small">Tìm kiếm</label>
                            <input type="text" name="search" class="custom-input" placeholder="Email, chủ đề, nội dung..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom small">Trạng thái</label>
                            <select name="status" class="custom-select">
                                <option value="">Tất cả</option>
                                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Đã đọc</option>
                                <option value="responded" {{ request('status') === 'responded' ? 'selected' : '' }}>Đã phản hồi</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom small">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn action-button">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('admin.contact-submissions.index') }}" class="btn back-button">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                @if ($submissions->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h4>Không có tin nhắn liên hệ nào</h4>
                        <p>Tin nhắn liên hệ từ khách hàng sẽ hiển thị ở đây</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-medium">Email</th>
                                    <th class="column-medium">Chủ đề</th>
                                    <th class="column-large">Nội dung</th>
                                    <th class="column-small">Người gửi</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($submissions as $key => $submission)
                                    <tr class="{{ !$submission->isRead() ? 'table-warning' : '' }}">
                                        <td>{{ ($submissions->currentPage() - 1) * $submissions->perPage() + $key + 1 }}</td>
                                        <td class="text-left">
                                            <a href="mailto:{{ $submission->email }}" class="text-primary">
                                                {{ $submission->email }}
                                            </a>
                                        </td>
                                        <td class="text-left">{{ Str::limit($submission->subject, 30) }}</td>
                                        <td class="text-left">
                                            <span class="text-muted small">
                                                {{ Str::limit($submission->message, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($submission->user)
                                                <span class="badge bg-info">{{ $submission->user->full_name }}</span>
                                            @else
                                                <span class="text-muted">Khách</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->isResponded())
                                                <span class="status-badge active">Đã phản hồi</span>
                                            @elseif($submission->isRead())
                                                <span class="status-badge inactive">Đã đọc</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Chưa đọc</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.contact-submissions.show', $submission) }}"
                                                class="action-icon view-icon" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $submissions->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@extends('admin.layouts.sidebar')

@section('title', 'Quản lý bài chia sẻ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Quản lý bài chia sẻ</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.shares.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Tạo bài viết
                    </a>
                </div>
            </div>
            <div class="card-content">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="h4 mb-0">{{ $counts['all'] }}</div>
                            <small class="text-muted">Tổng bài viết</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-warning bg-opacity-25 rounded text-center">
                            <div class="h4 mb-0 text-warning">{{ $counts['pending'] }}</div>
                            <small class="text-muted">Chờ duyệt</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-success bg-opacity-25 rounded text-center">
                            <div class="h4 mb-0 text-success">{{ $counts['approved'] }}</div>
                            <small class="text-muted">Đã duyệt</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-secondary bg-opacity-25 rounded text-center">
                            <div class="h4 mb-0 text-secondary">{{ $counts['draft'] }}</div>
                            <small class="text-muted">Bản nháp</small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.shares.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm tiêu đề, tác giả..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                @foreach (\App\Enums\ShareStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="custom-select">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.shares.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($shares->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-newspaper"></i></div>
                        <h4>Chưa có bài viết nào</h4>
                        <p>Tạo bài viết mới hoặc chờ người dùng gửi bài.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-large">Tiêu đề</th>
                                    <th class="column-medium">Tác giả</th>
                                    <th class="column-small">Danh mục</th>
                                    <th class="column-small text-center">Lượt xem</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-small">Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shares as $key => $share)
                                    <tr>
                                        <td class="text-center">{{ $shares->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.shares.show', $share) }}" class="action-icon view-icon" title="Xem">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.shares.edit', $share) }}" class="action-icon edit-icon" title="Sửa">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @if ($share->isPending())
                                                    <button type="button" class="action-icon border-0" style="background-color: #10b981; color: white;"
                                                        onclick="approveShare('{{ $share->slug }}')" title="Duyệt">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="action-icon border-0" style="background-color: #ef4444; color: white;"
                                                        onclick="rejectShare('{{ $share->slug }}')" title="Từ chối">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                @include('components.delete-form', [
                                                    'id' => $share->id,
                                                    'route' => route('admin.shares.destroy', $share),
                                                    'message' => 'Bạn có chắc chắn muốn xóa bài viết này?',
                                                ])
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($share->image)
                                                    <img src="{{ $share->image_url }}" alt="" class="rounded" style="width: 50px; height: 35px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 35px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong class="d-block">{{ Str::limit($share->title, 40) }}</strong>
                                                    <small class="text-muted">{{ $share->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $share->author->full_name ?? 'N/A' }}</strong>
                                            <div class="text-muted small">{{ $share->author->email ?? '' }}</div>
                                        </td>
                                        <td>{{ $share->category->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ number_format($share->views) }}</td>
                                        <td class="text-center">
                                            <span class="status-badge bg-{{ $share->status->badgeColor() }} {{ in_array($share->status->badgeColor(), ['warning', 'light']) ? 'text-dark' : 'text-white' }}">
                                                {{ $share->status->label() }}
                                            </span>
                                        </td>
                                        <td>{{ $share->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper">
                            {{ $shares->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function approveShare(shareSlug) {
            Swal.fire({
                title: 'Duyệt bài viết?',
                text: 'Bài viết sẽ được công khai sau khi duyệt.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Duyệt',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/shares/${shareSlug}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Thành công!', data.message, 'success')
                                    .then(() => window.location.reload());
                            } else {
                                Swal.fire('Lỗi', data.message, 'error');
                            }
                        });
                }
            });
        }

        function rejectShare(shareSlug) {
            Swal.fire({
                title: 'Từ chối bài viết?',
                html: '<textarea id="rejection_reason" class="swal2-textarea" placeholder="Nhập lý do từ chối..." required></textarea>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Từ chối',
                cancelButtonText: 'Hủy',
                preConfirm: () => {
                    const reason = document.getElementById('rejection_reason').value;
                    if (!reason.trim()) {
                        Swal.showValidationMessage('Vui lòng nhập lý do từ chối');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/shares/${shareSlug}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                rejection_reason: result.value
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Thành công!', data.message, 'success')
                                    .then(() => window.location.reload());
                            } else {
                                Swal.fire('Lỗi', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>
@endpush

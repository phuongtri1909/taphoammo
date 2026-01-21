@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết bài viết - ' . $share->title)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.shares.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-newspaper"></i>
                            Thông tin bài viết
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Tiêu đề:</small>
                                <p class="mb-0"><strong>{{ $share->title }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $share->status->badgeColor() }} {{ in_array($share->status->badgeColor(), ['warning', 'light']) ? 'text-dark' : 'text-white' }}">
                                        {{ $share->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Danh mục:</small>
                                <p class="mb-0">{{ $share->category->name ?? 'Không có' }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Lượt xem:</small>
                                <p class="mb-0">{{ number_format($share->views) }}</p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $share->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            @if ($share->approved_at)
                                <div class="col-6">
                                    <small class="text-muted">Ngày duyệt:</small>
                                    <p class="mb-0">{{ $share->approved_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @endif
                        </div>
                        @if ($share->excerpt)
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <small class="text-muted">Mô tả ngắn:</small>
                                    <p class="mb-0">{{ $share->excerpt }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-user"></i>
                            Thông tin tác giả
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Họ tên:</small>
                                <p class="mb-0"><strong>{{ $share->author->full_name ?? 'N/A' }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Email:</small>
                                <p class="mb-0">{{ $share->author->email ?? 'N/A' }}</p>
                            </div>
                            @if ($share->approvedByUser)
                                <div class="col-6">
                                    <small class="text-muted">Duyệt bởi:</small>
                                    <p class="mb-0">{{ $share->approvedByUser->full_name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($share->image)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-image"></i>
                                Ảnh đại diện
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <img src="{{ $share->image_url }}" alt="{{ $share->title }}" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>
                @endif

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-align-left"></i>
                            Nội dung
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="share-content p-3 bg-light rounded" style="max-height: 500px; overflow-y: auto;">
                            {!! $share->content !!}
                        </div>
                    </div>
                </div>

                @if ($share->rejection_reason)
                    <div class="product-info-card mb-3 border-danger">
                        <div class="card-header py-2 bg-danger text-white">
                            <h3 class="card-title mb-0 text-white" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-exclamation-circle"></i>
                                Lý do từ chối
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <p class="mb-0 text-danger">{{ $share->rejection_reason }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-cog"></i>
                            Thao tác
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if ($share->isPending())
                            <button type="button" class="btn-modern success w-100 mb-3" onclick="approveShare('{{ $share->slug }}')">
                                <i class="fas fa-check"></i> Duyệt bài viết
                            </button>
                            <button type="button" class="btn-modern danger w-100 mb-3" onclick="rejectShare('{{ $share->slug }}')">
                                <i class="fas fa-times"></i> Từ chối
                            </button>
                            <hr class="my-3">
                        @endif

                        <a href="{{ route('admin.shares.edit', $share) }}" class="btn action-button w-100 mb-3">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>

                        @if ($share->isApproved())
                            <a href="{{ route('shares.show', $share->slug) }}" target="_blank" class="btn back-button w-100 mb-3">
                                <i class="fas fa-external-link-alt"></i> Xem bài viết
                            </a>
                        @endif

                        <form action="{{ route('admin.shares.destroy', $share) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-modern danger w-100">
                                <i class="fas fa-trash"></i> Xóa bài viết
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
    @vite('resources/assets/admin/css/product-show.css')
    <style>
        .share-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
@endpush

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

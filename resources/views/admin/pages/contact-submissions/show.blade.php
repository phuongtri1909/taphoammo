@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết tin nhắn liên hệ #' . $contactSubmission->id)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.contact-submissions.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-envelope"></i>
                            Thông tin tin nhắn
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">ID:</small>
                                <p class="mb-0"><strong>#{{ $contactSubmission->id }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    @if($contactSubmission->isResponded())
                                        <span class="status-badge bg-success text-white">Đã phản hồi</span>
                                    @elseif($contactSubmission->isRead())
                                        <span class="status-badge bg-info text-dark">Đã đọc</span>
                                    @else
                                        <span class="status-badge bg-warning text-dark">Chưa đọc</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Ngày gửi:</small>
                                <p class="mb-0">{{ $contactSubmission->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            @if($contactSubmission->read_at)
                                <div class="col-6">
                                    <small class="text-muted">Ngày đọc:</small>
                                    <p class="mb-0">{{ $contactSubmission->read_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <small class="text-muted">Chủ đề:</small>
                                <p class="mb-0"><strong>{{ $contactSubmission->subject }}</strong></p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <small class="text-muted">Nội dung:</small>
                                <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">
                                    {{ $contactSubmission->message }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-user"></i>
                            Thông tin người gửi
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Email:</small>
                                <p class="mb-0">
                                    <a href="mailto:{{ $contactSubmission->email }}" class="color-primary">
                                        <strong>{{ $contactSubmission->email }}</strong>
                                    </a>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Số điện thoại:</small>
                                <p class="mb-0">{{ $contactSubmission->phone ?? 'Không có' }}</p>
                            </div>
                            @if($contactSubmission->user)
                                <div class="col-6">
                                    <small class="text-muted">Người dùng:</small>
                                    <p class="mb-0">
                                        <span class="status-badge bg-info text-dark">{{ $contactSubmission->user->full_name }}</span>
                                    </p>
                                </div>
                            @endif
                            <div class="col-6">
                                <small class="text-muted">IP Address:</small>
                                <p class="mb-0"><code>{{ $contactSubmission->ip_address ?? 'N/A' }}</code></p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($contactSubmission->admin_response)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-reply"></i>
                                Phản hồi của Admin
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <small class="text-muted">Nội dung phản hồi:</small>
                                    <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">
                                        {{ $contactSubmission->admin_response }}
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2">
                                @if($contactSubmission->responded_at)
                                    <div class="col-6">
                                        <small class="text-muted">Ngày phản hồi:</small>
                                        <p class="mb-0">{{ $contactSubmission->responded_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                @endif
                                @if($contactSubmission->respondedBy)
                                    <div class="col-6">
                                        <small class="text-muted">Phản hồi bởi:</small>
                                        <p class="mb-0">{{ $contactSubmission->respondedBy->full_name }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-paper-plane"></i>
                            Phản hồi
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if(!$contactSubmission->isResponded())
                            <form action="{{ route('admin.contact-submissions.update', $contactSubmission) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group mb-3">
                                    <label for="admin_response" class="form-label-custom">Nội dung phản hồi <span class="required-mark">*</span></label>
                                    <textarea id="admin_response" name="admin_response" class="custom-input custom-textarea" rows="8" required
                                        placeholder="Nhập nội dung phản hồi...">{{ old('admin_response') }}</textarea>
                                    @error('admin_response')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn action-button w-100">
                                    <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                </button>
                            </form>
                        @else
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle"></i> Đã phản hồi tin nhắn này
                            </div>
                            @if($contactSubmission->responded_at)
                                <div class="text-muted small">
                                    <p class="mb-1"><strong>Ngày phản hồi:</strong></p>
                                    <p class="mb-0">{{ $contactSubmission->responded_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @endif
                            @if($contactSubmission->respondedBy)
                                <div class="text-muted small mt-2">
                                    <p class="mb-1"><strong>Phản hồi bởi:</strong></p>
                                    <p class="mb-0">{{ $contactSubmission->respondedBy->full_name }}</p>
                                </div>
                            @endif
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">Nội dung phản hồi:</small>
                                <div class="p-2 bg-light rounded mt-2 small" style="white-space: pre-wrap;">
                                    {{ $contactSubmission->admin_response }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
    @vite('resources/assets/admin/css/product-show.css')
@endpush

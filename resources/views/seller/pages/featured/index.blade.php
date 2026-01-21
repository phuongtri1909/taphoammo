@extends('seller.layouts.sidebar')

@section('title', 'Đề xuất sản phẩm/dịch vụ')

@section('main-content')
    <div class="category-container">
        <div class="content-card mb-4">
            <div class="card-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-primary-light p-3 rounded">
                            <h5 class="mb-2"><i class="fas fa-info-circle me-2"></i>Thông tin đề xuất</h5>
                            <p class="mb-1"><strong>Giá mỗi lần đề xuất:</strong> <span class="text-primary">{{ number_format($featuredPrice, 0, ',', '.') }} VNĐ</span></p>
                            <p class="mb-0"><strong>Thời gian mỗi lần:</strong> <span class="text-primary">{{ $featuredHours }} giờ</span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-success-light p-3 rounded">
                            <h5 class="mb-2"><i class="fas fa-lightbulb me-2"></i>Lợi ích khi đề xuất</h5>
                            <ul class="mb-0 ps-3">
                                <li>Sản phẩm/dịch vụ hiển thị ưu tiên trên trang chủ</li>
                                <li>Xuất hiện đầu tiên trong kết quả tìm kiếm</li>
                                <li>Tăng cơ hội tiếp cận khách hàng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="content-card h-100">
                    <div class="card-top">
                        <h5 class="page-title mb-0"><i class="fas fa-box me-2"></i>Đề xuất sản phẩm</h5>
                    </div>
                    <div class="card-content">
                        @if($products->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p>Bạn chưa có sản phẩm nào được duyệt.</p>
                                <a href="{{ route('seller.products.create') }}" class="btn action-button btn-sm">
                                    <i class="fas fa-plus"></i> Thêm sản phẩm
                                </a>
                            </div>
                        @else
                            <form id="featureProductForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Chọn sản phẩm <span class="text-danger">*</span></label>
                                    <select name="product_id" class="custom-select" required>
                                        <option value="">-- Chọn sản phẩm --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                data-featured="{{ $product->featured_until ? $product->featured_until->format('d/m/Y H:i') : '' }}">
                                                {{ $product->name }}
                                                @if($product->isFeatured())
                                                    (Đang đề xuất đến {{ $product->featured_until->format('d/m/Y H:i') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Số lần đề xuất <span class="text-danger">*</span></label>
                                    <input type="number" name="times" class="custom-input" value="1" min="1" max="100" required>
                                    <small class="text-muted">Tổng: <span id="productTotalAmount">{{ number_format($featuredPrice, 0, ',', '.') }}</span> VNĐ 
                                        (<span id="productTotalHours">{{ $featuredHours }}</span> giờ)</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ghi chú (tùy chọn)</label>
                                    <textarea name="note" class="custom-input" rows="2" maxlength="500" placeholder="Ghi chú của bạn..."></textarea>
                                </div>
                                <button type="submit" class="btn action-button w-100" id="btnFeatureProduct">
                                    <i class="fas fa-rocket me-2"></i>Đề xuất sản phẩm
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="content-card h-100">
                    <div class="card-top">
                        <h5 class="page-title mb-0"><i class="fas fa-concierge-bell me-2"></i>Đề xuất dịch vụ</h5>
                    </div>
                    <div class="card-content">
                        @if($services->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-concierge-bell fa-3x mb-3"></i>
                                <p>Bạn chưa có dịch vụ nào được duyệt.</p>
                                <a href="{{ route('seller.services.create') }}" class="btn action-button btn-sm">
                                    <i class="fas fa-plus"></i> Thêm dịch vụ
                                </a>
                            </div>
                        @else
                            <form id="featureServiceForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Chọn dịch vụ <span class="text-danger">*</span></label>
                                    <select name="service_id" class="custom-select" required>
                                        <option value="">-- Chọn dịch vụ --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}"
                                                data-featured="{{ $service->featured_until ? $service->featured_until->format('d/m/Y H:i') : '' }}">
                                                {{ $service->name }}
                                                @if($service->isFeatured())
                                                    (Đang đề xuất đến {{ $service->featured_until->format('d/m/Y H:i') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Số lần đề xuất <span class="text-danger">*</span></label>
                                    <input type="number" name="times" class="custom-input" value="1" min="1" max="100" required>
                                    <small class="text-muted">Tổng: <span id="serviceTotalAmount">{{ number_format($featuredPrice, 0, ',', '.') }}</span> VNĐ 
                                        (<span id="serviceTotalHours">{{ $featuredHours }}</span> giờ)</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ghi chú (tùy chọn)</label>
                                    <textarea name="note" class="custom-input" rows="2" maxlength="500" placeholder="Ghi chú của bạn..."></textarea>
                                </div>
                                <button type="submit" class="btn action-button w-100" id="btnFeatureService">
                                    <i class="fas fa-rocket me-2"></i>Đề xuất dịch vụ
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Lịch sử đề xuất</h2>
            </div>
            <div class="card-content">
                <form action="{{ route('seller.featured.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select name="type" class="custom-select">
                                <option value="">Tất cả loại</option>
                                <option value="product" {{ $type == 'product' ? 'selected' : '' }}>Sản phẩm</option>
                                <option value="service" {{ $type == 'service' ? 'selected' : '' }}>Dịch vụ</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('seller.featured.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($featuredHistories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>Chưa có lịch sử đề xuất</h4>
                        <p>Hãy đề xuất sản phẩm/dịch vụ để tăng khả năng hiển thị</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-medium">Sản phẩm/Dịch vụ</th>
                                    <th class="column-small text-center">Loại</th>
                                    <th class="column-small text-center">Số tiền</th>
                                    <th class="column-small text-center">Số giờ</th>
                                    <th class="column-medium text-center">Thời gian đề xuất</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($featuredHistories as $key => $history)
                                    <tr>
                                        <td>{{ $featuredHistories->firstItem() + $key }}</td>
                                        <td class="text-start">
                                            @if($history->featurable)
                                                <strong>{{ $history->featurable->name }}</strong>
                                            @else
                                                <span class="text-muted">Đã xóa</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge badge-{{ $history->featurable_type_badge }}">
                                                {{ $history->featurable_type_label }}
                                            </span>
                                        </td>
                                        <td>{{ $history->formatted_amount }}</td>
                                        <td>{{ $history->hours }} giờ</td>
                                        <td>
                                            <small>
                                                {{ $history->featured_from->format('d/m/Y H:i') }}<br>
                                                <i class="fas fa-arrow-right text-muted"></i>
                                                {{ $history->featured_until->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($history->isActive())
                                                <span class="status-badge badge-success">Đang hoạt động</span>
                                            @else
                                                <span class="status-badge badge-secondary">Đã hết hạn</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($featuredHistories->hasPages())
                        <div class="pagination-wrapper mt-4">
                            {{ $featuredHistories->appends(request()->query())->links('components.paginate') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .bg-primary-light {
            background-color: rgba(13, 110, 253, 0.1);
        }
        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }
        .info-box h5 {
            color: #333;
            font-size: 1rem;
        }
        .info-box ul li {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
            const featuredPrice = {{ $featuredPrice }};
            const featuredHours = {{ $featuredHours }};

            $('input[name="times"]').first().on('input', function() {
                const times = parseInt($(this).val()) || 1;
                $('#productTotalAmount').text(formatNumber(times * featuredPrice));
                $('#productTotalHours').text(times * featuredHours);
            });

            $('input[name="times"]').last().on('input', function() {
                const times = parseInt($(this).val()) || 1;
                $('#serviceTotalAmount').text(formatNumber(times * featuredPrice));
                $('#serviceTotalHours').text(times * featuredHours);
            });

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('#featureProductForm').on('submit', function(e) {
                e.preventDefault();
                
                const btn = $('#btnFeatureProduct');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                $.ajax({
                    url: '{{ route("seller.featured.product") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: message
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            $('#featureServiceForm').on('submit', function(e) {
                e.preventDefault();
                
                const btn = $('#btnFeatureService');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                $.ajax({
                    url: '{{ route("seller.featured.service") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: message
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
    @endpush
@endsection

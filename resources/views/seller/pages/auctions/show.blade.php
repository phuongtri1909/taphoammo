@extends('seller.layouts.sidebar')

@section('title', 'Đấu giá - ' . $auction->title)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('seller.auctions.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-gavel"></i>
                            {{ $auction->title }}
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        @if($auction->description)
                            <p class="text-muted mb-3">{{ $auction->description }}</p>
                        @endif

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Vị trí banner:</small>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $auction->banner_position === 'left' ? 'info' : 'warning' }}">
                                        {{ $auction->banner_position === 'left' ? 'Bên trái' : 'Bên phải' }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Thời gian hạ banner:</small>
                                <p class="mb-0"><strong>{{ $auction->banner_duration_days }} ngày</strong></p>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Bắt đầu:</small>
                                <p class="mb-0">{{ $auction->start_time->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Kết thúc:</small>
                                <p class="mb-0">{{ $auction->end_time->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Giá khởi điểm:</small>
                                <p class="mb-0">
                                    <strong class="text-primary" style="font-size: 1.1rem;">
                                        {{ number_format($auction->starting_price, 0, ',', '.') }}₫
                                    </strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Giá cao nhất hiện tại:</small>
                                <p class="mb-0">
                                    <strong class="text-success" style="font-size: 1.1rem;">
                                        {{ number_format($currentHighestBid, 0, ',', '.') }}₫
                                    </strong>
                                </p>
                            </div>
                        </div>

                        @if($userBid)
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Bạn đã đấu giá:</strong> {{ number_format($userBid->bid_amount, 0, ',', '.') }}₫
                                @if($userBid->status === 'active' && $userBid->isTopBid())
                                    <span class="badge bg-success ms-2">Đang dẫn đầu</span>
                                @elseif($userBid->status === 'outbid')
                                    <span class="badge bg-warning ms-2">Đã bị vượt</span>
                                @endif
                            </div>
                        @endif

                        <!-- Bidding Form -->
                        <div class="product-info-card">
                            <div class="card-header py-2">
                                <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    Đấu giá
                                </h3>
                            </div>
                            <div class="card-body py-2">
                                <form id="bidForm" onsubmit="submitBid(event)">
                                    @csrf
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Chọn sản phẩm hoặc dịch vụ <span class="required-mark">*</span></label>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <select id="biddable_type" name="biddable_type" class="custom-select" required onchange="updateBiddableOptions()">
                                                    <option value="">Chọn loại</option>
                                                    <option value="App\Models\Product">Sản phẩm</option>
                                                    <option value="App\Models\Service">Dịch vụ</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select id="biddable_id" name="biddable_id" class="custom-select" required>
                                                    <option value="">Chọn sản phẩm/dịch vụ</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Giá đấu (VNĐ) <span class="required-mark">*</span></label>
                                        <input type="number" id="bid_amount" name="bid_amount" class="custom-input" 
                                            min="{{ $currentHighestBid + 1000 }}" 
                                            step="1000" 
                                            value="{{ $currentHighestBid + 1000 }}" 
                                            required>
                                        <small class="text-muted">Giá tối thiểu: {{ number_format($currentHighestBid + 1000, 0, ',', '.') }}₫</small>
                                    </div>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Lưu ý:</strong> Tiền sẽ chỉ bị trừ khi bạn thắng đấu giá. Hệ thống sẽ kiểm tra số dư ví trước khi cho phép đấu giá.
                                    </div>

                                    <button type="submit" class="btn action-button w-100">
                                        <i class="fas fa-gavel"></i> Đấu giá ngay
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Bids -->
                @if($topBids->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-trophy"></i>
                                Top đấu giá ({{ $topBids->count() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Người đấu giá</th>
                                            <th>Sản phẩm/Dịch vụ</th>
                                            <th class="text-center">Giá đấu</th>
                                            <th class="text-center">Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topBids as $index => $bid)
                                            <tr class="{{ $bid->seller_id === Auth::id() ? 'table-info' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $bid->seller->full_name }}</strong>
                                                    @if($bid->seller_id === Auth::id())
                                                        <span class="badge bg-primary ms-1">Bạn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($bid->biddable_type === 'App\Models\Product')
                                                        {{ $bid->biddable->name }}
                                                    @else
                                                        {{ $bid->biddable->name }}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <strong class="text-success">{{ number_format($bid->bid_amount, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td class="text-center">
                                                    <small>{{ $bid->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
    const products = @json($products->map(function($p) { return ['id' => $p->id, 'name' => $p->name]; }));
    const services = @json($services->map(function($s) { return ['id' => $s->id, 'name' => $s->name]; }));
    const currentHighest = {{ $currentHighestBid }};

    function updateBiddableOptions() {
        const type = document.getElementById('biddable_type').value;
        const select = document.getElementById('biddable_id');
        select.innerHTML = '<option value="">Chọn sản phẩm/dịch vụ</option>';

        if (type === 'App\\Models\\Product') {
            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.name;
                select.appendChild(option);
            });
        } else if (type === 'App\\Models\\Service') {
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name;
                select.appendChild(option);
            });
        }
    }

    function submitBid(event) {
        event.preventDefault();

        const form = event.target;
        const formData = {
            biddable_type: form.biddable_type.value,
            biddable_id: parseInt(form.biddable_id.value),
            bid_amount: parseFloat(form.bid_amount.value),
        };

        if (formData.bid_amount <= currentHighest) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Giá đấu phải cao hơn giá hiện tại: ' + new Intl.NumberFormat('vi-VN').format(currentHighest) + '₫'
            });
            return;
        }

        Swal.fire({
            title: 'Đang xử lý...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('{{ route("seller.auctions.bid", $auction->slug) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: data.error || 'Có lỗi xảy ra'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Có lỗi xảy ra, vui lòng thử lại'
            });
        });
    }
</script>
@endpush

@extends('client.layouts.app')

@section('title', 'Chi tiết đơn hàng dịch vụ #' . $serviceOrder->slug . ' - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-3">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-xs text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách đơn hàng
                </a>

                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn">
                    <div class="p-3">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2.5 pb-2.5 border-b border-gray-100">
                            <div>
                                <h2 class="text-base font-bold text-gray-900 mb-1">Đơn hàng dịch vụ #{{ $serviceOrder->slug }}</h2>
                                <div class="flex flex-wrap items-center gap-2 text-[10px] text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $serviceOrder->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-store"></i>
                                        {{ $serviceOrder->seller->full_name }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-left md:text-right">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded mb-1.5 {{ $serviceOrder->status->badgeColor() === 'success' ? 'bg-green-100 text-green-700' : ($serviceOrder->status->badgeColor() === 'warning' ? 'bg-yellow-100 text-yellow-700' : ($serviceOrder->status->badgeColor() === 'danger' ? 'bg-red-100 text-red-700' : ($serviceOrder->status->badgeColor() === 'primary' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700')) ) }}">
                                    {{ $serviceOrder->status->label() }}
                                </span>
                                <p class="text-base font-bold text-primary">
                                    {{ number_format($serviceOrder->total_amount, 0, ',', '.') }}₫
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-[10px]">
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Loại</p>
                                <p class="font-bold text-gray-900">Dịch vụ</p>
                            </div>
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Tổng tiền</p>
                                <p class="font-bold text-primary">{{ number_format($serviceOrder->total_amount, 0, ',', '.') }}₫</p>
                            </div>
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Đơn vị</p>
                                <p class="font-bold text-gray-900">VND</p>
                            </div>
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Người bán</p>
                                <p class="font-bold text-gray-900 truncate">{{ $serviceOrder->seller->full_name }}</p>
                            </div>
                        </div>

                        @php
                            $buyerConfirmHours = (int) \App\Models\Config::getConfig('service_order_buyer_confirm_hours', 96);
                            $completionHours = (int) \App\Models\Config::getConfig('service_order_completion_hours', 96);
                            
                            $hasOpenDispute = $serviceOrder->disputes()->whereIn('status', [
                                \App\Enums\ServiceDisputeStatus::OPEN, 
                                \App\Enums\ServiceDisputeStatus::REVIEWING
                            ])->exists();
                            
                            $canConfirmOrder = $serviceOrder->status === \App\Enums\ServiceOrderStatus::SELLER_CONFIRMED && !$hasOpenDispute;
                            $canCreateDispute = $serviceOrder->status === \App\Enums\ServiceOrderStatus::SELLER_CONFIRMED && !$hasOpenDispute;
                            
                            // Calculate buyer deadline
                            // Khi SELLER_CONFIRMED:
                            // - Nếu có seller_reconfirmed_at (sau dispute) → tính từ đó
                            // - Nếu không → tính từ seller_confirmed_at
                            $buyerDeadline = null;
                            if ($serviceOrder->status === \App\Enums\ServiceOrderStatus::SELLER_CONFIRMED) {
                                if ($serviceOrder->seller_reconfirmed_at) {
                                    $buyerDeadline = $serviceOrder->seller_reconfirmed_at->addHours($buyerConfirmHours);
                                } elseif ($serviceOrder->seller_confirmed_at) {
                                    $buyerDeadline = $serviceOrder->seller_confirmed_at->addHours($buyerConfirmHours);
                                }
                            }
                        @endphp

                        @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::PAID)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                @if($serviceOrder->last_dispute_resolved_at)
                                    <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded text-[10px] text-green-700 mb-2">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Seller đã chấp nhận giải quyết khiếu nại của bạn và đang xử lý lại đơn hàng.</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2 p-2 bg-blue-50 border border-blue-200 rounded text-[10px] text-blue-700">
                                    <i class="fas fa-hourglass-half"></i>
                                    @if($serviceOrder->last_dispute_resolved_at)
                                        <span>Vui lòng chờ seller hoàn thành lại dịch vụ theo yêu cầu của bạn.</span>
                                    @else
                                        <span>Đơn hàng đã được thanh toán. Vui lòng chờ seller xác nhận hoàn thành dịch vụ.</span>
                                    @endif
                                </div>
                            </div>
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::SELLER_CONFIRMED)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                @if($buyerDeadline && now()->lt($buyerDeadline))
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 p-2 bg-purple-50 border border-purple-200 rounded text-[10px]">
                                        <div class="flex items-center gap-2 text-purple-700">
                                            <i class="fas fa-clock"></i>
                                            <span>
                                                Seller đã xác nhận hoàn thành. Còn <strong id="countdown" data-deadline="{{ $buyerDeadline->toISOString() }}"></strong> để xác nhận hoặc khiếu nại
                                            </span>
                                        </div>
                                        <div class="flex gap-2">
                                            @if($canConfirmOrder)
                                                <button type="button" onclick="confirmServiceOrder()" 
                                                    class="px-3 py-1.5 text-[10px] font-medium bg-green-500 hover:bg-green-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Xác nhận đã đúng
                                                </button>
                                            @endif
                                            @if($canCreateDispute)
                                                <button type="button" onclick="showDisputeModal()" 
                                                    class="px-3 py-1.5 text-[10px] font-medium bg-red-500 hover:bg-red-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Khiếu nại
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-[10px] text-yellow-700">
                                        <i class="fas fa-hourglass-end"></i>
                                        <span>Thời gian xác nhận đã hết. Đơn hàng sẽ tự động hoàn thành.</span>
                                    </div>
                                @endif
                            </div>
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::DISPUTED)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-[10px]">
                                    <div class="flex items-center gap-2 text-yellow-700">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Đơn hàng đang trong quá trình xử lý khiếu nại. Vui lòng chờ seller phản hồi.</span>
                                    </div>
                                    @php
                                        $openDispute = $serviceOrder->disputes()->where('status', \App\Enums\ServiceDisputeStatus::OPEN)->first();
                                    @endphp
                                    @if($openDispute)
                                        <button type="button" onclick="withdrawDispute('{{ $openDispute->slug }}')" 
                                            class="px-3 py-1.5 text-[10px] font-medium bg-gray-500 hover:bg-gray-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                            <i class="fas fa-times mr-1"></i>
                                            Rút khiếu nại
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::COMPLETED)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded text-[10px] text-green-700">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Đơn hàng đã hoàn thành. Cảm ơn bạn đã sử dụng dịch vụ!</span>
                                </div>
                            </div>
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::REFUNDED)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-2 p-2 bg-gray-50 border border-gray-200 rounded text-[10px] text-gray-700">
                                    <i class="fas fa-undo"></i>
                                    <span>Đơn hàng đã được hoàn tiền.</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    @if($serviceOrder->serviceVariant && $serviceOrder->serviceVariant->service)
                        <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn">
                            <div class="p-3">
                                <a href="{{ route('services.show', $serviceOrder->serviceVariant->service->slug) }}" class="flex items-start justify-between gap-2 mb-2.5 pb-2.5 border-b border-gray-100">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-bold text-gray-900 mb-1 truncate">
                                            {{ $serviceOrder->serviceVariant->service->name }}
                                        </h3>
                                        <p class="text-[10px] text-gray-600 mb-1">
                                            Biến thể: <span class="font-medium">{{ $serviceOrder->serviceVariant->name }}</span>
                                        </p>
                                        <p class="text-[10px] text-gray-600">
                                            Giá: <span class="font-bold text-primary">{{ number_format($serviceOrder->serviceVariant->price, 0, ',', '.') }}₫</span>
                                        </p>
                                    </div>
                                </a>
                                @if($serviceOrder->serviceVariant->service->description)
                                    <div class="mt-2.5 pt-2.5 border-t border-gray-100">
                                        <p class="text-[10px] text-gray-700 leading-relaxed">
                                            {{ $serviceOrder->serviceVariant->service->description }}
                                        </p>
                                    </div>
                                @endif

                                @if($serviceOrder->note)
                                    <div class="mt-2.5 pt-2.5 border-t border-gray-100">
                                        <p class="text-[10px] font-semibold text-gray-700 mb-1">
                                            <i class="fas fa-sticky-note text-primary mr-1"></i>
                                            Ghi chú của bạn:
                                        </p>
                                        <div class="p-2 bg-blue-50 border border-blue-200 rounded">
                                            <p class="text-[10px] text-gray-700 leading-relaxed" style="white-space: pre-wrap;">{{ $serviceOrder->note }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Review Section --}}
                                @if(in_array($serviceOrder->status, [\App\Enums\ServiceOrderStatus::COMPLETED, \App\Enums\ServiceOrderStatus::PARTIAL_REFUNDED]))
                                    @php
                                        $serviceId = $serviceOrder->serviceVariant->service->id;
                                        $existingReview = \App\Models\Review::where('order_type', 'service')
                                            ->where('order_id', $serviceOrder->id)
                                            ->where('user_id', Auth::id())
                                            ->where('reviewable_id', $serviceId)
                                            ->first();
                                    @endphp
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        @if($existingReview)
                                            {{-- Display existing review --}}
                                            <div class="p-2.5 bg-yellow-50 border border-yellow-200 rounded">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <p class="text-[10px] font-semibold text-yellow-800">
                                                        <i class="fas fa-star mr-1"></i> Đánh giá của bạn
                                                    </p>
                                                    <div class="flex items-center gap-0.5">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star text-xs {{ $i <= $existingReview->rating ? 'text-yellow-500' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($existingReview->content)
                                                    <p class="text-[10px] text-yellow-700">{{ $existingReview->content }}</p>
                                                @endif
                                                <p class="text-[9px] text-yellow-600 mt-1">
                                                    Đánh giá lúc: {{ $existingReview->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        @else
                                            {{-- Review form --}}
                                            <div class="p-2.5 bg-blue-50 border border-blue-200 rounded" id="serviceReviewForm">
                                                <p class="text-[10px] font-semibold text-blue-800 mb-2">
                                                    <i class="fas fa-star mr-1"></i> Đánh giá dịch vụ
                                                </p>
                                                <div class="mb-2">
                                                    <div class="flex items-center gap-1 review-stars" id="serviceStars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <button type="button" class="star-btn text-lg text-gray-300 hover:text-yellow-500 transition-colors" data-rating="{{ $i }}">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        @endfor
                                                        <span class="text-[10px] text-gray-500 ml-2" id="serviceRatingText">Chọn số sao</span>
                                                    </div>
                                                    <input type="hidden" id="serviceRatingValue" value="">
                                                </div>
                                                <div class="mb-2">
                                                    <textarea id="serviceReviewContent" rows="2" maxlength="1000"
                                                        class="w-full text-[10px] border border-gray-300 rounded focus:ring-2 focus:ring-blue-400 focus:border-blue-400 p-1.5"
                                                        placeholder="Viết nhận xét của bạn về dịch vụ (tùy chọn)..."></textarea>
                                                </div>
                                                <button type="button" onclick="submitServiceReview()"
                                                    class="w-full py-1.5 px-3 text-[10px] font-medium bg-blue-500 hover:bg-blue-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                                    <i class="fas fa-paper-plane mr-1"></i> Gửi đánh giá
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($serviceOrder->seller_confirmed_at)
                        <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn">
                            <div class="p-3">
                                <h4 class="text-[11px] font-semibold text-gray-800 mb-2">
                                    <i class="fas fa-history mr-1"></i> Lịch sử xử lý
                                </h4>
                                <div class="space-y-1.5 text-[10px]">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="fas fa-shopping-cart text-blue-500"></i>
                                        <span>Đặt hàng: {{ $serviceOrder->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($serviceOrder->seller_confirmed_at)
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-check text-purple-500"></i>
                                            <span>Seller xác nhận hoàn thành: {{ $serviceOrder->seller_confirmed_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                    @if($serviceOrder->seller_reconfirmed_at)
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-redo text-purple-500"></i>
                                            <span>Seller báo lại hoàn thành: {{ $serviceOrder->seller_reconfirmed_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                    @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::COMPLETED)
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                            <span>Hoàn thành: {{ $serviceOrder->updated_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($serviceOrder->disputes->count() > 0)
                        <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn">
                            <div class="p-3">
                                <h4 class="text-[11px] font-semibold text-gray-800 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Lịch sử khiếu nại ({{ $serviceOrder->disputes->count() }})
                                </h4>
                                <div class="space-y-2">
                                    @foreach($serviceOrder->disputes->sortByDesc('created_at') as $dispute)
                                        <div class="p-2 rounded border-l-3 {{ $dispute->status === \App\Enums\ServiceDisputeStatus::APPROVED ? 'bg-green-50 border-green-500' : ($dispute->status === \App\Enums\ServiceDisputeStatus::REJECTED ? 'bg-red-50 border-red-500' : ($dispute->status === \App\Enums\ServiceDisputeStatus::REVIEWING ? 'bg-blue-50 border-blue-500' : 'bg-yellow-50 border-yellow-500')) }}">
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded {{ $dispute->status === \App\Enums\ServiceDisputeStatus::APPROVED ? 'bg-green-100 text-green-700' : ($dispute->status === \App\Enums\ServiceDisputeStatus::REJECTED ? 'bg-red-100 text-red-700' : ($dispute->status === \App\Enums\ServiceDisputeStatus::REVIEWING ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                                    {{ $dispute->status->label() }}
                                                </span>
                                                <span class="text-[9px] text-gray-500">{{ $dispute->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <p class="text-[10px] text-gray-700 mb-1">
                                                <strong>Lý do:</strong> {{ $dispute->reason }}
                                            </p>
                                            @if($dispute->evidence && is_array($dispute->evidence) && count($dispute->evidence) > 0)
                                                <div class="mb-1">
                                                    <p class="text-[9px] font-semibold text-gray-600 mb-0.5">
                                                        <i class="fas fa-link mr-0.5"></i> Bằng chứng URL:
                                                    </p>
                                                    <div class="space-y-0.5">
                                                        @foreach($dispute->evidence as $url)
                                                            <a href="{{ $url }}" target="_blank" class="block text-[9px] text-primary hover:underline truncate">
                                                                {{ $url }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if($dispute->evidence_files && is_array($dispute->evidence_files) && count($dispute->evidence_files) > 0)
                                                <div class="mb-1">
                                                    <p class="text-[9px] font-semibold text-gray-600 mb-0.5">
                                                        <i class="fas fa-paperclip mr-0.5"></i> Tệp đính kèm:
                                                    </p>
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($dispute->evidence_files as $file)
                                                            @if(isset($file['path']))
                                                                @php
                                                                    $isImage = in_array(strtolower(pathinfo($file['path'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
                                                                @endphp
                                                                @if($isImage)
                                                                    <a href="javascript:void(0)" onclick="openFileModal('{{ asset('storage/' . $file['path']) }}', '{{ $file['name'] ?? 'Image' }}', true)" class="block cursor-pointer">
                                                                        <img src="{{ asset('storage/' . $file['path']) }}" alt="{{ $file['name'] ?? 'Evidence' }}" class="w-12 h-12 object-cover rounded border border-gray-200 hover:opacity-80">
                                                                    </a>
                                                                @else
                                                                    <a href="javascript:void(0)" onclick="openFileModal('{{ asset('storage/' . $file['path']) }}', '{{ $file['name'] ?? 'File' }}', false)" class="flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-[9px] text-gray-700 hover:bg-gray-200 cursor-pointer">
                                                                        <i class="fas fa-file"></i>
                                                                        <span class="truncate max-w-[80px]">{{ $file['name'] ?? 'File' }}</span>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if($dispute->seller_note)
                                                <p class="text-[10px] text-amber-700 mb-1 p-1 bg-amber-50 rounded">
                                                    <strong><i class="fas fa-store mr-0.5"></i> Phản hồi seller:</strong> {{ $dispute->seller_note }}
                                                </p>
                                            @endif
                                            @if($dispute->admin_note)
                                                <p class="text-[10px] text-blue-700 mb-1 p-1 bg-blue-50 rounded">
                                                    <strong><i class="fas fa-user-shield mr-0.5"></i> Ghi chú admin:</strong> {{ $dispute->admin_note }}
                                                </p>
                                            @endif
                                            @if($dispute->resolved_at)
                                                <p class="text-[9px] text-gray-500">
                                                    Xử lý: {{ $dispute->resolved_at->format('d/m/Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($serviceOrder->refunds->count() > 0)
                        <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn">
                            <div class="p-3">
                                <h4 class="text-[11px] font-semibold text-gray-800 mb-2">
                                    <i class="fas fa-undo mr-1"></i> Lịch sử hoàn tiền ({{ $serviceOrder->refunds->count() }})
                                </h4>
                                <div class="space-y-2">
                                    @foreach($serviceOrder->refunds as $refund)
                                        <div class="p-2 bg-gray-50 rounded">
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-{{ $refund->status->badgeColor() === 'success' ? 'green' : ($refund->status->badgeColor() === 'warning' ? 'yellow' : 'gray') }}-100 text-{{ $refund->status->badgeColor() === 'success' ? 'green' : ($refund->status->badgeColor() === 'warning' ? 'yellow' : 'gray') }}-700">
                                                    {{ $refund->status->label() }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-gray-700 mb-1">
                                                <strong>Số tiền:</strong> <span class="font-bold text-primary">{{ number_format($refund->total_amount, 0, ',', '.') }}₫</span>
                                            </p>
                                            @if($refund->reason)
                                                <p class="text-[10px] text-gray-700 mb-1">
                                                    <strong>Lý do:</strong> {{ $refund->reason }}
                                                </p>
                                            @endif
                                            <p class="text-[9px] text-gray-500">
                                                Ngày tạo: {{ $refund->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dispute Modal -->
    <div id="disputeModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto animate-fadeIn">
            <div class="p-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        Tạo khiếu nại
                    </h3>
                    <button type="button" onclick="hideDisputeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form id="disputeForm" class="p-3" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                        Lý do khiếu nại <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="disputeReason" rows="4" required minlength="10" maxlength="1000"
                        class="w-full text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary focus:border-primary p-2"
                        placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."></textarea>
                    <p class="text-[9px] text-gray-500 mt-1">Tối thiểu 10 ký tự, tối đa 1000 ký tự</p>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                        Bằng chứng (URL, tùy chọn)
                    </label>
                    <div id="evidenceList" class="space-y-1 mb-1"></div>
                    <div class="flex gap-1">
                        <input type="url" id="evidenceInput" 
                            class="flex-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary focus:border-primary p-1.5"
                            placeholder="https://...">
                        <button type="button" onclick="addEvidence()" 
                            class="px-2 py-1.5 text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <p class="text-[9px] text-gray-500 mt-1">Nhập URL hình ảnh/video để làm bằng chứng</p>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                        Upload file/hình ảnh <span class="text-gray-500">(Tối đa 10MB/file, tối đa 10 files)</span>
                    </label>
                    <div class="space-y-2">
                        <input type="file" 
                            id="evidenceFiles" 
                            name="evidence_files[]" 
                            multiple
                            accept="image/jpeg,image/jpg,image/png,image/webp,.pdf,.doc,.docx,.txt,.rtf"
                            class="w-full text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary focus:border-primary p-1.5 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        <div id="filePreview" class="space-y-1"></div>
                        <p class="text-[9px] text-gray-500">
                            Hình ảnh: JPG, JPEG, PNG, WEBP | File: PDF, DOC, DOCX, TXT, RTF
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="hideDisputeModal()" 
                        class="flex-1 py-2 text-[10px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                        Hủy
                    </button>
                    <button type="button" onclick="submitDispute()"
                        class="flex-1 py-2 text-[10px] font-medium bg-red-500 hover:bg-red-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Gửi khiếu nại
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div id="fileModal" class="hidden fixed inset-0 bg-black/80 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden animate-fadeIn">
            <div class="flex items-center justify-between p-3 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-900" id="fileModalTitle">Xem file</h3>
                <button type="button" onclick="closeFileModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 overflow-auto" style="max-height: calc(90vh - 120px);" id="fileModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="flex justify-end gap-2 p-3 border-t border-gray-200">
                <a href="#" id="downloadLink" class="px-4 py-2 text-xs font-medium bg-primary text-white rounded hover:bg-primary-6" download>
                    <i class="fas fa-download mr-1"></i> Tải xuống
                </a>
                <button type="button" onclick="closeFileModal()" class="px-4 py-2 text-xs font-medium bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Đóng
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.6s ease-out; }
        .border-l-3 { border-left-width: 3px; }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const deadline = new Date(countdownEl.dataset.deadline).getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = deadline - now;
            
            if (distance < 0) {
                countdownEl.innerHTML = '<span class="text-red-500">Đã hết hạn</span>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            let text = '';
            if (days > 0) text += days + ' ngày ';
            if (hours > 0) text += hours + ' giờ ';
            text += minutes + ' phút ' + seconds + ' giây';
            
            countdownEl.innerHTML = text;
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
});

function confirmServiceOrder() {
    Swal.fire({
        title: 'Xác nhận đơn hàng đã đúng yêu cầu',
        html: `
            <div style="text-align: left; padding: 1rem 0;">
                <p style="font-size: 14px; color: #374151;">Bạn xác nhận seller đã hoàn thành dịch vụ đúng yêu cầu?</p>
                <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem; margin-top: 0.5rem;">
                    <li>Sau khi xác nhận, đơn hàng sẽ hoàn thành</li>
                    <li>Tiền sẽ được chuyển cho seller</li>
                    <li>Hành động này không thể hoàn tác</li>
                </ul>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check"></i> Xác nhận hoàn thành',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('{{ route("service-orders.confirm", $serviceOrder->slug) }}');
        }
    });
}

let evidenceUrls = [];
let selectedFiles = [];

function showDisputeModal() {
    document.getElementById('disputeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideDisputeModal() {
    document.getElementById('disputeModal').classList.add('hidden');
    document.body.style.overflow = '';
    // Reset form
    document.getElementById('disputeReason').value = '';
    evidenceUrls = [];
    selectedFiles = [];
    document.getElementById('evidenceList').innerHTML = '';
    document.getElementById('filePreview').innerHTML = '';
    document.getElementById('evidenceFiles').value = '';
    document.getElementById('evidenceInput').value = '';
}

function addEvidence() {
    const input = document.getElementById('evidenceInput');
    const url = input.value.trim();
    
    if (!url) return;
    
    try {
        new URL(url);
    } catch (e) {
        Swal.fire({
            title: 'Lỗi',
            text: 'URL không hợp lệ',
            icon: 'warning',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    if (evidenceUrls.includes(url)) {
        Swal.fire({
            title: 'Lỗi',
            text: 'URL này đã được thêm',
            icon: 'warning',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    evidenceUrls.push(url);
    input.value = '';
    updateEvidenceList();
}

function removeEvidence(index) {
    evidenceUrls.splice(index, 1);
    updateEvidenceList();
}

function updateEvidenceList() {
    const container = document.getElementById('evidenceList');
    container.innerHTML = evidenceUrls.map((url, index) => `
        <div class="flex items-center gap-1 p-1 bg-gray-50 rounded">
            <span class="flex-1 text-[9px] text-gray-600 truncate">${url}</span>
            <button type="button" onclick="removeEvidence(${index})" class="text-red-500 hover:text-red-700 p-0.5">
                <i class="fas fa-times text-[10px]"></i>
            </button>
        </div>
    `).join('');
}

// File upload preview
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('evidenceFiles');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const container = document.getElementById('filePreview');
            
            if (files.length > 10) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Chỉ được upload tối đa 10 files',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                fileInput.value = '';
                container.innerHTML = '';
                return;
            }
            
            // Check file sizes
            const oversizedFiles = files.filter(f => f.size > 10 * 1024 * 1024);
            if (oversizedFiles.length > 0) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Một số file vượt quá 10MB: ' + oversizedFiles.map(f => f.name).join(', '),
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                fileInput.value = '';
                container.innerHTML = '';
                return;
            }
            
            selectedFiles = files;
            container.innerHTML = files.map((file, index) => `
                <div class="flex items-center gap-1 p-1 bg-gray-50 rounded text-[9px]">
                    <i class="fas fa-file text-gray-500"></i>
                    <span class="flex-1 truncate">${file.name}</span>
                    <span class="text-gray-400">${(file.size / 1024).toFixed(1)}KB</span>
                </div>
            `).join('');
        });
    }
});

function submitDispute() {
    const reason = document.getElementById('disputeReason').value.trim();
    if (!reason || reason.length < 10) {
        Swal.fire({
            title: 'Lỗi',
            text: 'Vui lòng nhập lý do khiếu nại (tối thiểu 10 ký tự)',
            icon: 'warning',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    // Prepare FormData
    const formData = new FormData();
    formData.append('reason', reason);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Add evidence URLs
    evidenceUrls.forEach((url, index) => {
        formData.append('evidence[]', url);
    });
    
    // Add files
    const fileInput = document.getElementById('evidenceFiles');
    if (fileInput && fileInput.files.length > 0) {
        Array.from(fileInput.files).forEach(file => {
            formData.append('evidence_files[]', file);
        });
    }
    
    hideDisputeModal();
    
    Swal.fire({
        title: 'Đang xử lý...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch('{{ route("service-orders.dispute", $serviceOrder->slug) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Thành công!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => window.location.reload());
        } else {
            Swal.fire({
                title: 'Lỗi',
                text: data.message || 'Có lỗi xảy ra.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi gửi khiếu nại.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    });
}

function withdrawDispute(disputeSlug) {
    Swal.fire({
        title: 'Rút khiếu nại',
        text: 'Bạn có chắc muốn rút khiếu nại này? Đơn hàng sẽ quay về trạng thái chờ xác nhận.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6b7280',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Xác nhận rút',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction(`{{ url('service-disputes') }}/${disputeSlug}/withdraw`);
        }
    });
}

function submitAction(url) {
    Swal.fire({
        title: 'Đang xử lý...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Thành công!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => window.location.reload());
        } else {
            Swal.fire({
                title: 'Lỗi',
                text: data.message || 'Có lỗi xảy ra.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    });
}

// File Preview Modal Functions
function openFileModal(url, name, isImage) {
    const modal = document.getElementById('fileModal');
    document.getElementById('fileModalTitle').textContent = name;
    document.getElementById('downloadLink').href = url;
    
    if (isImage) {
        document.getElementById('fileModalBody').innerHTML = `
            <div class="text-center">
                <img src="${url}" alt="${name}" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
            </div>
        `;
    } else {
        const extension = url.split('.').pop().toLowerCase();
        if (extension === 'pdf') {
            document.getElementById('fileModalBody').innerHTML = `
                <iframe src="${url}" style="width: 100%; height: 70vh; border: none;"></iframe>
            `;
        } else if (['txt', 'rtf'].includes(extension)) {
            fetch(url)
                .then(response => response.text())
                .then(text => {
                    document.getElementById('fileModalBody').innerHTML = `
                        <pre class="p-4 bg-gray-100 rounded text-xs overflow-auto" style="max-height: 70vh; white-space: pre-wrap;">${escapeHtml(text)}</pre>
                    `;
                })
                .catch(() => {
                    document.getElementById('fileModalBody').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">File: ${name}</p>
                            <a href="${url}" class="px-4 py-2 bg-primary text-white rounded" download>
                                <i class="fas fa-download mr-1"></i> Tải xuống để xem
                            </a>
                        </div>
                    `;
                });
        } else {
            document.getElementById('fileModalBody').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-file text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">File: ${name}</p>
                    <a href="${url}" class="px-4 py-2 bg-primary text-white rounded" download>
                        <i class="fas fa-download mr-1"></i> Tải xuống để xem
                    </a>
                </div>
            `;
        }
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFileModal() {
    document.getElementById('fileModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal on click outside
document.getElementById('fileModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFileModal();
    }
});

// Service Review Star Rating
const serviceStars = document.getElementById('serviceStars');
if (serviceStars) {
    const buttons = serviceStars.querySelectorAll('.star-btn');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            document.getElementById('serviceRatingValue').value = rating;
            
            buttons.forEach((b, index) => {
                if (index < rating) {
                    b.classList.remove('text-gray-300');
                    b.classList.add('text-yellow-500');
                } else {
                    b.classList.remove('text-yellow-500');
                    b.classList.add('text-gray-300');
                }
            });
            
            const ratingTexts = ['', 'Rất tệ', 'Tệ', 'Bình thường', 'Tốt', 'Rất tốt'];
            document.getElementById('serviceRatingText').textContent = ratingTexts[rating];
        });
        
        btn.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            buttons.forEach((b, index) => {
                if (index < rating) {
                    b.classList.add('text-yellow-400');
                }
            });
        });
        
        btn.addEventListener('mouseleave', function() {
            const currentRating = parseInt(document.getElementById('serviceRatingValue').value) || 0;
            buttons.forEach((b, index) => {
                b.classList.remove('text-yellow-400');
                if (index < currentRating) {
                    b.classList.add('text-yellow-500');
                    b.classList.remove('text-gray-300');
                } else {
                    b.classList.add('text-gray-300');
                    b.classList.remove('text-yellow-500');
                }
            });
        });
    });
}

// Submit Service Review
function submitServiceReview() {
    const rating = document.getElementById('serviceRatingValue').value;
    const content = document.getElementById('serviceReviewContent').value;
    
    if (!rating) {
        Swal.fire({
            icon: 'warning',
            title: 'Chưa chọn số sao',
            text: 'Vui lòng chọn số sao đánh giá',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }
    
    Swal.fire({
        title: 'Đang gửi đánh giá...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch('{{ route("service-orders.review", $serviceOrder->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            rating: parseInt(rating),
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: data.message,
                confirmButtonColor: '#10b981'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: data.error || 'Có lỗi xảy ra',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Có lỗi xảy ra, vui lòng thử lại',
            confirmButtonColor: '#ef4444'
        });
    });
}
</script>
@endpush

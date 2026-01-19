@extends('client.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->slug . ' - ' . config('app.name'))

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
                                <h2 class="text-base font-bold text-gray-900 mb-1">Đơn hàng #{{ $order->slug }}</h2>
                                <div class="flex flex-wrap items-center gap-2 text-[10px] text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-store"></i>
                                        {{ $order->seller->full_name }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-left md:text-right">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded mb-1.5 {{ $order->status->badgeColor() === 'success' ? 'bg-green-100 text-green-700' : ($order->status->badgeColor() === 'warning' ? 'bg-yellow-100 text-yellow-700' : ($order->status->badgeColor() === 'danger' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                    {{ $order->status->label() }}
                                </span>
                                <p class="text-base font-bold text-primary">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-[10px]">
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Tổng sản phẩm</p>
                                <p class="font-bold text-gray-900">{{ $order->items->sum('quantity') }}</p>
                            </div>
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Số loại</p>
                                <p class="font-bold text-gray-900">{{ $order->items->count() }}</p>
                            </div>
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Tổng tiền</p>
                                <p class="font-bold text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}₫</p>
                            </div>
                            <div class="text-center p-1.5 bg-gray-50 rounded">
                                <p class="text-gray-600">Đơn vị</p>
                                <p class="font-bold text-gray-900">VND</p>
                            </div>
                        </div>

                        @php
                            $refundHours = (int) \App\Models\Config::getConfig('refund_hours', 24);
                            $refundDeadline = $order->created_at->addHours($refundHours);
                            $canConfirmOrder = $order->status === \App\Enums\OrderStatus::PAID 
                                && !$order->disputes()->whereIn('status', [\App\Enums\DisputeStatus::OPEN, \App\Enums\DisputeStatus::REVIEWING])->exists();
                            $isWithinRefundTime = now()->lt($refundDeadline);
                        @endphp

                        @if($order->status === \App\Enums\OrderStatus::PAID)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                @if($isWithinRefundTime)
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 p-2 bg-blue-50 border border-blue-200 rounded text-[10px]">
                                        <div class="flex items-center gap-2 text-blue-700">
                                            <i class="fas fa-clock"></i>
                                            <span>
                                                Còn <strong id="countdown" data-deadline="{{ $refundDeadline->toISOString() }}"></strong> để khiếu nại hoặc xác nhận đơn hàng
                                            </span>
                                        </div>
                                        @if($canConfirmOrder)
                                            <button type="button" onclick="confirmOrder()" 
                                                class="px-3 py-1.5 text-[10px] font-medium bg-green-500 hover:bg-green-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Xác nhận đơn hàng
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-[10px]">
                                        <div class="flex items-center gap-2 text-yellow-700">
                                            <i class="fas fa-hourglass-end"></i>
                                            <span>Thời gian khiếu nại đã hết. Đơn hàng sẽ tự động hoàn thành.</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($order->status === \App\Enums\OrderStatus::COMPLETED)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded text-[10px] text-green-700">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Đơn hàng đã hoàn thành</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($refundableItems as $refundableItem)
                        @php
                            $item = $refundableItem['item'];
                            $productValues = $refundableItem['product_values'];
                            $canRefund = $refundableItem['can_refund'];
                            $hasDispute = $item->disputes->isNotEmpty();
                            $dispute = $item->disputes->sortByDesc('created_at')->first();
                        @endphp

                        <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn" style="animation-delay: {{ $loop->index * 0.05 }}s">
                            <div class="p-3">
                                <div class="flex items-start justify-between gap-2 mb-2.5 pb-2.5 border-b border-gray-100">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-bold text-gray-900 mb-1 truncate">
                                            {{ $item->productVariant->product->name }}
                                        </h3>
                                        <p class="text-[10px] text-gray-600 mb-1">
                                            Biến thể: <span class="font-medium">{{ $item->productVariant->name }}</span>
                                        </p>
                                        <p class="text-[10px] text-gray-600">
                                            Số lượng: <span class="font-medium">{{ $item->quantity }}</span> × 
                                            <span class="font-medium">{{ number_format($item->price, 0, ',', '.') }}₫</span> = 
                                            <span class="font-bold text-primary">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</span>
                                        </p>
                                    </div>
                                </div>

                                @if($productValues->count() > 0)
                                    <div class="mb-2.5">
                                        <p class="text-[10px] font-semibold text-gray-700 mb-1.5">Giá trị sản phẩm ({{ $productValues->count() }}/{{ $item->quantity }}):</p>
                                        <div class="space-y-1">
                                            @foreach($productValues as $value)
                                                <div class="flex items-center justify-between gap-2 p-1.5 bg-gray-50 rounded text-[10px] hover:bg-gray-100 transition-colors">
                                                    <span class="text-gray-700 font-mono truncate flex-1">
                                                        #{{ $value->slug }} - {{ $value->status->label() }}
                                                    </span>
                                                    <div class="flex items-center gap-2">
                                                        @if($value->canViewDataBy(Auth::user()))
                                                            <button type="button"
                                                                onclick="showValueModal('{{ $value->slug }}')"
                                                                class="text-primary hover:text-primary/80 transition-colors"
                                                                title="Xem giá trị">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        @endif
                                                        @if($canRefund && !$hasDispute)
                                                            <label class="flex items-center cursor-pointer">
                                                                <input type="checkbox" 
                                                                    class="product-value-checkbox-{{ $item->id }} w-3 h-3 text-primary border-gray-300 rounded focus:ring-primary" 
                                                                    value="{{ $value->slug }}"
                                                                    data-item-id="{{ $item->id }}"
                                                                    data-value-slug="{{ $value->slug }}">
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($hasDispute && $dispute)
                                    @php
                                        $disputedProductValues = $dispute->productValues ?? collect();
                                        $canWithdrawDispute = in_array($dispute->status, [\App\Enums\DisputeStatus::OPEN, \App\Enums\DisputeStatus::REVIEWING]);
                                    @endphp
                                    <div class="mb-2.5 p-2 bg-{{ $dispute->status->badgeColor() }}-50 border border-{{ $dispute->status->badgeColor() }}-200 rounded">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="text-[10px] font-semibold text-{{ $dispute->status->badgeColor() }}-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Khiếu nại: {{ $dispute->status->label() }}
                                            </p>
                                            @if($canWithdrawDispute)
                                                <button type="button" onclick="withdrawDispute('{{ $dispute->slug }}')" 
                                                    class="text-[9px] px-2 py-0.5 bg-gray-500 hover:bg-gray-600 text-white rounded transition-all">
                                                    <i class="fas fa-undo mr-0.5"></i> Rút khiếu nại
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-{{ $dispute->status->badgeColor() }}-700 mb-1">
                                            <strong>Lý do:</strong> {{ $dispute->reason }}
                                        </p>
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
                                        @if($disputedProductValues->count() > 0)
                                            <div class="mt-1.5 pt-1.5 border-t border-{{ $dispute->status->badgeColor() }}-300">
                                                <p class="text-[9px] text-{{ $dispute->status->badgeColor() }}-600 mb-1">
                                                    Giá trị đã khiếu nại ({{ $disputedProductValues->count() }}):
                                                </p>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($disputedProductValues as $value)
                                                        <span class="text-[9px] px-1.5 py-0.5 bg-{{ $dispute->status->badgeColor() }}-200 text-{{ $dispute->status->badgeColor() }}-900 rounded font-mono">
                                                            #{{ $value->slug }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if($dispute->evidence || ($dispute->evidence_files && count($dispute->evidence_files) > 0))
                                            <div class="mt-1.5 pt-1.5 border-t border-{{ $dispute->status->badgeColor() }}-300">
                                                <p class="text-[9px] font-semibold text-{{ $dispute->status->badgeColor() }}-600 mb-1">
                                                    Bằng chứng ({{ ($dispute->evidence ? count($dispute->evidence) : 0) + ($dispute->evidence_files ? count($dispute->evidence_files) : 0) }}):
                                                </p>
                                                <div class="space-y-1">
                                                    @if($dispute->evidence && count($dispute->evidence) > 0)
                                                        @foreach($dispute->evidence as $evidenceUrl)
                                                            <div class="flex items-center gap-1.5 p-1 bg-{{ $dispute->status->badgeColor() }}-100 rounded text-[9px]">
                                                                <i class="fas fa-link text-{{ $dispute->status->badgeColor() }}-600"></i>
                                                                <a href="{{ $evidenceUrl }}" target="_blank" rel="noopener noreferrer" class="text-{{ $dispute->status->badgeColor() }}-700 hover:text-{{ $dispute->status->badgeColor() }}-900 truncate flex-1">
                                                                    {{ Str::limit($evidenceUrl, 40) }}
                                                                </a>
                                                                <a href="{{ $evidenceUrl }}" target="_blank" rel="noopener noreferrer" class="text-{{ $dispute->status->badgeColor() }}-600 hover:text-{{ $dispute->status->badgeColor() }}-800">
                                                                    <i class="fas fa-external-link-alt text-xs"></i>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @if($dispute->evidence_files && count($dispute->evidence_files) > 0)
                                                        @foreach($dispute->evidence_files as $filePath)
                                                            @php
                                                                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp']);
                                                                $fileName = basename($filePath);
                                                                $fileUrl = Storage::url($filePath);
                                                            @endphp
                                                            <div class="flex items-center gap-1.5 p-1 bg-{{ $dispute->status->badgeColor() }}-100 rounded text-[9px]">
                                                                @if($isImage)
                                                                    <i class="fas fa-image text-{{ $dispute->status->badgeColor() }}-600"></i>
                                                                    <span class="text-{{ $dispute->status->badgeColor() }}-700 truncate flex-1">{{ $fileName }}</span>
                                                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="text-{{ $dispute->status->badgeColor() }}-600 hover:text-{{ $dispute->status->badgeColor() }}-800 mr-1" title="Xem ảnh">
                                                                        <i class="fas fa-eye text-xs"></i>
                                                                    </a>
                                                                    <a href="{{ $fileUrl }}" download class="text-{{ $dispute->status->badgeColor() }}-600 hover:text-{{ $dispute->status->badgeColor() }}-800" title="Tải xuống">
                                                                        <i class="fas fa-download text-xs"></i>
                                                                    </a>
                                                                @else
                                                                    <i class="fas fa-file text-{{ $dispute->status->badgeColor() }}-600"></i>
                                                                    <span class="text-{{ $dispute->status->badgeColor() }}-700 truncate flex-1">{{ $fileName }}</span>
                                                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="text-{{ $dispute->status->badgeColor() }}-600 hover:text-{{ $dispute->status->badgeColor() }}-800 mr-1" title="Xem file">
                                                                        <i class="fas fa-eye text-xs"></i>
                                                                    </a>
                                                                    <a href="{{ $fileUrl }}" download class="text-{{ $dispute->status->badgeColor() }}-600 hover:text-{{ $dispute->status->badgeColor() }}-800" title="Tải xuống">
                                                                        <i class="fas fa-download text-xs"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($canRefund && !$hasDispute)
                                    @if($order->status === \App\Enums\OrderStatus::COMPLETED)
                                        <button type="button" disabled
                                            class="w-full py-1.5 px-3 text-[10px] font-medium bg-gray-400 cursor-not-allowed text-white rounded shadow-sm opacity-50">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Khiếu nại / Yêu cầu hoàn tiền
                                        </button>
                                    @else
                                        <button type="button" onclick="openDisputeModal({{ $item->id }})" 
                                            class="w-full py-1.5 px-3 text-[10px] font-medium bg-red-500 hover:bg-red-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Khiếu nại / Yêu cầu hoàn tiền
                                        </button>
                                    @endif
                                @elseif(!$canRefund && !$hasDispute)
                                    <p class="text-[10px] text-gray-500 text-center py-1">
                                        Không thể khiếu nại đơn hàng này
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($canRefund && !$hasDispute)
                            <div id="disputeModal{{ $item->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto animate-fadeIn">
                                    <div class="p-3 border-b border-gray-200">
                                        <h3 class="text-sm font-bold text-gray-900">Khiếu nại sản phẩm</h3>
                                    </div>
                                    <form id="disputeForm{{ $item->id }}" class="p-3" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="order_item_id" value="{{ $item->id }}">
                                        
                                        <div class="mb-3">
                                            <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                                                Chọn giá trị sản phẩm cần hoàn trả <span class="text-red-500">*</span>
                                            </label>
                                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                                @foreach($productValues as $value)
                                                    <label class="flex items-center p-1.5 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer">
                                                        <input type="checkbox" name="product_value_slugs[]" value="{{ $value->slug }}" 
                                                            class="w-3 h-3 text-primary border-gray-300 rounded focus:ring-primary">
                                                        <span class="ml-2 text-[10px] text-gray-700 font-mono">#{{ $value->slug }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                                                Lý do khiếu nại <span class="text-red-500">*</span>
                                            </label>
                                            <textarea name="reason" rows="4" required minlength="10" maxlength="1000"
                                                class="w-full text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary focus:border-primary p-2"
                                                placeholder="Vui lòng mô tả lý do khiếu nại (tối thiểu 10 ký tự)..."></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                                                Bằng chứng (URL, tùy chọn)
                                            </label>
                                            <div id="evidenceList{{ $item->id }}" class="space-y-1 mb-1"></div>
                                            <div class="flex gap-1">
                                                <input type="url" id="evidenceInput{{ $item->id }}" 
                                                    class="flex-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary focus:border-primary p-1.5"
                                                    placeholder="https://...">
                                                <button type="button" onclick="addEvidence({{ $item->id }})" 
                                                    class="px-2 py-1.5 text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                                                Upload file/hình ảnh <span class="text-gray-500">(Tối đa 10MB/file, tối đa 10 files)</span>
                                            </label>
                                            <div class="space-y-2">
                                                <input type="file" 
                                                    id="evidenceFiles{{ $item->id }}" 
                                                    name="evidence_files[]" 
                                                    multiple
                                                    accept="image/jpeg,image/jpg,image/png,image/webp,.pdf,.doc,.docx,.txt,.rtf"
                                                    class="w-full text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary focus:border-primary p-1.5 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                                <div id="filePreview{{ $item->id }}" class="space-y-1"></div>
                                                <p class="text-[9px] text-gray-500">
                                                    Hình ảnh: JPG, JPEG, PNG, WEBP | File: PDF, DOC, DOCX, TXT, RTF
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex gap-2">
                                            <button type="button" onclick="closeDisputeModal({{ $item->id }})" 
                                                class="flex-1 py-2 text-[10px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                                                Hủy
                                            </button>
                                            <button type="submit" 
                                                class="flex-1 py-2 text-[10px] font-medium bg-red-500 hover:bg-red-600 text-white rounded shadow-sm hover:shadow transition-all duration-200">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Gửi khiếu nại
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Value View Modal -->
        <div id="valueModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-fadeIn">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Chi tiết giá trị sản phẩm</h3>
                        <button type="button" onclick="closeValueModal()" class="text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div id="valueModalContent" class="p-4">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openDisputeModal(itemId) {
        const outsideCheckboxes = document.querySelectorAll('.product-value-checkbox-' + itemId);
        const modalCheckboxes = document.querySelectorAll('#disputeForm' + itemId + ' input[name="product_value_slugs[]"]');
        const submitBtn = document.querySelector('#disputeForm' + itemId + ' button[type="submit"]');
        
        modalCheckboxes.forEach(cb => cb.checked = false);
        
        outsideCheckboxes.forEach(outsideCb => {
            if (outsideCb.checked) {
                const valueSlug = outsideCb.value;
                const modalCb = Array.from(modalCheckboxes).find(cb => cb.value === valueSlug);
                if (modalCb) {
                    modalCb.checked = true;
                }
            }
        });
        
        updateSubmitButtonState(itemId);
        
        modalCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() { 
                updateSubmitButtonState(itemId); 
            });
        });
        
        document.getElementById('disputeModal' + itemId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function updateSubmitButtonState(itemId) {
        const modalCheckboxes = document.querySelectorAll('#disputeForm' + itemId + ' input[name="product_value_slugs[]"]');
        const submitBtn = document.querySelector('#disputeForm' + itemId + ' button[type="submit"]');
        const checkedCount = Array.from(modalCheckboxes).filter(cb => cb.checked).length;
        
        if (checkedCount === 0) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.remove('hover:bg-red-600');
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.add('hover:bg-red-600');
        }
    }

    function closeDisputeModal(itemId) {
        document.getElementById('disputeModal' + itemId).classList.add('hidden');
        document.body.style.overflow = '';
        
        const modalCheckboxes = document.querySelectorAll('#disputeForm' + itemId + ' input[name="product_value_slugs[]"]');
        const outsideCheckboxes = document.querySelectorAll('.product-value-checkbox-' + itemId);
        
        modalCheckboxes.forEach(modalCb => {
            const valueSlug = modalCb.value;
            const outsideCb = Array.from(outsideCheckboxes).find(cb => cb.value === valueSlug);
            if (outsideCb) {
                outsideCb.checked = modalCb.checked;
            }
        });
    }

    function addEvidence(itemId) {
        const input = document.getElementById('evidenceInput' + itemId);
        const url = input.value.trim();
        
        if (!url) return;
        
        const list = document.getElementById('evidenceList' + itemId);
        const div = document.createElement('div');
        div.className = 'flex items-center gap-1 p-1.5 bg-gray-50 rounded';
        div.innerHTML = `
            <input type="hidden" name="evidence[]" value="${url}">
            <span class="flex-1 text-[10px] text-gray-700 truncate">${url}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;
        list.appendChild(div);
        input.value = '';
    }

    @foreach($refundableItems as $refundableItem)
        @php
            $item = $refundableItem['item'];
            $canRefund = $refundableItem['can_refund'];
            $hasDispute = $item->disputes->isNotEmpty();
        @endphp
        @if($canRefund && !$hasDispute)
            document.getElementById('evidenceFiles{{ $item->id }}').addEventListener('change', function(e) {
                const preview = document.getElementById('filePreview{{ $item->id }}');
                preview.innerHTML = '';
                
                Array.from(e.target.files).forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-2 p-1.5 bg-gray-50 rounded text-[10px]';
                    
                    const icon = file.type.startsWith('image/') ? 'fa-image' : 'fa-file';
                    const size = (file.size / 1024 / 1024).toFixed(2);
                    
                    div.innerHTML = `
                        <i class="fas ${icon} text-gray-500"></i>
                        <span class="flex-1 text-gray-700 truncate">${file.name}</span>
                        <span class="text-gray-500">${size} MB</span>
                        <button type="button" onclick="removeFile({{ $item->id }}, ${index})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    `;
                    preview.appendChild(div);
                });
            });
        @endif
    @endforeach

    function removeFile(itemId, index) {
        const fileInput = document.getElementById('evidenceFiles' + itemId);
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        fileInput.dispatchEvent(new Event('change'));
    }

    @foreach($refundableItems as $refundableItem)
        @php
            $item = $refundableItem['item'];
            $canRefund = $refundableItem['can_refund'];
            $hasDispute = $item->disputes->isNotEmpty();
        @endphp
        @if($canRefund && !$hasDispute)
            document.getElementById('disputeForm{{ $item->id }}').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const checkedValues = Array.from(document.querySelectorAll('#disputeForm{{ $item->id }} input[name="product_value_slugs[]"]:checked')).map(cb => cb.value);
                
                if (checkedValues.length === 0) {
                    Swal.fire({
                        title: 'Lỗi',
                        html: `
                            <div style="text-align: center; padding: 1rem 0;">
                                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                                </div>
                                <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Vui lòng chọn ít nhất một giá trị sản phẩm.</p>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                        width: '480px'
                    });
                    return;
                }
                
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('order_item_id', {{ $item->id }});
                formData.append('reason', this.querySelector('textarea[name="reason"]').value);
                
                const evidenceInputs = this.querySelectorAll('input[name="evidence[]"]');
                evidenceInputs.forEach(input => {
                    if (input.value) {
                        formData.append('evidence[]', input.value);
                    }
                });
                
                checkedValues.forEach(slug => {
                    formData.append('product_value_slugs[]', slug);
                });
                
                const fileInput = document.getElementById('evidenceFiles{{ $item->id }}');
                if (fileInput.files.length > 10) {
                    Swal.fire({
                        title: 'Lỗi',
                        html: `
                            <div style="text-align: center; padding: 1rem 0;">
                                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                                </div>
                                <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Tối đa 10 files được phép upload.</p>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                        width: '480px'
                    });
                    return;
                }
                
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    if (file.size > 10 * 1024 * 1024) {
                        Swal.fire({
                            title: 'Lỗi',
                            html: `
                                <div style="text-align: center; padding: 1rem 0;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);">
                                        <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                                    </div>
                                    <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">File "${file.name}" vượt quá 10MB.</p>
                                </div>
                            `,
                            icon: 'warning',
                            confirmButtonColor: '#f59e0b',
                            confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                            width: '480px'
                        });
                        return;
                    }
                    formData.append('evidence_files[]', file);
                }
                
                fetch('{{ route("orders.dispute", $order->slug) }}', {
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
                            html: `
                                <div style="text-align: center; padding: 1rem 0;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                        <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                                    </div>
                                    <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message}</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                            width: '480px'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi',
                            html: `
                                <div style="text-align: center; padding: 1rem 0;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                        <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                    </div>
                                    <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message || 'Có lỗi xảy ra.'}</p>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                            width: '480px'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi',
                        html: `
                            <div style="text-align: center; padding: 1rem 0;">
                                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                    <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                </div>
                                <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Có lỗi xảy ra khi gửi khiếu nại.</p>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                        width: '480px'
                    });
                });
            });
        @endif
    @endforeach

    document.addEventListener('click', function(e) {
        @foreach($refundableItems as $refundableItem)
            @php
                $item = $refundableItem['item'];
                $canRefund = $refundableItem['can_refund'];
                $hasDispute = $item->disputes->isNotEmpty();
            @endphp
            @if($canRefund && !$hasDispute)
                const modal{{ $item->id }} = document.getElementById('disputeModal{{ $item->id }}');
                if (modal{{ $item->id }} && e.target === modal{{ $item->id }}) {
                    closeDisputeModal({{ $item->id }});
                }
            @endif
        @endforeach
        
        const valueModal = document.getElementById('valueModal');
        if (valueModal && e.target === valueModal) {
            closeValueModal();
        }
    });

    function showValueModal(slug) {
        const modal = document.getElementById('valueModal');
        const content = document.getElementById('valueModalContent');
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        content.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        `;
        
        fetch(`/product-values/${slug}/data`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = `
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700">#${data.value.slug}</span>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded ${
                                data.value.status_color === 'success' ? 'bg-green-100 text-green-700' :
                                (data.value.status_color === 'warning' ? 'bg-yellow-100 text-yellow-700' :
                                (data.value.status_color === 'danger' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'))
                            }">
                                ${data.value.status}
                            </span>
                        </div>
                `;
                
                if (data.data && Object.keys(data.data).length > 0) {
                    html += `
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Thông tin giá trị:</h4>
                            <div class="space-y-2">
                    `;
                    
                    if (data.data.value) {
                        html += `
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Giá trị:</label>
                                <code class="block p-3 bg-white border border-gray-300 rounded text-sm font-mono text-gray-900 break-all">
                                    ${data.data.value}
                                </code>
                            </div>
                        `;
                    }
                    
                    if (data.data.password) {
                        html += `
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Mật khẩu:</label>
                                <code class="block p-3 bg-white border border-gray-300 rounded text-sm font-mono text-gray-900 break-all">
                                    ${data.data.password}
                                </code>
                            </div>
                        `;
                    }
                    
                    Object.keys(data.data).forEach(key => {
                        if (!['value', 'password'].includes(key)) {
                            const value = typeof data.data[key] === 'object' 
                                ? JSON.stringify(data.data[key], null, 2)
                                : data.data[key];
                            const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            html += `
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">${label}:</label>
                                    <code class="block p-3 bg-white border border-gray-300 rounded text-sm font-mono text-gray-900 break-all">
                                        ${value}
                                    </code>
                                </div>
                            `;
                        }
                    });
                    
                    html += `
                            </div>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 text-center">
                            <p class="text-sm text-gray-600">Không có dữ liệu hiển thị</p>
                        </div>
                    `;
                }
                
                html += `</div>`;
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-sm text-red-600">${data.message || 'Có lỗi xảy ra khi tải dữ liệu.'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-sm text-red-600">Có lỗi xảy ra khi tải dữ liệu.</p>
                </div>
            `;
        });
    }

    function closeValueModal() {
        document.getElementById('valueModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function updateCountdown() {
        const countdownEl = document.getElementById('countdown');
        if (!countdownEl) return;
        
        const deadline = new Date(countdownEl.dataset.deadline);
        const now = new Date();
        const diff = deadline - now;
        
        if (diff <= 0) {
            countdownEl.textContent = '0 giờ 0 phút';
            setTimeout(() => location.reload(), 2000);
            return;
        }
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        if (hours > 0) {
            countdownEl.textContent = `${hours} giờ ${minutes} phút`;
        } else if (minutes > 0) {
            countdownEl.textContent = `${minutes} phút ${seconds} giây`;
        } else {
            countdownEl.textContent = `${seconds} giây`;
        }
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);

    function confirmOrder() {
        Swal.fire({
            title: 'Xác nhận đơn hàng?',
            html: `
                <div style="text-align: center; padding: 1rem 0;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                    </div>
                    <p style="font-size: 14px; color: #374151; margin: 0;">Sau khi xác nhận, bạn sẽ không thể khiếu nại đơn hàng này nữa.</p>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">Tiền sẽ được chuyển cho người bán.</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-check mr-2"></i>Xác nhận',
            cancelButtonText: 'Hủy',
            width: '480px'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("orders.confirm", $order->slug) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            html: `
                                <div style="text-align: center; padding: 1rem 0;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                        <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                                    </div>
                                    <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message}</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                            width: '480px'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi',
                            html: `
                                <div style="text-align: center; padding: 1rem 0;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                        <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                    </div>
                                    <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message || 'Có lỗi xảy ra.'}</p>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                            width: '480px'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi xác nhận đơn hàng.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }

    function withdrawDispute(disputeSlug) {
        Swal.fire({
            title: 'Rút khiếu nại?',
            html: `
                <div style="text-align: center; padding: 1rem 0;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #6b7280, #4b5563); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(107, 114, 128, 0.3);">
                        <i class="fas fa-undo" style="font-size: 40px; color: white;"></i>
                    </div>
                    <p style="font-size: 14px; color: #374151; margin: 0;">Bạn có chắc chắn muốn rút khiếu nại này?</p>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">Sau khi rút, bạn có thể xác nhận đơn hàng hoặc gửi khiếu nại mới.</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#6b7280',
            cancelButtonColor: '#ef4444',
            confirmButtonText: '<i class="fas fa-undo mr-2"></i>Rút khiếu nại',
            cancelButtonText: 'Hủy',
            width: '480px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Đang xử lý...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/disputes/${disputeSlug}/withdraw`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
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
                        }).then(() => {
                            window.location.reload();
                        });
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
                        text: 'Có lỗi xảy ra khi rút khiếu nại.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }
</script>
@endpush


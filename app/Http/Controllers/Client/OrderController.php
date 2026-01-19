<?php

namespace App\Http\Controllers\Client;

use App\Models\Order;
use App\Models\Config;
use App\Models\Dispute;
use App\Enums\OrderStatus;
use App\Enums\DisputeStatus;
use App\Models\ProductValue;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Enums\ProductValueStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Services\DisputeService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Order::where('buyer_id', $user->id)
            ->with(['seller', 'items.productVariant.product', 'items.productValues'])
            ->latest();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return view('client.pages.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $order->load([
            'seller',
            'items.productVariant.product',
            'items.productValues',
            'disputes.orderItem',
            'disputes.items.productValue',
            'refunds.items.productValue'
        ]);

        $refundableItems = [];
        foreach ($order->items as $item) {
            $soldValues = $item->productValues()->where('status', ProductValueStatus::SOLD)->get();
            $hasOpenDispute = $item->disputes()->whereIn('status', [DisputeStatus::OPEN, DisputeStatus::REVIEWING])->exists();
            $hasPendingRefund = $order->refunds()->where('status', \App\Enums\RefundStatus::PENDING)->exists();

            if ($soldValues->count() > 0 && !$hasOpenDispute && !$hasPendingRefund && in_array($order->status, [OrderStatus::PAID, OrderStatus::COMPLETED])) {
                $refundableItems[] = [
                    'item' => $item,
                    'product_values' => $soldValues,
                    'can_refund' => true
                ];
            } else {
                $refundableItems[] = [
                    'item' => $item,
                    'product_values' => $soldValues,
                    'can_refund' => false
                ];
            }
        }

        return view('client.pages.orders.show', compact('order', 'refundableItems'));
    }

    public function createDispute(Request $request, Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $allowedImageMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $allowedFileExtensions = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
        $allowedFileMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/rtf'];
        
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'product_value_slugs' => 'required|array|min:1',
            'product_value_slugs.*' => 'required|exists:product_values,slug',
            'reason' => 'required|string|min:10|max:1000',
            'evidence' => 'nullable|array',
            'evidence.*' => 'nullable|string|url',
            'evidence_files' => 'nullable|array|max:10',
            'evidence_files.*' => 'file|max:10240|mimes:jpeg,jpg,png,webp,pdf,doc,docx,txt,rtf',
        ], [
            'order_item_id.required' => 'Vui lòng chọn sản phẩm.',
            'order_item_id.exists' => 'Sản phẩm không tồn tại.',
            'product_value_slugs.required' => 'Vui lòng chọn ít nhất một giá trị sản phẩm.',
            'product_value_slugs.array' => 'Dữ liệu không hợp lệ.',
            'product_value_slugs.min' => 'Vui lòng chọn ít nhất một giá trị sản phẩm.',
            'product_value_slugs.*.exists' => 'Giá trị sản phẩm không tồn tại.',
            'reason.required' => 'Vui lòng nhập lý do khiếu nại.',
            'reason.min' => 'Lý do khiếu nại phải có ít nhất 10 ký tự.',
            'reason.max' => 'Lý do khiếu nại không được vượt quá 1000 ký tự.',
            'evidence.array' => 'Bằng chứng không hợp lệ.',
            'evidence.*.url' => 'Bằng chứng phải là URL hợp lệ.',
            'evidence_files.array' => 'Files không hợp lệ.',
            'evidence_files.max' => 'Tối đa 10 files được phép upload.',
            'evidence_files.*.file' => 'File không hợp lệ.',
            'evidence_files.*.max' => 'File không được vượt quá 10MB.',
            'evidence_files.*.mimes' => 'File phải là: jpeg, jpg, png, webp, pdf, doc, docx, txt, hoặc rtf.',
        ]);

        $uploadedFiles = $request->file('evidence_files', []);
        $filePaths = [];
        
        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $mimeType = $file->getMimeType();
                $extension = strtolower($file->getClientOriginalExtension());
                
                $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'sh', 'js', 'html', 'htm'];
                if (in_array($extension, $dangerousExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => "File type không được phép: .{$extension}"
                    ], 422);
                }
                
                if (in_array($mimeType, $allowedImageMimes)) {
                    $fileName = 'disputes/' . uniqid() . '_' . time() . '.' . $extension;
                    $image = Image::make($file->getRealPath());

                    $image->encode($extension === 'png' ? 'png' : 'jpg', 85);
                    Storage::disk('public')->put($fileName, $image->stream());

                    $filePaths[] = $fileName;
                } 
                elseif (in_array($mimeType, $allowedFileMimes)) {
                    $fileContent = file_get_contents($file->getRealPath(), false, null, 0, 512);
                    
                    if (preg_match('/<\?php|<\?=|<script/i', $fileContent)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File chứa nội dung không được phép.'
                        ], 422);
                    }
                    
                    $fileName = 'disputes/' . uniqid() . '_' . time() . '.' . $extension;
                    Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));
                    
                    $filePaths[] = $fileName;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "File type không được hỗ trợ: {$mimeType}"
                    ], 422);
                }
            }
        }

        $orderItem = $order->items()->findOrFail($validated['order_item_id']);

        $slugs = array_values(array_unique($validated['product_value_slugs']));

        $productValues = ProductValue::whereIn('slug', $slugs)
            ->where('order_item_id', $orderItem->id)
            ->where('status', ProductValueStatus::SOLD)
            ->get();

        if ($productValues->count() !== count($slugs)) {
            return response()->json([
                'success' => false,
                'message' => 'Một số giá trị sản phẩm không hợp lệ hoặc đã được hoàn trả.'
            ], 422);
        }

        $productValueIds = $productValues->pluck('id')->toArray();

        $existingDisputeItems = \App\Models\DisputeItem::whereIn('product_value_id', $productValueIds)
            ->whereHas('dispute', function($query) {
                $query->whereIn('status', [DisputeStatus::OPEN, DisputeStatus::REVIEWING]);
            })
            ->exists();

        if ($existingDisputeItems) {
            return response()->json([
                'success' => false,
                'message' => 'Một số giá trị sản phẩm đã có khiếu nại đang được xử lý.'
            ], 422);
        }

        DB::transaction(function () use ($order, $orderItem, $productValues, $validated, $filePaths) {
            $dispute = Dispute::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'buyer_id' => Auth::id(),
                'seller_id' => $order->seller_id,
                'reason' => $validated['reason'],
                'evidence' => $validated['evidence'] ?? null,
                'evidence_files' => !empty($filePaths) ? $filePaths : null,
                'status' => DisputeStatus::OPEN,
            ]);

            foreach ($productValues as $productValue) {
                \App\Models\DisputeItem::create([
                    'dispute_id' => $dispute->id,
                    'product_value_id' => $productValue->id,
                ]);
            }

            if ($order->status === OrderStatus::PAID || $order->status === OrderStatus::COMPLETED) {
                $order->changeStatus(OrderStatus::DISPUTED);
            }
        });



        return response()->json([
            'success' => true,
            'message' => 'Khiếu nại đã được gửi thành công. Người bán sẽ xem xét trong vòng ' . Config::getConfig('dispute_response_hours', 48) . ' giờ.'
        ]);
    }

    public function getValueData(ProductValue $value)
    {
        $user = Auth::user();
        
        if (!$value->canViewDataBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem giá trị sản phẩm này.'
            ], 403);
        }

        $data = $value->getDecryptedDataFor($user);

        return response()->json([
            'success' => true,
            'value' => [
                'slug' => $value->slug,
                'status' => $value->status->label(),
                'status_color' => $value->status->badgeColor(),
            ],
            'data' => $data,
        ]);
    }

    public function confirmOrder(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        if ($order->status !== OrderStatus::PAID) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không ở trạng thái chờ xác nhận.'
            ], 422);
        }

        if ($order->disputes()->whereIn('status', [DisputeStatus::OPEN, DisputeStatus::REVIEWING])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đang có khiếu nại chưa được xử lý.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($order) {
                $order->update(['status' => OrderStatus::COMPLETED]);
                
                WalletService::paySellerForOrder($order);
            });

            $commissionRate = (float) Config::getConfig('commission_rate', 10);

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận đơn hàng thành công! Cảm ơn bạn đã mua hàng.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function canConfirmOrder(Order $order): bool
    {
        if ($order->buyer_id !== Auth::id()) {
            return false;
        }

        if ($order->status !== OrderStatus::PAID) {
            return false;
        }

        if ($order->disputes()->whereIn('status', [DisputeStatus::OPEN, DisputeStatus::REVIEWING])->exists()) {
            return false;
        }

        return true;
    }

    public function withdrawDispute(Dispute $dispute)
    {
        if ($dispute->buyer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        if (!in_array($dispute->status, [DisputeStatus::OPEN, DisputeStatus::REVIEWING])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể rút khiếu nại này.'
            ], 422);
        }

        try {
            DisputeService::withdrawDispute($dispute);

            return response()->json([
                'success' => true,
                'message' => 'Đã rút khiếu nại thành công. Bạn có thể xác nhận đơn hàng hoặc gửi khiếu nại mới.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}

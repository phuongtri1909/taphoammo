<?php

namespace App\Http\Controllers\Client;

use App\Models\Bank;
use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    protected DepositService $depositService;

    public function __construct(DepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    public function index()
    {
        $banks = Bank::where('status', true)->get();
        $deposits = Deposit::where('user_id', Auth::id())
            ->with('bank')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('client.pages.deposit.index', compact('banks', 'deposits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000',
            'bank_id' => 'required|exists:banks,id'
        ], [
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.integer' => 'Số tiền phải là số nguyên',
            'amount.min' => 'Số tiền tối thiểu là 10,000₫',
            'bank_id.required' => 'Vui lòng chọn ngân hàng',
            'bank_id.exists' => 'Ngân hàng không hợp lệ',
        ]);

        try {
            $deposit = $this->depositService->createDeposit(
                Auth::id(),
                $request->input('bank_id'),
                $request->input('amount')
            );

            $bank = Bank::find($request->input('bank_id'));
            $bankInfo = $this->depositService->getBankInfoWithQR(
                $bank,
                $deposit->transaction_code,
                $request->input('amount')
            );

            return response()->json([
                'success' => true,
                'deposit' => [
                    'slug' => $deposit->slug,
                    'transaction_code' => $deposit->transaction_code,
                    'amount' => $deposit->amount,
                    'amount_formatted' => $deposit->amount_formatted,
                ],
                'bank_info' => $bankInfo,
                'message' => 'Vui lòng chuyển khoản theo thông tin bên dưới'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating deposit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo giao dịch'
            ], 500);
        }
    }

    public function callback(Request $request)
    {

        $payload = $request->getContent();
        $signature = $request->header('X-Casso-Signature');

        if (!$signature) {
            Log::warning('signature casso không tồn tại');
            return response()->json(['success' => false, 'message' => 'signature casso không tồn tại'], 401);
        }

        if (!$this->depositService->verifyCassoSignature($payload, $signature)) {
            Log::warning('signature casso không hợp lệ');
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON payload không hợp lệ');
            return response()->json(['success' => false, 'message' => 'Invalid JSON payload'], 400);
        }

        $result = $this->depositService->processCallback($data);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function sseTransactionUpdates(Request $request)
    {
        $transactionCode = $request->get('transaction_code');

        if (!$transactionCode) {
            return response('Missing transaction_code', 400);
        }

        return response()->stream(function () use ($transactionCode) {
            $sseDir = storage_path('app/sse_transactions');
            $filename = $sseDir . '/sse_transaction_' . $transactionCode . '.json';
            $lastModified = 0;
            $timeout = 300; // 5 minutes
            $startTime = time();

            while (true) {
                if (time() - $startTime > $timeout) {
                    echo "data: " . json_encode(['type' => 'timeout']) . "\n\n";
                    break;
                }

                if (file_exists($filename)) {
                    $currentModified = filemtime($filename);

                    if ($currentModified > $lastModified) {
                        $data = json_decode(file_get_contents($filename), true);
                        echo "data: " . json_encode($data) . "\n\n";
                        $lastModified = $currentModified;

                        if (isset($data['status']) && $data['status'] === 'success') {
                            echo "data: " . json_encode(['type' => 'close']) . "\n\n";
                            @unlink($filename);
                            break;
                        }
                    }
                }

                ob_flush();
                flush();
                sleep(2);

                if (connection_aborted()) {
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function checkStatus(Request $request)
    {
        $transactionCode = $request->get('transaction_code');

        if (!$transactionCode) {
            return response()->json(['success' => false, 'message' => 'Missing transaction code'], 400);
        }

        $deposit = Deposit::where('transaction_code', $transactionCode)
            ->where('user_id', Auth::id())
            ->first();

        if (!$deposit) {
            return response()->json(['success' => false, 'message' => 'Deposit not found'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $deposit->status->value,
            'status_label' => $deposit->status->label(),
            'is_successful' => $deposit->isSuccessful(),
        ]);
    }
}

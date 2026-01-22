<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    /**
     * Hiển thị trang kết nối Telegram
     */
    public function connect()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sign-in');
        }

        // Tạo mã xác nhận duy nhất (8 ký tự, dễ đọc)
        $verificationCode = strtoupper(Str::random(8));
        // Lưu vào cache với thời gian hết hạn 10 phút
        Cache::put("telegram_verification_{$verificationCode}", $user->id, now()->addMinutes(10));

        $botUsername = Config::getConfig('telegram_bot_username', 'YourBotName');
        $botToken = Config::getConfig('telegram_bot_token', '');

        return view('client.pages.telegram.connect', [
            'user' => $user,
            'verificationCode' => $verificationCode,
            'botUsername' => $botUsername,
            'botToken' => $botToken,
        ]);
    }

    /**
     * Xử lý webhook từ Telegram Bot
     */
    public function webhook(Request $request)
    {
        // Luôn trả về 200 OK cho Telegram để tránh retry
        // Telegram sẽ retry nếu không nhận được 200 OK
        
        try {
            $data = $request->all();
            
            // Log minimal để tránh spam log
            if (isset($data['message'])) {
                Log::info('Telegram webhook received', [
                    'chat_id' => $data['message']['chat']['id'] ?? null,
                    'text' => $data['message']['text'] ?? null
                ]);
            }
            
            // Kiểm tra token
            try {
                $botToken = Config::getConfig('telegram_bot_token', '');
            } catch (\Exception $e) {
                Log::error('Error getting bot token', ['error' => $e->getMessage()]);
                return response()->json(['ok' => true], 200);
            }
            
            if (empty($botToken)) {
                Log::error('Telegram bot token not configured');
                return response()->json(['ok' => true], 200);
            }

            // Xử lý message từ user
            if (isset($data['message'])) {
                $message = $data['message'];
                $chatId = $message['chat']['id'] ?? null;
                $text = $message['text'] ?? '';
                $username = $message['from']['username'] ?? null;

                if (!$chatId) {
                    Log::warning('Telegram webhook: No chat_id in message', ['message' => $message]);
                    return response()->json(['ok' => true, 'error' => 'No chat_id'], 200);
                }

                // Kiểm tra mã xác nhận (chuyển sang chữ hoa để so sánh)
                $textUpper = strtoupper(trim($text));
                
                try {
                    $userId = Cache::get("telegram_verification_{$textUpper}");
                } catch (\Exception $e) {
                    Log::error('Error getting verification code from cache', ['error' => $e->getMessage()]);
                    return response()->json(['ok' => true], 200);
                }

                if ($userId) {
                    // Xác nhận thành công
                    try {
                        $user = \App\Models\User::find($userId);
                    } catch (\Exception $e) {
                        Log::error('Error finding user', ['error' => $e->getMessage(), 'user_id' => $userId]);
                        return response()->json(['ok' => true], 200);
                    }
                    
                    if ($user) {
                        // Kiểm tra xem chat_id này đã được sử dụng bởi user khác chưa
                        try {
                            $existingUser = \App\Models\User::where('telegram_chat_id', (string) $chatId)
                                ->where('id', '!=', $user->id)
                                ->first();
                            
                            if ($existingUser) {
                                try {
                                    $telegramService = new TelegramNotificationService();
                                    $telegramService->sendToChatId($chatId, "❌ <b>Lỗi!</b>\n\nChat ID này đã được sử dụng bởi tài khoản khác. Vui lòng liên hệ admin để được hỗ trợ.");
                                } catch (\Exception $e) {
                                    Log::error('Failed to send Telegram message', ['error' => $e->getMessage()]);
                                }
                                return response()->json(['ok' => true, 'success' => false, 'message' => 'Chat ID already in use'], 200);
                            }

                            $user->update([
                                'telegram_chat_id' => (string) $chatId,
                                'telegram_username' => $username,
                                'telegram_connected_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error updating user Telegram info', [
                                'error' => $e->getMessage(),
                                'user_id' => $user->id,
                                'chat_id' => $chatId
                            ]);
                            return response()->json(['ok' => true], 200);
                        }

                        // Xóa mã xác nhận khỏi cache
                        Cache::forget("telegram_verification_{$textUpper}");

                        // Gửi thông báo xác nhận
                        try {
                            $telegramService = new TelegramNotificationService();
                            $telegramService->sendToChatId($chatId, "✅ <b>Kết nối thành công!</b>\n\nBạn đã kết nối tài khoản Telegram với hệ thống. Bạn sẽ nhận được thông báo về đơn hàng, giao dịch và các sự kiện quan trọng qua Telegram.");
                        } catch (\Exception $e) {
                            Log::error('Failed to send Telegram confirmation message', ['error' => $e->getMessage()]);
                        }

                        Log::info('Telegram connected successfully', [
                            'user_id' => $user->id,
                            'chat_id' => $chatId,
                            'username' => $username
                        ]);

                        return response()->json(['ok' => true, 'success' => true, 'message' => 'Connected successfully'], 200);
                    }
                } elseif ($text === '/start') {
                    // Gửi hướng dẫn
                    $helpMessage = "👋 <b>Chào mừng đến với Telegram Bot!</b>\n\n";
                    $helpMessage .= "Để kết nối tài khoản, vui lòng:\n";
                    $helpMessage .= "1. Truy cập trang cá nhân trên website\n";
                    $helpMessage .= "2. Click vào 'Kết nối Telegram'\n";
                    $helpMessage .= "3. Nhập mã xác nhận vào đây\n\n";
                    $helpMessage .= "Hoặc nhập mã xác nhận của bạn:";

                    try {
                        $telegramService = new TelegramNotificationService();
                        $telegramService->sendToChatId($chatId, $helpMessage);
                    } catch (\Exception $e) {
                        Log::error('Failed to send Telegram help message', ['error' => $e->getMessage()]);
                    }

                    return response()->json(['ok' => true, 'success' => true], 200);
                } else {
                    // Gửi thông báo hướng dẫn
                    $helpMessage = "❌ <b>Mã xác nhận không đúng!</b>\n\n";
                    $helpMessage .= "Vui lòng:\n";
                    $helpMessage .= "1. Truy cập trang cá nhân trên website\n";
                    $helpMessage .= "2. Click vào 'Kết nối Telegram'\n";
                    $helpMessage .= "3. Sao chép mã xác nhận và gửi lại vào đây";

                    try {
                        $telegramService = new TelegramNotificationService();
                        $telegramService->sendToChatId($chatId, $helpMessage);
                    } catch (\Exception $e) {
                        Log::error('Failed to send Telegram error message', ['error' => $e->getMessage()]);
                    }

                    return response()->json(['ok' => true, 'success' => true], 200);
                }
            }

            // Nếu không có message, vẫn trả về 200 OK
            return response()->json(['ok' => true, 'success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Luôn trả về 200 OK để Telegram không retry
            return response()->json(['ok' => true, 'error' => 'Internal server error'], 200);
        }
    }

    /**
     * Ngắt kết nối Telegram
     */
    public function disconnect(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.'
            ], 401);
        }

        if (!$user->hasTelegramConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa kết nối Telegram.'
            ], 400);
        }

        // Gửi thông báo ngắt kết nối
        if ($user->telegram_chat_id) {
            try {
                $telegramService = new TelegramNotificationService();
                $telegramService->sendToChatId($user->telegram_chat_id, "🔌 <b>Đã ngắt kết nối</b>\n\nBạn đã ngắt kết nối tài khoản Telegram với hệ thống. Bạn sẽ không còn nhận được thông báo qua Telegram nữa.");
            } catch (\Exception $e) {
                Log::warning('Failed to send disconnect notification', ['error' => $e->getMessage()]);
            }
        }

        // Xóa thông tin Telegram
        $user->update([
            'telegram_chat_id' => null,
            'telegram_username' => null,
            'telegram_connected_at' => null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã ngắt kết nối Telegram thành công!'
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'Đã ngắt kết nối Telegram thành công!');
    }

    /**
     * Kiểm tra trạng thái kết nối (AJAX)
     */
    public function checkStatus()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'connected' => false
            ], 401);
        }

        // Đảm bảo telegram_connected_at được format đúng
        $connectedAt = null;
        if ($user->telegram_connected_at) {
            if (is_string($user->telegram_connected_at)) {
                $connectedAt = Carbon::parse($user->telegram_connected_at)->format('d/m/Y H:i');
            } elseif (is_object($user->telegram_connected_at) && method_exists($user->telegram_connected_at, 'format')) {
                $connectedAt = $user->telegram_connected_at->format('d/m/Y H:i');
            }
        }

        return response()->json([
            'success' => true,
            'connected' => $user->hasTelegramConnected(),
            'username' => $user->telegram_username,
            'connected_at' => $connectedAt,
        ]);
    }
}

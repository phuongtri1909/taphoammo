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
    public function connect()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sign-in');
        }

        $verificationCode = strtoupper(Str::random(8));
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

    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            
            try {
                $botToken = Config::getConfig('telegram_bot_token', '');
            } catch (\Exception $e) {
                Log::error('Error getting bot token', ['error' => $e->getMessage()]);
                return response()->json(['ok' => true], 200);
            }
            
            if (empty($botToken)) {
                return response()->json(['ok' => true], 200);
            }

            if (isset($data['message'])) {
                $message = $data['message'];
                $chatId = $message['chat']['id'] ?? null;
                $text = $message['text'] ?? '';
                $username = $message['from']['username'] ?? null;

                if (!$chatId) {
                    return response()->json(['ok' => true], 200);
                }

                $textUpper = strtoupper(trim($text));
                
                try {
                    $userId = Cache::get("telegram_verification_{$textUpper}");
                } catch (\Exception $e) {
                    Log::error('Error getting verification code from cache', ['error' => $e->getMessage()]);
                    return response()->json(['ok' => true], 200);
                }

                if ($userId) {
                    try {
                        $user = \App\Models\User::find($userId);
                    } catch (\Exception $e) {
                        Log::error('Error finding user', ['error' => $e->getMessage(), 'user_id' => $userId]);
                        return response()->json(['ok' => true], 200);
                    }
                    
                    if ($user) {
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

                        Cache::forget("telegram_verification_{$textUpper}");

                        try {
                            $telegramService = new TelegramNotificationService();
                            $telegramService->sendToChatId($chatId, "✅ <b>Kết nối thành công!</b>\n\nBạn đã kết nối tài khoản Telegram với hệ thống. Bạn sẽ nhận được thông báo về đơn hàng, giao dịch và các sự kiện quan trọng qua Telegram.");
                        } catch (\Exception $e) {
                            Log::error('Failed to send Telegram confirmation message', ['error' => $e->getMessage()]);
                        }

                        return response()->json(['ok' => true, 'success' => true, 'message' => 'Connected successfully'], 200);
                    }
                } elseif ($text === '/start') {
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

            return response()->json(['ok' => true, 'success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['ok' => true, 'error' => 'Internal server error'], 200);
        }
    }

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

        if ($user->telegram_chat_id) {
            try {
                $telegramService = new TelegramNotificationService();
                $telegramService->sendToChatId($user->telegram_chat_id, "🔌 <b>Đã ngắt kết nối</b>\n\nBạn đã ngắt kết nối tài khoản Telegram với hệ thống. Bạn sẽ không còn nhận được thông báo qua Telegram nữa.");
            } catch (\Exception $e) {
                Log::warning('Failed to send disconnect notification', ['error' => $e->getMessage()]);
            }
        }

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

    public function checkStatus()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'connected' => false
            ], 401);
        }

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

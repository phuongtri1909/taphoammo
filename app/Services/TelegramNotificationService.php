<?php

namespace App\Services;

use App\Models\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected $token;
    protected $chatId1;
    protected $chatId2;

    public function __construct()
    {
        $this->token = trim(Config::getConfig('telegram_bot_token', ''));
        $this->chatId1 = trim(Config::getConfig('telegram_chat_id_1', ''));
        $this->chatId2 = trim(Config::getConfig('telegram_chat_id_2', ''));
    }

    /**
     * Gửi thông báo đến Telegram
     * 
     * @param string $message
     * @param bool $useChatId2 Nếu true, dùng chat_id_2 cho rút tiền, nếu không có thì dùng chat_id_1
     * @return bool
     */
    public function sendMessage(string $message, bool $useChatId2 = false): bool
    {
        if (empty($this->token)) {
            Log::warning('Telegram bot token chưa được cấu hình');
            return false;
        }

        $chatId = $this->chatId1;
        
        if ($useChatId2) {
            $chatId = !empty($this->chatId2) ? $this->chatId2 : $this->chatId1;
        }

        if (empty($chatId)) {
            Log::warning('Telegram chat_id chưa được cấu hình');
            return false;
        }

        return $this->sendToTelegram($this->token, $chatId, $message);
    }

    /**
     * Gửi tin nhắn đến Telegram Bot API
     */
    protected function sendToTelegram(string $token, string $chatId, string $message): bool
    {
        try {
            $url = "https://api.telegram.org/bot{$token}/sendMessage";

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(5)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::warning('Telegram API trả về lỗi', [
                    'url' => $url,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi Telegram notification', [
                'token' => substr($token, 0, 10) . '...',
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Gửi thông báo đơn hàng sản phẩm mới
     */
    public function sendOrderNotification($order): bool
    {
        $orderUrl = url("/admin/orders/{$order->slug}");
        
        $message = "🛒 <b>Đơn hàng sản phẩm mới</b>\n\n";
        $message .= "📦 Mã đơn: <code>{$order->slug}</code>\n";
        $message .= "👤 Người mua: {$order->buyer->full_name} ({$order->buyer->email})\n";
        $message .= "🏪 Người bán: {$order->seller->full_name}\n";
        $message .= "💰 Tổng tiền: <b>" . number_format($order->total_amount, 0, ',', '.') . "₫</b>\n";
        $message .= "📊 Số lượng sản phẩm: {$order->items->sum('quantity')}\n\n";
        $message .= "🔗 <a href=\"{$orderUrl}\">Xem chi tiết</a>";

        return $this->sendMessage($message, false);
    }

    /**
     * Gửi thông báo đơn hàng dịch vụ mới
     */
    public function sendServiceOrderNotification($serviceOrder): bool
    {
        $orderUrl = url("/admin/service-orders/{$serviceOrder->slug}");
        
        $message = "🔧 <b>Đơn hàng dịch vụ mới</b>\n\n";
        $message .= "📦 Mã đơn: <code>{$serviceOrder->slug}</code>\n";
        $message .= "👤 Người mua: {$serviceOrder->buyer->full_name} ({$serviceOrder->buyer->email})\n";
        $message .= "🏪 Người bán: {$serviceOrder->seller->full_name}\n";
        $message .= "🔧 Dịch vụ: {$serviceOrder->serviceVariant->service->name}\n";
        $message .= "📋 Biến thể: {$serviceOrder->serviceVariant->name}\n";
        $message .= "💰 Tổng tiền: <b>" . number_format($serviceOrder->total_amount, 0, ',', '.') . "₫</b>\n\n";
        $message .= "🔗 <a href=\"{$orderUrl}\">Xem chi tiết</a>";

        return $this->sendMessage($message, false);
    }

    /**
     * Gửi thông báo yêu cầu rút tiền mới
     */
    public function sendWithdrawalNotification($withdrawal): bool
    {
        $withdrawalUrl = url("/admin/withdrawals/{$withdrawal->slug}");
        
        $message = "💵 <b>Yêu cầu rút tiền mới</b>\n\n";
        $message .= "📋 Mã yêu cầu: <code>{$withdrawal->slug}</code>\n";
        $message .= "👤 Người yêu cầu: {$withdrawal->user->full_name} ({$withdrawal->user->email})\n";
        $message .= "💰 Số tiền: <b>" . number_format($withdrawal->amount, 0, ',', '.') . "₫</b>\n";
        $message .= "🏦 Ngân hàng: {$withdrawal->bank_name}\n";
        $message .= "💳 Số tài khoản: <code>{$withdrawal->bank_account_number}</code>\n";
        $message .= "👤 Chủ tài khoản: {$withdrawal->bank_account_name}\n\n";
        $message .= "🔗 <a href=\"{$withdrawalUrl}\">Xem chi tiết</a>";

        return $this->sendMessage($message, true);
    }

    /**
     * Gửi thông báo đến user cụ thể qua Telegram
     * 
     * @param int $userId
     * @param string $message
     * @return bool
     */
    public function sendToUser(int $userId, string $message): bool
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user || !$user->telegram_chat_id) {
            return false;
        }

        return $this->sendToChatId($user->telegram_chat_id, $message);
    }

    /**
     * Gửi thông báo đến chat_id cụ thể
     * 
     * @param string $chatId
     * @param string $message
     * @return bool
     */
    public function sendToChatId(string $chatId, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning('Telegram bot token chưa được cấu hình');
            return false;
        }

        return $this->sendToTelegram($this->token, $chatId, $message);
    }

    /**
     * Gửi thông báo đơn hàng cho buyer
     */
    public function sendOrderNotificationToBuyer($order): bool
    {
        if (!$order->buyer || !$order->buyer->hasTelegramConnected()) {
            return false;
        }

        $orderUrl = url("/orders/{$order->slug}");
        
        $message = "🛒 <b>Đơn hàng mới của bạn</b>\n\n";
        $message .= "📦 Mã đơn: <code>{$order->slug}</code>\n";
        $message .= "🏪 Người bán: {$order->seller->full_name}\n";
        $message .= "💰 Tổng tiền: <b>" . number_format($order->total_amount, 0, ',', '.') . "₫</b>\n";
        $message .= "📊 Số lượng sản phẩm: {$order->items->sum('quantity')}\n\n";
        $message .= "🔗 <a href=\"{$orderUrl}\">Xem chi tiết</a>";

        return $this->sendToUser($order->buyer->id, $message);
    }

    /**
     * Gửi thông báo đơn hàng dịch vụ cho buyer
     */
    public function sendServiceOrderNotificationToBuyer($serviceOrder): bool
    {
        if (!$serviceOrder->buyer || !$serviceOrder->buyer->hasTelegramConnected()) {
            return false;
        }

        $orderUrl = url("/orders/{$serviceOrder->slug}");
        
        $message = "🔧 <b>Đơn hàng dịch vụ mới của bạn</b>\n\n";
        $message .= "📦 Mã đơn: <code>{$serviceOrder->slug}</code>\n";
        $message .= "🏪 Người bán: {$serviceOrder->seller->full_name}\n";
        $message .= "🔧 Dịch vụ: {$serviceOrder->serviceVariant->service->name}\n";
        $message .= "📋 Biến thể: {$serviceOrder->serviceVariant->name}\n";
        $message .= "💰 Tổng tiền: <b>" . number_format($serviceOrder->total_amount, 0, ',', '.') . "₫</b>\n\n";
        $message .= "🔗 <a href=\"{$orderUrl}\">Xem chi tiết</a>";

        return $this->sendToUser($serviceOrder->buyer->id, $message);
    }
}

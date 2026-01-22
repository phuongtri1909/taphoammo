# Hướng dẫn cấu hình Telegram Webhook

## Cách 1: Dùng URL trực tiếp (Nhanh nhất)

1. Lấy Bot Token từ @BotFather trên Telegram
2. Thay thế `YOUR_BOT_TOKEN` và `YOUR_DOMAIN` trong URL sau:
   ```
   https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://yourdomain.com/telegram/webhook
   ```
3. Mở URL này trên trình duyệt
4. Nếu thành công, bạn sẽ thấy: `{"ok":true,"result":true,"description":"Webhook was set"}`

## Cách 2: Dùng cURL (Terminal)

```bash
curl -X POST "https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook"
```

## Cách 3: Dùng Postman hoặc HTTP Client

- **Method**: POST
- **URL**: `https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook`
- **Body** (form-data hoặc JSON):
  ```json
  {
    "url": "https://yourdomain.com/telegram/webhook"
  }
  ```

## Kiểm tra Webhook đã được set chưa

Truy cập URL sau để xem thông tin webhook hiện tại:
```
https://api.telegram.org/botYOUR_BOT_TOKEN/getWebhookInfo
```

## Xóa Webhook (nếu cần)

```
https://api.telegram.org/botYOUR_BOT_TOKEN/deleteWebhook
```

## Lưu ý quan trọng

1. **HTTPS bắt buộc**: Webhook URL phải dùng HTTPS (trừ localhost)
2. **SSL Certificate**: Server phải có SSL certificate hợp lệ
3. **Public URL**: URL phải có thể truy cập công khai từ internet
4. **Test local**: Nếu test trên localhost, dùng ngrok hoặc tunnel

## Test trên Localhost

Nếu bạn đang test trên localhost, dùng ngrok:

```bash
# Cài đặt ngrok (nếu chưa có)
# https://ngrok.com/

# Chạy ngrok
ngrok http 8000

# Sử dụng URL ngrok để set webhook
# Ví dụ: https://abc123.ngrok.io/telegram/webhook
```

## Ví dụ cụ thể

Giả sử:
- Bot Token: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`
- Domain: `https://taphoammo.local`

URL để set webhook:
```
https://api.telegram.org/bot123456789:ABCdefGHIjklMNOpqrsTUVwxyz/setWebhook?url=https://taphoammo.local/telegram/webhook
```

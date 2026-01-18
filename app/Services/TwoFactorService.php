<?php

namespace App\Services;

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQRCodeUrl(User $user, string $secret): string
    {
        $companyName = config('app.name');
        $companyEmail = $user->email;

        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secret
        );
    }

    public function generateQRCode(User $user, string $secret): string
    {
        $qrCodeUrl = $this->getQRCodeUrl($user, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($qrCodeUrl);
    }

    public function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(random_bytes(16)), 0, 10));
        }
        return $codes;
    }

    public function initializeSetup(User $user): array
    {
        $secret = $this->generateSecret();
        $qrCode = $this->generateQRCode($user, $secret);

        session(['two_factor_secret' => $secret]);

        return [
            'secret' => $secret,
            'qr_code' => $qrCode,
        ];
    }

    public function confirmSetup(User $user, string $code): array
    {
        $secret = session('two_factor_secret');

        if (!$secret) {
            throw new \Exception('Không tìm thấy phiên cài đặt 2FA. Vui lòng bắt đầu lại.');
        }

        if (!$this->verify($secret, $code)) {
            throw new \Exception('Mã xác thực không chính xác. Vui lòng thử lại.');
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ]);

        session()->forget('two_factor_secret');

        return [
            'message' => 'Bật bảo mật 2 lớp thành công!',
            'recovery_codes' => $recoveryCodes,
        ];
    }

    public function verifyLogin(User $user, string $code): bool
    {
        if (!$user->hasTwoFactorEnabled()) {
            return true;
        }

        $secret = decrypt($user->two_factor_secret);

        if ($this->isValidRecoveryCode($user, $code)) {
            return true;
        }

        return $this->verify($secret, $code);
    }

    protected function isValidRecoveryCode(User $user, string $code): bool
    {
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (!$recoveryCodes || !is_array($recoveryCodes)) {
            return false;
        }

        $code = strtoupper($code);
        $key = array_search($code, $recoveryCodes);

        if ($key !== false) {
            unset($recoveryCodes[$key]);
            $user->update([
                'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
            ]);
            return true;
        }

        return false;
    }

    public function disable(User $user, string $password): bool
    {
        if (!\Hash::check($password, $user->password)) {
            throw new \Exception('Mật khẩu không chính xác.');
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return true;
    }
}



<?php

namespace App\Traits;

trait CustomEncryptable
{
    public function getAttribute($key)
    {
        if (in_array($key, $this->encryptable ?? []) && !empty($this->attributes[$key])) {
            return $this->customDecrypt($this->attributes[$key]);
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable ?? []) && !empty($value)) {
            $value = $this->customEncrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    protected function customEncrypt($plainText)
    {
        $key = hash('sha256', env('APP_CUSTOM_SECRET_KEY'));
        $iv = substr(hash('sha256', 'custom-fixed-iv'), 0, 16);
        return base64_encode(openssl_encrypt($plainText, 'AES-256-CBC', $key, 0, $iv));
    }

    protected function customDecrypt($encryptedText)
    {
        $key = hash('sha256', env('APP_CUSTOM_SECRET_KEY'));
        $iv = substr(hash('sha256', 'custom-fixed-iv'), 0, 16);
        return openssl_decrypt(base64_decode($encryptedText), 'AES-256-CBC', $key, 0, $iv);
    }
}

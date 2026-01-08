<?php

namespace App\Providers;

use App\Models\SMTPSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class SMTPSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            $smtpSettings = SMTPSetting::first();

            if ($smtpSettings) {
                $config = [
                    'default' => 'smtp',
                    'mailers' => [
                        'smtp' => [
                            'transport' => 'smtp',
                            'host' => $smtpSettings->host,
                            'port' => $smtpSettings->port,
                            'encryption' => $smtpSettings->encryption,
                            'username' => $smtpSettings->username,
                            'password' => $smtpSettings->password,
                            'timeout' => null,
                            'local_domain' => null,
                        ],
                    ],
                    'from' => [
                        'address' => $smtpSettings->from_address,
                        'name' => $smtpSettings->from_name,
                    ],
                ];

                config(['mail' => array_merge(config('mail'), $config)]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to load SMTP settings from database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Client;

use Exception;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;
use App\Models\GoogleSetting;
use App\Enums\WalletStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Laravel\Socialite\Facades\Socialite;

class AuthGoogleController extends Controller
{

    public function redirectToGoogle()
    {
        $googleSettings = GoogleSetting::first();

        if (!$googleSettings) {
            return redirect()->route('sign-in')
                ->with('error', 'Đang nhập bằng Google hiện không khả dụng. Vui lòng thử lại sau.');
        }

        config([
            'services.google.client_id' => $googleSettings->google_client_id,
            'services.google.client_secret' => $googleSettings->google_client_secret,
            'services.google.redirect' => route($googleSettings->google_redirect)
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $googleSettings = GoogleSetting::first();

            if (!$googleSettings) {
                return redirect()->route('sign-in')
                    ->with('error', 'Đang nhập bằng Google hiện không khả dụng. Vui lòng thử lại sau.');
            }

            config([
                'services.google.client_id' => $googleSettings->google_client_id,
                'services.google.client_secret' => $googleSettings->google_client_secret,
                'services.google.redirect' => route($googleSettings->google_redirect)
            ]);

            $googleUser = Socialite::driver('google')->user();
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                $existingUser->active = true;
                $existingUser->save();
                
                Wallet::firstOrCreate(
                    ['user_id' => $existingUser->id],
                    ['balance' => 0, 'status' => WalletStatus::ACTIVE]
                );
                
                Auth::login($existingUser);

                return redirect()->route('home');
            } else {
                $user = new User();
                $user->full_name = explode('@', $googleUser->getEmail())[0];
                $user->email = $googleUser->getEmail();
                $user->password = bcrypt(Str::random(16)); 
                $user->active = true;
                
                if ($googleUser->getAvatar()) {
                    try {
                        $avatar = file_get_contents($googleUser->getAvatar());
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                        $avatarPaths = $this->processAndSaveAvatar($tempFile);
                        $user->avatar = $avatarPaths['original'];
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        Log::error('Error processing Google avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                
                Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0, 'status' => WalletStatus::ACTIVE]
                );
                
                Auth::login($user);

                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            Log::error('Google login error:', ['error' => $e->getMessage()]);
            return redirect()->route('sign-in')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.');
        }
    }

    private function processAndSaveAvatar($tempFile)
    {
        $now = now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}");

        $image = Image::make($tempFile);
        
        $image->encode('webp', 85);
        
        $fullPath = "avatars/{$yearMonth}/{$fileName}.webp";
        Storage::disk('public')->put($fullPath, $image->stream());

        return [
            'original' => $fullPath
        ];
    }
}

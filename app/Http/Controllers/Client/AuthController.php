<?php

namespace App\Http\Controllers\Client;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\ActivationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Mời bạn nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Mời bạn nhập mật khẩu.',
        ]);

        try {

            $oldSessionId = session()->getId();

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Email hoặc mật khẩu không chính xác.',
                ]);
            }

            if ($user->active == false) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Email hoặc mật khẩu không chính xác.',
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Email hoặc mật khẩu không chính xác.',
                ]);
            }


            Auth::login($user);


            $user->ip_address = $request->ip();
            $user->save();

            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại sau.');
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        try {
            $existingUser = User::where('email', $request->email)->first();
            
            if ($existingUser) {
                if ($existingUser->active) {
                    return redirect()->back()->withInput()->withErrors(['email' => 'Email này đã được sử dụng.']);
                }
                
                if ($existingUser->last_activation_email_sent_at) {
                    $lastSentTime = $existingUser->last_activation_email_sent_at;
                    $now = now();
                    
                    $elapsedSeconds = $now->timestamp - $lastSentTime->timestamp;
                    
                    if ($elapsedSeconds < 0) {
                        $elapsedSeconds = 0;
                    }
                    
                    $requiredSeconds = 3 * 60;
                    if ($elapsedSeconds < $requiredSeconds) {
                        $remainingSeconds = $requiredSeconds - $elapsedSeconds;
                        $remainingMinutes = (int)($remainingSeconds / 60);
                        $remainingSecs = $remainingSeconds % 60;
                        
                        if ($remainingMinutes > 0 && $remainingSecs > 0) {
                            $timeMessage = "{$remainingMinutes} phút {$remainingSecs} giây";
                        } elseif ($remainingMinutes > 0) {
                            $timeMessage = "{$remainingMinutes} phút";
                        } else {
                            $timeMessage = "{$remainingSecs} giây";
                        }
                        
                        return redirect()->back()->withInput()->withErrors(['email' => "Vui lòng đợi {$timeMessage} nữa trước khi yêu cầu gửi lại email kích hoạt."]);
                    }
                }
                
                $activationToken = Str::random(60);
                $existingUser->password = Hash::make($request->password);
                $existingUser->key_active = $activationToken;
                $existingUser->last_activation_email_sent_at = now();
                $existingUser->save();
                
                $activationUrl = route('verify-email', ['token' => $activationToken]);
                Mail::to($existingUser->email)->send(new ActivationMail($existingUser, $activationUrl));
                
                return redirect()->route('sign-in')->with('success', 'Email kích hoạt đã được gửi lại! Vui lòng kiểm tra email để kích hoạt tài khoản.');
            }
            
            $activationToken = Str::random(60);
            
            $fullName = explode('@', $request->email)[0];
            
            $user = User::create([
                'full_name' => $fullName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'active' => false,
                'key_active' => $activationToken,
                'last_activation_email_sent_at' => now(),
            ]);

            $activationUrl = route('verify-email', ['token' => $activationToken]);
            
            Mail::to($user->email)->send(new ActivationMail($user, $activationUrl));

            return redirect()->route('sign-in')->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại sau.');
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return redirect()->back()->withInput()->withErrors(['email' => 'Không tìm thấy email trong hệ thống.']);
            }

            if (!$user->active) {
                return redirect()->back()->withInput()->withErrors(['email' => 'Tài khoản chưa được kích hoạt. Vui lòng kích hoạt tài khoản trước khi đặt lại mật khẩu.']);
            }

            if ($user->last_reset_password_email_sent_at) {
                $lastSentTime = $user->last_reset_password_email_sent_at;
                $now = now();
                
                $elapsedSeconds = $now->timestamp - $lastSentTime->timestamp;
                
                if ($elapsedSeconds < 0) {
                    $elapsedSeconds = 0;
                }
                
                $requiredSeconds = 3 * 60;
                if ($elapsedSeconds < $requiredSeconds) {
                    $remainingSeconds = $requiredSeconds - $elapsedSeconds;
                    $remainingMinutes = (int)($remainingSeconds / 60);
                    $remainingSecs = $remainingSeconds % 60;
                    
                    if ($remainingMinutes > 0 && $remainingSecs > 0) {
                        $timeMessage = "{$remainingMinutes} phút {$remainingSecs} giây";
                    } elseif ($remainingMinutes > 0) {
                        $timeMessage = "{$remainingMinutes} phút";
                    } else {
                        $timeMessage = "{$remainingSecs} giây";
                    }
                    
                    return redirect()->back()->withInput()->withErrors(['email' => "Vui lòng đợi {$timeMessage} nữa trước khi yêu cầu gửi lại email đặt lại mật khẩu."]);
                }
            }

            $resetToken = Str::random(60);
            $user->key_reset_password = $resetToken;
            $user->reset_password_at = now();
            $user->last_reset_password_email_sent_at = now();
            $user->save();

            $resetUrl = route('reset-password', ['token' => $resetToken]);
            
            Mail::send('emails.reset-password', ['resetUrl' => $resetUrl], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Đặt lại mật khẩu - ' . config('app.name'));
            });

            return redirect()->back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    }

    public function verifyEmail($token)
    {
        try {
            $user = User::where('key_active', $token)->first();

            if (!$user) {
                return redirect()->route('sign-in')->with('error', 'Link kích hoạt không hợp lệ hoặc đã hết hạn.');
            }

            if ($user->active) {
                return redirect()->route('sign-in')->with('error', 'Tài khoản của bạn đã được kích hoạt trước đó.');
            }

            $user->active = true;
            $user->key_active = null;
            $user->save();

            return redirect()->route('sign-in')->with('success', 'Tài khoản đã được kích hoạt thành công! Vui lòng đăng nhập.');
        } catch (Exception $e) {
            return redirect()->route('sign-in')->with('error', 'Có lỗi xảy ra khi kích hoạt tài khoản.');
        }
    }

    public function showResetPasswordForm($token)
    {
        $user = User::where('key_reset_password', $token)->first();

        if (!$user) {
            return redirect()->route('forgot-password')->with('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
        }

        $expiryTime = $user->reset_password_at ? $user->reset_password_at->copy()->addMinutes(60) : null;
        if ($expiryTime && now()->isAfter($expiryTime)) {
            return redirect()->route('forgot-password')->with('error', 'Link đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại.');
        }

        return view('client.pages.auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        try {
            $user = User::where('email', $request->email)
                        ->where('key_reset_password', $request->token)
                        ->first();

            if (!$user) {
                return redirect()->back()->withInput()->withErrors(['email' => 'Link đặt lại mật khẩu không hợp lệ.']);
            }

            $expiryTime = $user->reset_password_at ? $user->reset_password_at->copy()->addMinutes(60) : null;
            if ($expiryTime && now()->isAfter($expiryTime)) {
                return redirect()->route('forgot-password')->with('error', 'Link đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại.');
            }

            $user->password = Hash::make($request->password);
            $user->key_reset_password = null;
            $user->reset_password_at = null;
            $user->save();

            return redirect()->route('sign-in')->with('success', 'Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi đặt lại mật khẩu.');
        }
    }

    public function logout(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

}

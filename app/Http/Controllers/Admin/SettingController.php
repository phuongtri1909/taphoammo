<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\OrderSetting;
use App\Models\SMTPSetting;
use App\Models\GoogleSetting;
use App\Models\PaypalSetting;

class SettingController extends Controller
{
    public function index()
    {
        $smtpSetting = SMTPSetting::first() ?? new SMTPSetting();

        return view('admin.pages.settings.index', compact(
            'smtpSetting',
        ));
    }

    public function updateSMTP(Request $request)
    {
        $request->validate([
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'nullable|string',
            'admin_email' => 'required|email',
        ]);

        $smtpSetting = SMTPSetting::first();
        if (!$smtpSetting) {
            $smtpSetting = new SMTPSetting();
        }

        $smtpSetting->fill($request->all());
        $smtpSetting->save();

        return redirect()->route('admin.setting.index', ['tab' => 'smtp'])
            ->with('success', 'Cài đặt SMTP đã được cập nhật thành công.');
    }


}

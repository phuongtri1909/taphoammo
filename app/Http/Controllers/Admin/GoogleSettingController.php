<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\GoogleSetting;

class GoogleSettingController extends Controller
{
    public function index()
    {
        $googleSetting = GoogleSetting::first() ?? new GoogleSetting();

        return view('admin.pages.google-setting.index', compact(
            'googleSetting',
        ));
    }

    public function updateGoogle(Request $request)
    {
        $request->validate([
            'google_client_id' => 'required|string',
            'google_client_secret' => 'required|string',
            'google_redirect' => 'required|string',
        ]);

        $googleSetting = GoogleSetting::first();
        if (!$googleSetting) {
            $googleSetting = new GoogleSetting();
        }

        $googleSetting->fill($request->all());
        $googleSetting->save();

        return redirect()->route('admin.setting.index', ['tab' => 'google'])
            ->with('success', 'Cài đặt Google đã được cập nhật thành công.');
    }


}

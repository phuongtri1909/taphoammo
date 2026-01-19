<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = Config::orderBy('key')->get();

        return view('admin.pages.configs.index', compact('configs'));
    }

    public function update(Request $request, Config $config)
    {
        $request->validate([
            'value' => 'nullable',
            'description' => 'nullable|string|max:500',
        ]);

        $config->update([
            'value' => $request->input('value'),
            'description' => $request->input('description'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cấu hình đã được cập nhật thành công.'
            ]);
        }

        return redirect()->route('admin.configs.index')
            ->with('success', 'Cấu hình đã được cập nhật thành công.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'configs' => 'required|array',
            'configs.*.value' => 'nullable',
            'configs.*.description' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('configs') as $id => $data) {
                Config::where('id', $id)->update([
                    'value' => $data['value'] ?? null,
                    'description' => $data['description'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.configs.index')
            ->with('success', 'Tất cả cấu hình đã được cập nhật thành công.');
    }
}

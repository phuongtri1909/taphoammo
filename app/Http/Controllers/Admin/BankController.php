<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::paginate(10);
        return view('admin.pages.banks.index', compact('banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
        ],[
            'name.required' => 'Tên ngân hàng là bắt buộc.',
            'name.string' => 'Tên ngân hàng phải là một chuỗi.',
            'name.max' => 'Tên ngân hàng không được vượt quá 255 ký tự.',
            'code.required' => 'Mã ngân hàng là bắt buộc.',
            'code.string' => 'Mã ngân hàng phải là một chuỗi.',
            'code.max' => 'Mã ngân hàng không được vượt quá 50 ký tự.',
            'account_number.required' => 'Số tài khoản là bắt buộc.',
            'account_number.string' => 'Số tài khoản phải là một chuỗi.',
            'account_number.max' => 'Số tài khoản không được vượt quá 50 ký tự.',
            'account_name.required' => 'Chủ tài khoản là bắt buộc.',
            'account_name.string' => 'Chủ tài khoản phải là một chuỗi.',
            'account_name.max' => 'Chủ tài khoản không được vượt quá 255 ký tự.',
        ]);

        $data = $request->only(['name', 'code', 'account_number', 'account_name']);

        $data['status'] = $request->has('status');

        Bank::create($data);

        return redirect()->route('admin.banks.index')
            ->with('success', 'Ngân hàng đã được tạo thành công.');
    }


    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
        ],[
            'name.required' => 'Tên ngân hàng là bắt buộc.',
            'name.string' => 'Tên ngân hàng phải là một chuỗi.',
            'name.max' => 'Tên ngân hàng không được vượt quá 255 ký tự.',
            'code.required' => 'Mã ngân hàng là bắt buộc.',
            'code.string' => 'Mã ngân hàng phải là một chuỗi.',
            'code.max' => 'Mã ngân hàng không được vượt quá 50 ký tự.',
            'account_number.required' => 'Số tài khoản là bắt buộc.',
            'account_number.string' => 'Số tài khoản phải là một chuỗi.',
            'account_number.max' => 'Số tài khoản không được vượt quá 50 ký tự.',
            'account_name.required' => 'Chủ tài khoản là bắt buộc.',
            'account_name.string' => 'Chủ tài khoản phải là một chuỗi.',
            'account_name.max' => 'Chủ tài khoản không được vượt quá 255 ký tự.',
        ]);

        $data = $request->only(['name', 'code', 'account_number', 'account_name']);

        $data['status'] = $request->has('status');

        $bank->update($data);

        return redirect()->route('admin.banks.index')
            ->with('success', 'Thông tin ngân hàng đã được cập nhật thành công.');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();

        return redirect()->route('admin.banks.index')
            ->with('success', 'Ngân hàng đã được xóa thành công.');
    }
}

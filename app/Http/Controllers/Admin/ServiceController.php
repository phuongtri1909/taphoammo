<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Enums\ServiceStatus;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Trang duyệt dịch vụ (pending services)
     */
    public function pending()
    {
        $services = Service::with(['serviceSubCategory.serviceCategory', 'seller', 'variants'])
            ->where('status', ServiceStatus::PENDING)
            ->latest()
            ->paginate(20);

        app()->instance('pending_services_count', $services->total());

        return view('admin.pages.services.pending', compact('services'));
    }

    /**
     * Xem chi tiết dịch vụ để duyệt
     */
    public function review(Service $service)
    {
        $service->load(['serviceSubCategory.serviceCategory', 'seller', 'variants']);

        return view('admin.pages.services.review', compact('service'));
    }

    /**
     * Duyệt dịch vụ
     */
    public function approve(Request $request, Service $service)
    {
        if ($service->status !== ServiceStatus::PENDING) {
            return redirect()->back()->with('error', 'Dịch vụ không ở trạng thái chờ duyệt!');
        }

        $service->update([
            'status' => ServiceStatus::APPROVED,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->route('admin.services.pending')
            ->with('success', 'Đã duyệt dịch vụ thành công!');
    }

    /**
     * Từ chối dịch vụ
     */
    public function reject(Request $request, Service $service)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        if ($service->status !== ServiceStatus::PENDING) {
            return redirect()->back()->with('error', 'Dịch vụ không ở trạng thái chờ duyệt!');
        }

        $service->update([
            'status' => ServiceStatus::REJECTED,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->route('admin.services.pending')
            ->with('success', 'Đã từ chối dịch vụ!');
    }

    /**
     * Quản lý tất cả dịch vụ
     */
    public function index(Request $request)
    {
        $query = Service::with(['serviceSubCategory.serviceCategory', 'seller', 'variants']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('serviceSubCategory', function ($q) use ($request) {
                $q->where('service_category_id', $request->category_id);
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->latest()->paginate(20);
        $categories = ServiceCategory::active()->ordered()->get();
        $statuses = ServiceStatus::cases();

        return view('admin.pages.services.index', compact('services', 'categories', 'statuses'));
    }

    /**
     * Xem chi tiết dịch vụ
     */
    public function show(Service $service)
    {
        $service->load(['serviceSubCategory.serviceCategory', 'seller', 'variants']);

        return view('admin.pages.services.show', compact('service'));
    }

    /**
     * Cấm dịch vụ
     */
    public function ban(Request $request, Service $service)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do cấm.',
        ]);

        if (!in_array($service->status, [ServiceStatus::APPROVED, ServiceStatus::HIDDEN])) {
            return redirect()->back()->with('error', 'Không thể cấm dịch vụ ở trạng thái này!');
        }

        $service->update([
            'status' => ServiceStatus::BANNED,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->back()->with('success', 'Đã cấm dịch vụ!');
    }

    /**
     * Bỏ cấm dịch vụ (chuyển về pending để duyệt lại)
     */
    public function unban(Service $service)
    {
        if ($service->status !== ServiceStatus::BANNED) {
            return redirect()->back()->with('error', 'Dịch vụ không ở trạng thái bị cấm!');
        }

        $service->update([
            'status' => ServiceStatus::PENDING,
            'admin_note' => null,
        ]);

        return redirect()->back()->with('success', 'Đã bỏ cấm dịch vụ, dịch vụ chuyển về trạng thái chờ duyệt!');
    }
}

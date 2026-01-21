@extends('admin.layouts.sidebar')

@section('title', 'Duyệt dịch vụ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Dịch vụ chờ duyệt</h2>
                <span class="badge bg-warning text-dark fs-6">{{ $services->total() }} dịch vụ</span>
            </div>
            <div class="card-content">
                @if ($services->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4>Không có dịch vụ chờ duyệt</h4>
                        <p>Tất cả dịch vụ đã được xử lý</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Dịch vụ</th>
                                    <th class="column-medium">Danh mục</th>
                                    <th class="column-small">Seller</th>
                                    <th class="column-small text-center">Biến thể</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($services as $key => $service)
                                    <tr class="text-center">
                                        <td>{{ $services->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.services.review', $service) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                @if ($service->image)
                                                    <img src="{{ Storage::url($service->image) }}"
                                                        alt="{{ $service->name }}" class="product-thumb">
                                                @else
                                                    <div class="product-thumb-placeholder">
                                                        <i class="fas fa-concierge-bell"></i>
                                                    </div>
                                                @endif
                                                <div class="product-details">
                                                    <span class="item-name">{{ $service->name }}</span>
                                                    <small class="text-muted d-block">{{ Str::limit($service->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $service->serviceSubCategory->serviceCategory->name ?? 'N/A' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $service->serviceSubCategory->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="seller-name">{{ $service->seller->full_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $service->variants->count() }}</span>
                                        </td>
                                        <td>
                                            {{ $service->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $services->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush

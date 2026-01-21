@extends('client.layouts.app')

@section('title', 'Quản lý bài viết - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn mb-4 lg:mb-5">
                <div class="p-4 md:p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">Quản lý nội dung chia sẻ</h1>
                            <p class="text-sm text-gray-500">Tạo và quản lý các bài viết chia sẻ của bạn</p>
                        </div>
                        <a href="{{ route('shares.manage.create') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                            <i class="fas fa-plus"></i>
                            Tạo bài viết mới
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 lg:gap-4 mb-4 lg:mb-5">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn p-4 text-center"
                    style="animation-delay: 0.1s">
                    <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg flex items-center justify-center mx-auto mb-2 shadow-md">
                        <i class="fas fa-file-alt text-white text-sm"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ $counts['all'] }}</div>
                    <div class="text-xs font-medium text-gray-500">Tổng bài viết</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn p-4 text-center"
                    style="animation-delay: 0.15s">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg flex items-center justify-center mx-auto mb-2 shadow-md">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <div class="text-2xl font-bold text-yellow-600 mb-1">{{ $counts['pending'] }}</div>
                    <div class="text-xs font-medium text-gray-500">Chờ duyệt</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn p-4 text-center"
                    style="animation-delay: 0.2s">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center mx-auto mb-2 shadow-md">
                        <i class="fas fa-check-circle text-white text-sm"></i>
                    </div>
                    <div class="text-2xl font-bold text-green-600 mb-1">{{ $counts['approved'] }}</div>
                    <div class="text-xs font-medium text-gray-500">Đã duyệt</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn p-4 text-center"
                    style="animation-delay: 0.25s">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center mx-auto mb-2 shadow-md">
                        <i class="fas fa-edit text-white text-sm"></i>
                    </div>
                    <div class="text-2xl font-bold text-blue-600 mb-1">{{ $counts['draft'] }}</div>
                    <div class="text-xs font-medium text-gray-500">Bản nháp</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn mb-4 lg:mb-5"
                style="animation-delay: 0.3s">
                <div class="p-4 md:p-5">
                    <form action="{{ route('shares.manage.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm"
                                    placeholder="Tìm kiếm bài viết...">
                            </div>
                        </div>
                        <select name="status"
                            class="px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                            <option value="">Tất cả trạng thái</option>
                            @foreach (\App\Enums\ShareStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-4 py-2.5 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:scale-[1.01]">
                            <i class="fas fa-filter mr-1.5"></i> Lọc
                        </button>
                    </form>
                </div>
            </div>

            @if ($shares->isEmpty())
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 animate-fadeIn"
                    style="animation-delay: 0.35s">
                    <div class="p-8 md:p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-newspaper text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Chưa có bài viết nào</h3>
                        <p class="text-sm text-gray-500 mb-6">Hãy tạo bài viết đầu tiên của bạn để chia sẻ kiến thức!</p>
                        <a href="{{ route('shares.manage.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                            <i class="fas fa-plus"></i>
                            Tạo bài viết
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($shares as $index => $share)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                            style="animation-delay: {{ 0.35 + ($index * 0.05) }}s">
                            <div class="p-4 md:p-5">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <!-- Image -->
                                    @if ($share->isApproved())
                                        <a href="{{ route('shares.show', $share->slug) }}" target="_blank"
                                            class="w-full md:w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 hover:opacity-90 transition-opacity">
                                            @if ($share->image)
                                                <img src="{{ $share->image_url }}" alt="{{ $share->title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400 text-3xl"></i>
                                                </div>
                                            @endif
                                        </a>
                                    @else
                                        <div class="w-full md:w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                                            @if ($share->image)
                                                <img src="{{ $share->image_url }}" alt="{{ $share->title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400 text-3xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            @if ($share->isApproved())
                                                <a href="{{ route('shares.show', $share->slug) }}" target="_blank"
                                                    class="text-base font-bold text-gray-900 line-clamp-2 hover:text-primary transition-colors">
                                                    {{ $share->title }}
                                                </a>
                                            @else
                                                <h3 class="text-base font-bold text-gray-900 line-clamp-2">{{ $share->title }}</h3>
                                            @endif
                                            <span class="flex-shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full 
                                                @if($share->status->value === 'approved') bg-green-100 text-green-700
                                                @elseif($share->status->value === 'pending') bg-yellow-100 text-yellow-700
                                                @elseif($share->status->value === 'rejected') bg-red-100 text-red-700
                                                @elseif($share->status->value === 'hidden') bg-gray-100 text-gray-700
                                                @else bg-blue-100 text-blue-700
                                                @endif">
                                                {{ $share->status->label() }}
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2.5 mb-3 text-xs text-gray-500">
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-5 h-5 bg-primary/10 rounded flex items-center justify-center">
                                                    <i class="fas fa-folder-open text-primary text-[10px]"></i>
                                                </div>
                                                <span>{{ $share->category->name ?? 'Không có danh mục' }}</span>
                                            </div>
                                            <span>•</span>
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-5 h-5 bg-blue-100 rounded flex items-center justify-center">
                                                    <i class="fas fa-eye text-blue-600 text-[10px]"></i>
                                                </div>
                                                <span>{{ number_format($share->views) }} lượt xem</span>
                                            </div>
                                            <span>•</span>
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-5 h-5 bg-purple-100 rounded flex items-center justify-center">
                                                    <i class="fas fa-clock text-purple-600 text-[10px]"></i>
                                                </div>
                                                <span>{{ $share->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $share->getExcerptOrContent(120) }}</p>

                                        @if ($share->isRejected() && $share->rejection_reason)
                                            <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                                <div class="flex items-start gap-2">
                                                    <i class="fas fa-exclamation-circle text-red-500 text-sm mt-0.5"></i>
                                                    <div>
                                                        <p class="text-xs font-semibold text-red-700 mb-1">Lý do từ chối:</p>
                                                        <p class="text-xs text-red-600">{{ $share->rejection_reason }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Actions -->
                                        <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                                            @if ($share->isApproved())
                                                <a href="{{ route('shares.show', $share->slug) }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all duration-200">
                                                    <i class="fas fa-external-link-alt"></i> Xem
                                                </a>
                                                <button type="button" onclick="toggleVisibility('{{ $share->slug }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200">
                                                    <i class="fas fa-eye-slash"></i> Ẩn
                                                </button>
                                            @elseif ($share->isHidden())
                                                <button type="button" onclick="toggleVisibility('{{ $share->slug }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-all duration-200">
                                                    <i class="fas fa-eye"></i> Hiện lại
                                                </button>
                                                <a href="{{ route('shares.manage.edit', $share) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-all duration-200">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                            @elseif ($share->canEdit())
                                                <a href="{{ route('shares.manage.edit', $share) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-all duration-200">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                            @endif

                                            @if (!$share->isPending())
                                                <form action="{{ route('shares.manage.destroy', $share) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-all duration-200">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $shares->appends(request()->query())->links('components.paginate') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleVisibility(shareSlug) {
            Swal.fire({
                title: 'Xác nhận',
                text: 'Bạn có chắc muốn thay đổi trạng thái hiển thị của bài viết này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#02B3B9',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/shares/manage/${shareSlug}/toggle-visibility`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Thành công!', data.message, 'success')
                                    .then(() => window.location.reload());
                            } else {
                                Swal.fire('Lỗi', data.message, 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('Lỗi', 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                        });
                }
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }
    </style>
@endpush

@extends('client.layouts.app')

@section('title', 'Chia sẻ kinh nghiệm - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn mb-4 lg:mb-5">
                <div class="p-4 md:p-5 text-center">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">Chia sẻ kinh nghiệm</h1>
                    <p class="text-sm text-gray-500">Khám phá kiến thức và kinh nghiệm từ cộng đồng</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-4 lg:gap-5">
                <div class="flex-1 space-y-4">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                        style="animation-delay: 0.1s">
                        <div class="p-4 md:p-5">
                            <form action="{{ route('shares.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                                <div class="flex-1 relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm"
                                        placeholder="Tìm kiếm bài viết...">
                                    @if(request('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                </div>
                                <button type="submit"
                                    class="px-4 py-2.5 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01]">
                                    <i class="fas fa-search mr-1.5"></i> Tìm kiếm
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(request('search') || request('category') || request('author'))
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-4 animate-fadeIn"
                            style="animation-delay: 0.15s">
                            <div class="flex flex-wrap items-center gap-2 text-sm">
                                <span class="text-gray-600 font-medium">Đang lọc:</span>
                                @if(request('search'))
                                    <span class="px-3 py-1 bg-gray-100 rounded-lg flex items-center gap-2">
                                        <span>"{{ request('search') }}"</span>
                                        <a href="{{ route('shares.index', array_filter(['category' => request('category'), 'author' => request('author')])) }}" 
                                            class="text-red-500 hover:text-red-600">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if(request('category'))
                                    @php $currentCat = $categories->where('slug', request('category'))->first(); @endphp
                                    @if($currentCat)
                                        <span class="px-3 py-1 bg-primary/10 text-primary rounded-lg flex items-center gap-2 font-medium">
                                            <span>{{ $currentCat->name }}</span>
                                            <a href="{{ route('shares.index', array_filter(['search' => request('search'), 'author' => request('author')])) }}" 
                                                class="text-red-500 hover:text-red-600">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                @endif
                                @if(request('author'))
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg flex items-center gap-2 font-medium">
                                        <i class="fas fa-user text-xs"></i>
                                        <span>Tác giả: {{ request('author') }}</span>
                                        <a href="{{ route('shares.index', array_filter(['search' => request('search'), 'category' => request('category')])) }}" 
                                            class="text-red-500 hover:text-red-600">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                <a href="{{ route('shares.index') }}" 
                                    class="ml-auto px-3 py-1 text-primary hover:text-primary-6 font-medium text-sm">
                                    <i class="fas fa-redo mr-1"></i> Xóa tất cả
                                </a>
                            </div>
                        </div>
                    @endif

                    @if ($shares->isEmpty())
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 animate-fadeIn"
                            style="animation-delay: 0.2s">
                            <div class="p-8 md:p-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-newspaper text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Chưa có bài viết nào</h3>
                                <p class="text-sm text-gray-500">Hãy quay lại sau để xem các bài viết mới nhất!</p>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                            @foreach ($shares as $index => $share)
                                <article class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn group"
                                    style="animation-delay: {{ 0.2 + ($index * 0.05) }}s">
                                    <a href="{{ route('shares.show', $share->slug) }}" class="block">
                                        <div class="aspect-video h-40 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                                            @if ($share->image)
                                                <img src="{{ $share->image_url }}" alt="{{ $share->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-primary/20 to-primary/40 flex items-center justify-center">
                                                    <i class="fas fa-newspaper text-2xl text-primary/50"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-3">
                                            <div class="flex items-center gap-1.5 mb-2">
                                                <a href="{{ route('shares.index', ['category' => $share->category->slug ?? '']) }}"
                                                    class="px-2 py-0.5 text-[10px] font-semibold bg-primary/10 text-primary rounded hover:bg-primary/20 transition-colors">
                                                    {{ $share->category->name ?? 'Không phân loại' }}
                                                </a>
                                                <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                                    <i class="fas fa-calendar text-[9px]"></i>
                                                    {{ $share->approved_at?->format('d/m/Y') }}
                                                </span>
                                            </div>
                                            <h3 class="font-semibold text-gray-900 mb-1.5 line-clamp-2 group-hover:text-primary transition-colors text-sm leading-tight">
                                                {{ $share->title }}
                                            </h3>
                                            <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ $share->getExcerptOrContent(80) }}</p>
                                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                                <div class="flex items-center gap-1.5">
                                                    <div class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-user text-[9px] text-gray-500"></i>
                                                    </div>
                                                    <span class="text-[10px] font-medium text-gray-600">{{ $share->author->full_name ?? 'Ẩn danh' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1 text-[10px] text-gray-400">
                                                    <i class="fas fa-eye"></i>
                                                    <span>{{ number_format($share->views) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $shares->withQueryString()->links('components.paginate') }}
                        </div>
                    @endif
                </div>

                <div class="lg:w-80 space-y-4 lg:space-y-5">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                        style="animation-delay: 0.3s">
                        <div class="p-4 md:p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-folder-open text-primary text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-900">Danh mục</h3>
                            </div>
                            <ul class="space-y-1.5">
                                <li>
                                    <a href="{{ route('shares.index') }}"
                                        class="flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 {{ !request('category') ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-50' }}">
                                        <span class="text-sm">Tất cả</span>
                                        <span class="text-xs font-medium bg-primary/20 text-primary px-2 py-0.5 rounded-full">{{ $shares->total() }}</span>
                                    </a>
                                </li>
                                @foreach ($categories as $category)
                                    <li>
                                        <a href="{{ route('shares.index', ['category' => $category->slug]) }}"
                                            class="flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 {{ request('category') === $category->slug ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-50' }}">
                                            <span class="text-sm">{{ $category->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $category->approved_shares_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if ($featuredShares->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                            style="animation-delay: 0.35s">
                            <div class="p-4 md:p-5">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-500 rounded-lg flex items-center justify-center shadow-md">
                                        <i class="fas fa-fire text-white text-sm"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900">Xem nhiều nhất</h3>
                                </div>
                                <ul class="space-y-2.5">
                                    @foreach ($featuredShares as $index => $featured)
                                        <li>
                                            <a href="{{ route('shares.show', $featured->slug) }}"
                                                class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 group">
                                                <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors mb-1">
                                                        {{ $featured->title }}
                                                    </h4>
                                                    <div class="flex items-center gap-2 text-xs text-gray-400">
                                                        <span><i class="fas fa-eye mr-1"></i> {{ number_format($featured->views) }}</span>
                                                        <span>•</span>
                                                        <span>{{ $featured->approved_at?->format('d/m/Y') }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

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

@extends('client.layouts.app')

@section('title', $share->title . ' - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <nav class="mb-4 lg:mb-5">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-3 md:p-4 animate-fadeIn">
                    <ol class="flex items-center gap-2 text-sm">
                        <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-primary transition-colors">Trang chủ</a></li>
                        <li class="text-gray-400">/</li>
                        <li><a href="{{ route('shares.index') }}" class="text-gray-500 hover:text-primary transition-colors">Chia sẻ</a></li>
                        <li class="text-gray-400">/</li>
                        <li class="text-gray-900 truncate max-w-[200px]">{{ $share->title }}</li>
                    </ol>
                </div>
            </nav>

            <div class="flex flex-col lg:flex-row gap-4 lg:gap-5">
                <div class="flex-1">
                    <article class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                        style="animation-delay: 0.1s">
                        @if ($share->image)
                            <div class="aspect-video overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                                <img src="{{ $share->image_url }}" alt="{{ $share->title }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="p-4 md:p-6 lg:p-8">
                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                <a href="{{ route('shares.index', ['category' => $share->category->slug ?? '']) }}"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-all duration-200">
                                    <i class="fas fa-folder-open"></i>
                                    {{ $share->category->name ?? 'Không phân loại' }}
                                </a>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 bg-purple-100 rounded flex items-center justify-center">
                                            <i class="fas fa-calendar text-purple-600 text-[10px]"></i>
                                        </div>
                                        <span>{{ $share->approved_at?->format('d/m/Y') }}</span>
                                    </div>
                                    <span>•</span>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 bg-blue-100 rounded flex items-center justify-center">
                                            <i class="fas fa-eye text-blue-600 text-[10px]"></i>
                                        </div>
                                        <span>{{ number_format($share->views) }} lượt xem</span>
                                    </div>
                                </div>
                            </div>

                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-5">{{ $share->title }}</h1>

                            <div class="flex items-center gap-3 pb-5 mb-5 border-b border-gray-100">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary/20 to-primary/40 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $share->author->full_name ?? 'Ẩn danh' }}</p>
                                    <p class="text-xs text-gray-500">Tác giả</p>
                                </div>
                            </div>

                            @if ($share->excerpt)
                                <div class="mb-5 p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-l-4 border-primary rounded-lg">
                                    <p class="text-base text-gray-700 italic leading-relaxed">{{ $share->excerpt }}</p>
                                </div>
                            @endif

                            <div class="prose prose-lg max-w-none py-4 share-content">
                                {!! $share->content !!}
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-200">
                                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <i class="fas fa-share-alt text-primary"></i> Chia sẻ bài viết:
                                </p>
                                <div class="flex items-center gap-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                        target="_blank"
                                        class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($share->title) }}"
                                        target="_blank"
                                        class="w-10 h-10 flex items-center justify-center bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($share->title) }}"
                                        target="_blank"
                                        class="w-10 h-10 flex items-center justify-center bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                        <i class="fab fa-telegram-plane"></i>
                                    </a>
                                    <button type="button" onclick="copyToClipboard('{{ request()->url() }}')"
                                        class="w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>

                    @if ($relatedShares->isNotEmpty())
                        <div class="mt-5 animate-fadeIn"
                            style="animation-delay: 0.2s">
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-4 md:p-5">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-link text-primary text-sm"></i>
                                    </div>
                                    <h2 class="text-lg font-bold text-gray-900">Bài viết liên quan</h2>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($relatedShares as $related)
                                        <a href="{{ route('shares.show', $related->slug) }}"
                                            class="flex gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200 group">
                                            <div class="w-24 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300">
                                                @if ($related->image)
                                                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-newspaper text-xl text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="font-semibold text-sm text-gray-900 line-clamp-2 group-hover:text-primary transition-colors mb-1.5">
                                                    {{ $related->title }}
                                                </h3>
                                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                                    <span><i class="fas fa-eye mr-1"></i> {{ number_format($related->views) }}</span>
                                                    <span>•</span>
                                                    <span>{{ $related->approved_at?->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-5 text-center animate-fadeIn"
                        style="animation-delay: 0.25s">
                        <a href="{{ route('shares.index') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:scale-[1.01]">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại danh sách
                        </a>
                    </div>
                </div>

                <div class="lg:w-80 space-y-4 lg:space-y-5">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                        style="animation-delay: 0.15s">
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
                                        class="flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50">
                                        <span class="text-sm">Tất cả</span>
                                        <span class="text-xs text-gray-400">{{ $categories->sum('approved_shares_count') }}</span>
                                    </a>
                                </li>
                                @foreach ($categories as $category)
                                    <li>
                                        <a href="{{ route('shares.index', ['category' => $category->slug]) }}"
                                            class="flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 {{ request('category', $share->category->slug ?? '') === $category->slug ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-50' }}">
                                            <span class="text-sm">{{ $category->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $category->approved_shares_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                        style="animation-delay: 0.2s">
                        <div class="p-4 md:p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-900">Tác giả</h3>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-primary/40 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-user text-primary text-xl"></i>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $share->author->full_name ?? 'Ẩn danh' }}</h4>
                                <p class="text-xs text-gray-500 mb-4">Người chia sẻ</p>
                                @if($share->author)
                                    <a href="{{ route('shares.index', ['author' => $share->author->full_name]) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-all duration-200">
                                        <i class="fas fa-list"></i> Xem bài viết khác
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyToClipboard(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã sao chép!',
                        text: 'Liên kết đã được sao chép vào clipboard.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }).catch(function(err) {
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                
                if (successful) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã sao chép!',
                        text: 'Liên kết đã được sao chép vào clipboard.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Không thể sao chép liên kết. Vui lòng sao chép thủ công.',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } catch (err) {
                document.body.removeChild(textArea);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể sao chép liên kết. Vui lòng sao chép thủ công.',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
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

        .share-content {
            color: #374151;
            line-height: 1.8;
        }

        .share-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1.5rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .share-content .image-left {
            float: left;
            margin-right: 1rem;
            margin-bottom: 0.5rem;
        }

        .share-content .image-right {
            float: right;
            margin-left: 1rem;
            margin-bottom: 0.5rem;
        }

        .share-content .image-center {
            display: block;
            margin: 1.5rem auto;
        }

        .share-content p {
            margin-bottom: 1.25rem;
            font-size: 15px;
        }

        .share-content h2, .share-content h3, .share-content h4 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .share-content h2 {
            font-size: 1.5rem;
        }

        .share-content h3 {
            font-size: 1.25rem;
        }

        .share-content ul, .share-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1.25rem;
            padding-left: 0.5rem;
        }

        .share-content li {
            margin-bottom: 0.5rem;
        }

        .share-content blockquote {
            border-left: 4px solid #02B3B9;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: #4b5563;
            background: #f9fafb;
            border-radius: 0 8px 8px 0;
        }

        .share-content a {
            color: #02B3B9;
            text-decoration: underline;
        }

        .share-content a:hover {
            color: #002740;
        }

        .share-content code {
            background: #f3f4f6;
            padding: 0.125rem 0.375rem;
            border-radius: 4px;
            font-size: 0.875em;
        }

        .share-content pre {
            background: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1.5rem 0;
        }

        .share-content pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }
    </style>
@endpush

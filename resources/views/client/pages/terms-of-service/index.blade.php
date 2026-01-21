@extends('client.layouts.app')

@section('title', 'Điều khoản sử dụng - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-4 lg:space-y-5">
                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center gap-3 mb-2.5">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-primary"></i>
                            </div>
                            <div>
                                <h1 class="text-xl md:text-2xl font-bold text-gray-900">{{ $terms->title }}</h1>
                                <p class="text-sm text-gray-500 mt-0.5">Cập nhật lần cuối: {{ $terms->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn"
                    style="animation-delay: 0.1s">
                    <div class="p-4 md:p-8">
                        <div class="prose prose-lg max-w-none">
                            {!! nl2br(e($terms->content)) !!}
                        </div>
                    </div>
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
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }

        .prose p {
            margin-bottom: 1rem;
            line-height: 1.75;
        }

        .prose h2, .prose h3 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .prose ul, .prose ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose li {
            margin-bottom: 0.5rem;
        }
    </style>
@endpush

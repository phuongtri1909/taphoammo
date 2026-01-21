@extends('client.layouts.app')

@section('title', 'Câu hỏi thường gặp (FAQ) - ' . config('app.name'))

@section('content')
<div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
    <div class="w-full max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Câu hỏi thường gặp</h1>
            <p class="text-sm md:text-base text-gray-600">Tìm câu trả lời cho những câu hỏi phổ biến nhất</p>
        </div>

        @if($faqs->isEmpty())
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 text-center">
                <div class="mb-4">
                    <i class="fas fa-question-circle text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Chưa có câu hỏi nào</h3>
                <p class="text-sm text-gray-600">FAQ sẽ được cập nhật sớm nhất có thể.</p>
            </div>
        @else
            <!-- FAQ Accordion -->
            <div class="space-y-4">
                @foreach($faqs as $index => $faq)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl">
                        <button 
                            type="button"
                            class="faq-question-btn w-full px-4 md:px-6 py-4 md:py-5 flex items-center justify-between text-left group focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset"
                            data-faq-index="{{ $index }}"
                            aria-expanded="false"
                            aria-controls="faq-answer-{{ $index }}">
                            <span class="flex-1 pr-4">
                                <span class="text-base md:text-lg font-semibold text-gray-900 group-hover:text-primary transition-colors">
                                    {{ $faq->question }}
                                </span>
                            </span>
                            <span class="flex-shrink-0">
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-primary transition-all duration-300 transform faq-icon" id="faq-icon-{{ $index }}"></i>
                            </span>
                        </button>
                        <div 
                            id="faq-answer-{{ $index }}"
                            class="faq-answer hidden px-4 md:px-6 pb-4 md:pb-5"
                            role="region">
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-sm md:text-base text-gray-700 leading-relaxed whitespace-pre-line">
                                    {{ $faq->answer }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-8 bg-gradient-to-r from-primary/10 to-primary-6/10 rounded-xl border border-primary/20 p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Không tìm thấy câu trả lời?</h3>
            <p class="text-sm text-gray-600 mb-4">Hãy liên hệ với chúng tôi, chúng tôi sẽ hỗ trợ bạn ngay!</p>
            <a href="{{ route('contact.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-6 transition-colors text-sm font-medium">
                <i class="fas fa-envelope"></i>
                Liên hệ hỗ trợ
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .faq-answer {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .faq-question-btn[aria-expanded="true"] #faq-icon-{{ $index }} {
        transform: rotate(180deg);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqButtons = document.querySelectorAll('.faq-question-btn');
    
    faqButtons.forEach(button => {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-faq-index');
            const answer = document.getElementById('faq-answer-' + index);
            const icon = document.getElementById('faq-icon-' + index);
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            faqButtons.forEach(otherButton => {
                if (otherButton !== button) {
                    const otherIndex = otherButton.getAttribute('data-faq-index');
                    const otherAnswer = document.getElementById('faq-answer-' + otherIndex);
                    const otherIcon = document.getElementById('faq-icon-' + otherIndex);
                    
                    otherButton.setAttribute('aria-expanded', 'false');
                    otherAnswer.classList.add('hidden');
                    otherIcon.style.transform = 'rotate(0deg)';
                }
            });
            
            if (isExpanded) {
                this.setAttribute('aria-expanded', 'false');
                answer.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            } else {
                this.setAttribute('aria-expanded', 'true');
                answer.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });
});
</script>
@endpush
@endsection

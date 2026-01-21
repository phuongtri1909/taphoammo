@extends('client.layouts.app')

@section('title', 'Liên hệ hỗ trợ - ' . config('app.name'))

@section('content')
<div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
    <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn">
            <div class="p-4 md:p-6 lg:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Liên hệ hỗ trợ</h2>
                        
                        @if($contactContent && $contactContent->description)
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                {{ $contactContent->description }}
                            </p>
                        @endif

                        <div class="space-y-2">
                            @forelse($contactLinks as $link)
                                <a href="{{ $link->url }}" {{ str_starts_with($link->url, 'http') || str_starts_with($link->url, 'mailto:') ? 'target="_blank"' : '' }}
                                    class="group flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                    @if($link->icon)
                                        <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                            <i class="{{ $link->icon }} text-green-600 text-sm"></i>
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">
                                        {{ $link->name }}
                                    </span>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Chưa có thông tin liên hệ</p>
                            @endforelse
                        </div>

                        @if($socials->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Theo dõi chúng tôi trên mạng xã hội</h3>
                                <div class="flex items-center gap-3 flex-wrap">
                                    @foreach($socials as $social)
                                        @php
                                            $colorClass = 'text-primary';
                                            $bgColorClass = 'bg-primary/20';
                                            
                                            if (str_contains(strtolower($social->name), 'instagram') || str_contains(strtolower($social->icon), 'instagram')) {
                                                $colorClass = 'text-pink-500';
                                                $bgColorClass = 'bg-pink-500/20';
                                            } elseif (str_contains(strtolower($social->name), 'twitter') || str_contains(strtolower($social->icon), 'twitter')) {
                                                $colorClass = 'text-blue-400';
                                                $bgColorClass = 'bg-blue-400/20';
                                            } elseif (str_contains(strtolower($social->name), 'youtube') || str_contains(strtolower($social->icon), 'youtube')) {
                                                $colorClass = 'text-red-600';
                                                $bgColorClass = 'bg-red-600/20';
                                            } elseif (str_contains(strtolower($social->name), 'tiktok') || str_contains(strtolower($social->icon), 'tiktok')) {
                                                $colorClass = 'text-black';
                                                $bgColorClass = 'bg-black/20';
                                            }
                                        @endphp
                                        <a href="{{ $social->url }}" target="_blank" 
                                           class="group relative w-12 h-12 flex items-center justify-center" 
                                           aria-label="{{ $social->name }}">
                                            <div class="absolute inset-0 {{ $bgColorClass }} rounded-full scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                                            @if (strpos($social->icon, 'custom-') === 0)
                                                <span class="{{ $social->icon }} {{ $colorClass }} text-2xl relative z-10 block group-hover:scale-110 transition-transform"></span>
                                            @else
                                                <i class="{{ $social->icon }} {{ $colorClass }} text-2xl relative z-10 group-hover:scale-110 transition-transform"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Tin nhắn</h2>
                        
                        <form id="contactForm" class="space-y-4">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" required
                                        value="{{ Auth::check() ? Auth::user()->email : '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                        placeholder="your@email.com">
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Số điện thoại
                                    </label>
                                    <input type="tel" id="phone" name="phone"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                        placeholder="0123456789">
                                </div>
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Chủ đề <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="subject" name="subject" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                    placeholder="Nhập chủ đề">
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Nội dung <span class="text-red-500">*</span>
                                </label>
                                <textarea id="message" name="message" rows="6" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
                                    placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" id="submitBtn"
                                    class="px-6 py-2.5 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>Gửi tin nhắn</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        
        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route("contact.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                form.reset();
            } else {
                showToast(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            }
        } catch (error) {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endpush
@endsection

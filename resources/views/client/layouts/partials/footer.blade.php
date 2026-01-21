@php
    $contactContent = \App\Models\FooterContent::where('section', 'contact')->first();
    $informationContent = \App\Models\FooterContent::where('section', 'information')->first();
    $sellerRegContent = \App\Models\FooterContent::where('section', 'seller_registration')->first();
    $contactLinks = \App\Models\ContactLink::active()->orderBy('order')->get();
@endphp
<footer
    class="relative space-y-6 border-t border-gray-200 px-4 pb-6 pt-12 lg:px-32 bg-gradient-to-b from-white to-gray-50">
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-12 lg:grid-cols-3">
        <!-- Liên hệ -->
        <ul class="list-none space-y-3">
            <h2 class="pb-3 text-xl font-bold text-gray-900">{{ $contactContent->title ?? 'Liên hệ' }}</h2>
            @if($contactContent && $contactContent->description)
                <li class="text-sm ml-0 font-normal text-gray-600 leading-relaxed">
                    {{ $contactContent->description }}
                </li>
            @endif
            @forelse($contactLinks as $link)
                <li class="ml-0">
                    <a href="{{ $link->url }}" {{ str_starts_with($link->url, 'http') ? 'target="_blank"' : '' }}
                        class="group text-sm ml-0 flex cursor-pointer items-center gap-x-3 font-medium text-gray-700 hover:text-primary transition-all duration-300">
                        @if($link->icon)
                            <i class="{{ $link->icon }} h-5 w-5 text-primary group-hover:scale-110 transition-transform"></i>
                        @endif
                        <span class="group-hover:translate-x-1 transition-transform">{{ $link->name }}</span>
                    </a>
                </li>
            @empty
                <!-- Fallback nếu không có contact links -->
                <li class="ml-0">
                    <a href="#"
                        class="group text-sm ml-0 flex cursor-pointer items-center gap-x-3 font-medium text-gray-700 hover:text-primary transition-all duration-300">
                        <i class="fas fa-comment-dots h-5 w-5 text-primary group-hover:scale-110 transition-transform"></i>
                        <span class="group-hover:translate-x-1 transition-transform">Chat với hỗ trợ viên</span>
                    </a>
                </li>
            @endforelse
        </ul>

        <!-- Thông tin -->
        <ul class="list-none space-y-3">
            <h2 class="pb-3 text-xl font-bold text-gray-900">{{ $informationContent->title ?? 'Thông tin' }}</h2>
            @if($informationContent && $informationContent->description)
                <li class="text-sm ml-0 font-normal text-gray-600 leading-relaxed">
                    {{ $informationContent->description }}
                </li>
            @endif

            <li>
                <a href="{{ route('faqs.index') }}"
                    class="group text-sm ml-0 flex cursor-pointer items-center gap-x-3 font-medium text-gray-700 hover:text-primary transition-all duration-300">
                    <span class="group-hover:translate-x-1 transition-transform">Câu hỏi thường gặp</span>
                </a>
            </li>
            <li>
                <a href="{{ route('terms-of-service.index') }}"
                    class="group text-sm ml-0 flex cursor-pointer items-center gap-x-3 font-medium text-gray-700 hover:text-primary transition-all duration-300">
                    <span class="group-hover:translate-x-1 transition-transform">Điều khoản sử dụng</span>
                </a>
            </li>
        </ul>

        <!-- Đăng ký bán hàng -->
        <ul class="list-none space-y-3">
            <h2 class="pb-3 text-xl font-bold text-gray-900">{{ $sellerRegContent->title ?? 'Đăng ký bán hàng' }}</h2>
            @if($sellerRegContent && $sellerRegContent->description)
                <li class="text-sm ml-0 font-normal text-gray-600 leading-relaxed">
                    {{ $sellerRegContent->description }}
                </li>
            @endif
            <li class="ml-0 pt-2">
                @auth
                    @if(auth()->user()->canRegisterAsSeller())
                        <a href="{{ route('seller.register') }}"
                            class="group relative inline-block rounded-lg bg-gradient-to-r from-primary to-primary-6 px-6 py-3 text-sm font-semibold text-white shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2">
                                Tham gia
                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </a>
                    @elseif(auth()->user()->isSeller())
                        <span class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-green-600 bg-green-50 rounded-lg">
                            <i class="fas fa-check-circle"></i>
                            Bạn đã là người bán
                        </span>
                    @elseif(auth()->user()->isAdmin())
                        <span class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-500 bg-gray-100 rounded-lg">
                            <i class="fas fa-info-circle"></i>
                            Admin không thể đăng ký bán hàng
                        </span>
                    @endif
                @else
                    <a href="{{ route('sign-in') }}"
                        class="group relative inline-block rounded-lg bg-gradient-to-r from-primary to-primary-6 px-6 py-3 text-sm font-semibold text-white shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2">
                            Tham gia
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </a>
                @endauth
            </li>
            <li class="text-sm ml-0 font-medium text-gray-700 pt-2">
                Theo dõi chúng tôi trên mạng xã hội
            </li>
            <li class="ml-0 flex items-center gap-x-4 pt-2">
                @forelse($socials as $social)
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
                       class="group relative w-14 h-14 flex items-center justify-center" 
                       aria-label="{{ $social->name }}">
                        <div class="absolute inset-0 {{ $bgColorClass }} rounded-full scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                        @if (strpos($social->icon, 'custom-') === 0)
                            <span class="{{ $social->icon }} {{ $colorClass }} text-3xl relative z-10 block group-hover:scale-110 transition-transform"></span>
                        @else
                            <i class="{{ $social->icon }} {{ $colorClass }} text-3xl relative z-10 group-hover:scale-110 transition-transform"></i>
                        @endif
                    </a>
                @empty
                    <a href="https://m.facebook.com/shoptaphoazalo?mibextid=LQQJ4d" target="_blank" class="group relative w-14 h-14 flex items-center justify-center">
                        <div class="absolute inset-0 bg-primary/20 rounded-full scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                        <i class="fab fa-facebook text-3xl text-primary relative z-10 group-hover:scale-110 transition-transform"></i>
                    </a>
                    <a href="https://zalo.me/0565392901" target="_blank" class="group relative w-14 h-14 flex items-center justify-center">
                        <div class="absolute inset-0 bg-primary/20 rounded-full scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                        <span class="custom-zalo text-3xl text-primary relative z-10 block group-hover:scale-110 transition-transform"></span>
                    </a>
                @endforelse
            </li>
        </ul>
    </div>

    <hr class="border-gray-200 my-8">
    <div class="flex flex-col items-center justify-between gap-4 pb-4 text-sm font-semibold text-blue-700 md:flex-row">
        <p class="text-gray-600">Cty TNHH truyền thông MMO Tạp Hoá</p>
        <span class="text-primary font-bold">Mua nhanh bán nhanh, hoàn tất giao dịch chưa tới 2p</span>
    </div>
</footer>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@endpush

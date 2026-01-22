<style>
    /* Custom animations cho social icons */
    @keyframes pulseAttention {
        0% { transform: scale(1); }
        5% { transform: scale(1.1); }
        10% { transform: scale(1); }
        15% { transform: scale(1.1); }
        20% { transform: scale(1); }
        100% { transform: scale(1); }
    }

    @keyframes shakeIcon {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(10deg); }
        50% { transform: rotate(-10deg); }
        75% { transform: rotate(5deg); }
        100% { transform: rotate(0deg); }
    }

    @keyframes wiggleAttention {
        0% { transform: rotate(0deg) scale(1); }
        85% { transform: rotate(0deg) scale(1); }
        90% { transform: rotate(10deg) scale(1.15); }
        92% { transform: rotate(-10deg) scale(1.15); }
        94% { transform: rotate(10deg) scale(1.15); }
        96% { transform: rotate(-10deg) scale(1.15); }
        98% { transform: rotate(5deg) scale(1.1); }
        100% { transform: rotate(0deg) scale(1); }
    }

    @keyframes colorChange {
        0% { color: white; }
        50% { color: rgba(255, 255, 255, 0.7); }
        100% { color: white; }
    }

    @keyframes glowEffect {
        0% { transform: scale(1); opacity: 0.6; }
        50% { transform: scale(1.4); opacity: 0; }
        100% { transform: scale(1); opacity: 0; }
    }

    @keyframes bounceAttention {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    @keyframes popIn {
        0% { transform: scale(0); }
        60% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .animate-pulse-attention {
        animation: pulseAttention 2s infinite;
    }

    .animate-shake-icon {
        animation: shakeIcon 0.5s ease-in-out;
    }

    .animate-wiggle-attention {
        animation: wiggleAttention 3s infinite;
    }

    .animate-color-change {
        animation: colorChange 8s infinite;
    }

    .animate-glow-effect {
        animation: glowEffect 2s infinite;
    }

    .animate-bounce-attention {
        animation: bounceAttention 2s infinite;
    }

    .animate-pop-in {
        animation: popIn 0.5s forwards;
    }

    /* Đảm bảo animations không bị override bởi Tailwind transforms */
    .social-icon {
        will-change: transform;
    }

    /* Hover effect - nâng lên và shake */
    .social-icon:hover {
        transform: translateY(-3px);
    }

    /* Đảm bảo glow effect không che màu primary */
    .social-icon::after {
        background-color: var(--color-primary) !important;
        opacity: 0;
    }

    /* Đảm bảo animations không bị conflict */
    .social-icon.animate-pulse-attention,
    .social-icon.animate-wiggle-attention {
        animation-fill-mode: both;
    }

    /* Hover effect với animations */
    .social-icon:hover {
        animation: shakeIcon 0.5s ease-in-out;
    }
    
    /* Khi hover, vẫn giữ base animations */
    .social-icon:hover.animate-pulse-attention {
        animation: shakeIcon 0.5s ease-in-out, pulseAttention 2s infinite 0.5s;
    }

    .social-icon:hover.animate-wiggle-attention {
        animation: shakeIcon 0.5s ease-in-out, wiggleAttention 3s infinite 0.5s;
    }

    /* Mobile specific styles */
    @media (max-width: 767px) {
        .social-icons-mobile-hidden {
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
        }

        .social-icons-mobile-show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            bottom: 80px !important;
        }

        .social-icon-mobile-hidden {
            transform: scale(0);
        }

        .social-icon-mobile-show {
            transform: scale(1);
        }

        /* Giảm kích thước social icons trên mobile */
        .social-icon {
            width: 36px !important;
            height: 36px !important;
        }

        .social-icon i,
        .social-icon span {
            font-size: 0.875rem !important; /* text-sm */
        }
    }
</style>

{{-- Button toggle cho mobile (ẩn trên desktop >= 768px) - Đã tắt để mobile luôn hiển thị như desktop --}}
{{-- <div class="fixed bottom-5 left-[15px] w-11 h-11 bg-primary-6 text-white rounded-full flex items-center justify-center z-[10000] shadow-[0_3px_10px_rgba(0,0,0,0.3)] cursor-pointer animate-bounce-attention md:hidden" id="socialToggle">
    <i class="fas fa-plus text-2xl transition-transform duration-300"></i>
</div> --}}

<!-- Social Icons -->
<div class="fixed bottom-[45px] right-6 md:bottom-[65px] right-[20px] md:right-6 flex flex-col md:flex-col gap-2.5 z-[9999] transition-all duration-500 ease-in-out md:!opacity-100 md:!visible md:!transform-none" id="socialIcons">
    @forelse($socials as $social)
        <a href="{{ $social->url }}" target="_blank" 
           class="social-icon relative flex items-center justify-center w-9 h-9 md:w-11 md:h-11 bg-primary hover:bg-primary-2 text-white rounded-full no-underline transition-colors duration-300 shadow-[0_2px_5px_rgba(0,0,0,0.2)] hover:shadow-[0_4px_10px_rgba(0,0,0,0.3)] animate-pulse-attention md:!scale-100 md:!transform-none group first:animate-wiggle-attention first:shadow-[0_3px_8px_rgba(0,0,0,0.3)] after:content-[''] after:absolute after:w-full after:h-full after:rounded-full after:bg-primary after:-z-10 after:opacity-0 after:animate-glow-effect" 
           aria-label="{{ $social->name }}">
            @if (strpos($social->icon, 'custom-') === 0)
                <span class="{{ $social->icon }} text-xl animate-color-change"></span>
            @else
                <i class="{{ $social->icon }} text-xl animate-color-change"></i>
            @endif
        </a>
    @empty
        <a href="https://facebook.com" target="_blank" 
           class="social-icon relative flex items-center justify-center w-9 h-9 md:w-11 md:h-11 bg-primary hover:bg-primary-2 text-white rounded-full no-underline transition-colors duration-300 shadow-[0_2px_5px_rgba(0,0,0,0.2)] hover:shadow-[0_4px_10px_rgba(0,0,0,0.3)] animate-pulse-attention md:!scale-100 md:!transform-none group first:animate-wiggle-attention first:shadow-[0_3px_8px_rgba(0,0,0,0.3)] after:content-[''] after:absolute after:w-full after:h-full after:rounded-full after:bg-primary after:-z-10 after:opacity-0 after:animate-glow-effect" 
           aria-label="Facebook">
            <i class="fab fa-facebook-f text-xl animate-color-change"></i>
        </a>
        <a href="mailto:contact@pinknovel.com" target="_blank" 
           class="social-icon relative flex items-center justify-center w-11 h-11 md:w-11 bg-primary hover:bg-primary-2 text-white rounded-full no-underline transition-all duration-300 shadow-[0_2px_5px_rgba(0,0,0,0.2)] hover:shadow-[0_4px_10px_rgba(0,0,0,0.3)] hover:-translate-y-0.5 animate-pulse-attention social-icon-mobile-hidden md:!scale-100 md:!transform-none after:content-[''] after:absolute after:w-full after:h-full after:rounded-full after:bg-primary after:-z-10 after:opacity-0 after:animate-glow-effect" 
           aria-label="Email">
            <i class="fas fa-envelope text-xl animate-color-change"></i>
        </a>
    @endforelse
</div>

{{-- Toggle social icons khi nhấn nút - Đã tắt để mobile luôn hiển thị như desktop --}}
{{-- <script>
    // Toggle social icons khi nhấn nút
    document.addEventListener('DOMContentLoaded', function() {
        const socialToggle = document.getElementById('socialToggle');
        const socialIcons = document.getElementById('socialIcons');

        if(socialToggle && socialIcons) {
            socialToggle.addEventListener('click', function() {
                socialIcons.classList.toggle('social-icons-mobile-show');
                socialIcons.classList.toggle('social-icons-mobile-hidden');
                socialToggle.classList.toggle('active');

                // Thêm animation pop-in cho các icons khi mở
                const icons = socialIcons.querySelectorAll('a');
                if (socialIcons.classList.contains('social-icons-mobile-show')) {
                    icons.forEach((icon, index) => {
                        setTimeout(() => {
                            icon.classList.remove('social-icon-mobile-hidden');
                            icon.classList.add('social-icon-mobile-show', 'animate-pop-in');
                        }, index * 100);
                    });
                } else {
                    icons.forEach((icon) => {
                        icon.classList.remove('social-icon-mobile-show', 'animate-pop-in');
                        icon.classList.add('social-icon-mobile-hidden');
                    });
                }
            });

            // Xoay icon trong toggle button
            const toggleIcon = socialToggle.querySelector('i');
            if (toggleIcon) {
                socialToggle.addEventListener('click', function() {
                    if (socialToggle.classList.contains('active')) {
                        toggleIcon.style.transform = 'rotate(45deg)';
                    } else {
                        toggleIcon.style.transform = 'rotate(0deg)';
                    }
                });
            }

            // Đóng social icons khi click ra ngoài (chỉ trên mobile)
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 768) {
                    if (!socialToggle.contains(e.target) && !socialIcons.contains(e.target) && socialIcons.classList.contains('social-icons-mobile-show')) {
                        socialIcons.classList.remove('social-icons-mobile-show');
                        socialIcons.classList.add('social-icons-mobile-hidden');
                        socialToggle.classList.remove('active');
                        if (toggleIcon) {
                            toggleIcon.style.transform = 'rotate(0deg)';
                        }
                        const icons = socialIcons.querySelectorAll('a');
                        icons.forEach((icon) => {
                            icon.classList.remove('social-icon-mobile-show', 'animate-pop-in');
                            icon.classList.add('social-icon-mobile-hidden');
                        });
                    }
                }
            });
        }
    });
</script> --}}
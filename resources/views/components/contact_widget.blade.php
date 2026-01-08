<style>
    /* Style cơ bản cho social icons */
    .social-icons-widget {
        position: fixed;
        bottom: 47px;
        right: 24px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 9999;
        transition: all 0.5s ease;
    }

    .social-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        background-color: var(--primary-color);
        color: white;
        border-radius: 50%;
        text-decoration: none;
        transition: transform 0.3s, background 0.3s, box-shadow 0.3s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        animation: pulseAttention 2s infinite;
    }

    /* Animation hiệu ứng nhấp nháy nhẹ */
    @keyframes pulseAttention {
        0% { transform: scale(1); }
        5% { transform: scale(1.1); }
        10% { transform: scale(1); }
        15% { transform: scale(1.1); }
        20% { transform: scale(1); }
        100% { transform: scale(1); }
    }

    /* Hiệu ứng rung lắc khi hover */
    .social-icon:hover {
        background-color: var(--primary-color-2);
        animation: shakeIcon 0.5s ease-in-out;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        transform: translateY(-3px);
    }

    @keyframes shakeIcon {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(10deg); }
        50% { transform: rotate(-10deg); }
        75% { transform: rotate(5deg); }
        100% { transform: rotate(0deg); }
    }

    /* Hiệu ứng nổi bật cho icon đầu tiên */
    .social-icons-widget a:first-child {
        animation: wiggleAttention 3s infinite;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
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

    /* Hiệu ứng đổi màu cho biểu tượng */
    .social-icon i, .social-icon span {
        animation: colorChange 8s infinite;
        font-size: 1.2rem;
    }

    @keyframes colorChange {
        0% { color: white; }
        50% { color: rgba(255, 255, 255, 0.7); }
        100% { color: white; }
    }

    /* Hiệu ứng phát sáng xung quanh */
    .social-icon::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: var(--primary-color-3);
        z-index: -1;
        opacity: 0.6;
        animation: glowEffect 2s infinite;
    }

    @keyframes glowEffect {
        0% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.3); opacity: 0; }
        100% { transform: scale(1); opacity: 0; }
    }

    /* Mobile Toggle Button - Ẩn trên desktop */
    .social-toggle {
        display: none;
    }

    /* Responsive: Điều chỉnh cho mobile */
    @media (max-width: 767px) {
        /* Đổi vị trí sang góc trái và ẩn các social icon */
        .social-icons-widget {
            bottom: 20px;
            left: 15px;
            right: auto;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            flex-direction: column-reverse; /* Hiển thị từ dưới lên */
        }

        .social-icon {
            width: 40px;
            height: 40px;
            transform: scale(0);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Hiển thị nút toggle trên mobile */
        .social-toggle {
            display: flex;
            position: fixed;
            bottom: 20px;
            left: 15px;
            width: 45px;
            height: 45px;
            background-color: var(--primary-color-6);
            color: white;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            animation: bounceAttention 2s infinite;
        }

        .social-toggle i {
            font-size: 1.5rem;
            transition: transform 0.3s;
        }

        /* Hiệu ứng nhảy nhẹ cho nút toggle */
        @keyframes bounceAttention {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Hiển thị social icons khi nút được kích hoạt */
        .social-icons-widget.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            bottom: 80px; /* Đẩy lên cao hơn nút toggle */
        }

        .social-icons-widget.show .social-icon {
            transform: scale(1);
        }

        /* Hiệu ứng xuất hiện tuần tự từng icon */
        .social-icons-widget.show .social-icon:nth-child(1) {
            transition-delay: 0.1s;
        }

        .social-icons-widget.show .social-icon:nth-child(2) {
            transition-delay: 0.2s;
        }

        .social-icons-widget.show .social-icon:nth-child(3) {
            transition-delay: 0.3s;
        }

        .social-icons-widget.show .social-icon:nth-child(4) {
            transition-delay: 0.4s;
        }

        .social-icons-widget.show .social-icon:nth-child(5) {
            transition-delay: 0.5s;
        }

        /* Chuyển đổi icon trong nút toggle khi mở */
        .social-toggle.active i {
            transform: rotate(45deg);
        }

        /* Hiệu ứng khi mới mở lần đầu */
        .social-icons-widget.show .social-icon {
            animation: popIn 0.5s forwards;
        }

        @keyframes popIn {
            0% { transform: scale(0); }
            60% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    }
</style>

<!-- Button toggle cho mobile -->
<div class="social-toggle" id="socialToggle">
    <i class="fas fa-plus"></i>
</div>

<!-- Social Icons -->
<div class="social-icons-widget mb-3 py-3" id="socialIcons">
    @forelse($socials as $social)
        <a href="{{ $social->url }}" target="_blank" class="social-icon" aria-label="{{ $social->name }}">
            @if (strpos($social->icon, 'custom-') === 0)
                <span class="{{ $social->icon }}"></span>
            @else
                <i class="{{ $social->icon }}"></i>
            @endif
        </a>
    @empty
        <a href="https://facebook.com" target="_blank" class="social-icon" aria-label="Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="mailto:contact@pinknovel.com" target="_blank" class="social-icon" aria-label="Email">
            <i class="fas fa-envelope"></i>
        </a>
    @endforelse
</div>

<script>
    // Toggle social icons khi nhấn nút
    document.addEventListener('DOMContentLoaded', function() {
        const socialToggle = document.getElementById('socialToggle');
        const socialIcons = document.getElementById('socialIcons');

        if(socialToggle && socialIcons) {
            socialToggle.addEventListener('click', function() {
                socialIcons.classList.toggle('show');
                socialToggle.classList.toggle('active');

                // Thêm hiệu ứng âm thanh nhẹ khi click (tuỳ chọn)
                // const audio = new Audio('/path/to/pop-sound.mp3');
                // audio.volume = 0.3;
                // audio.play();
            });

            // Đóng social icons khi click ra ngoài
            document.addEventListener('click', function(e) {
                if (!socialToggle.contains(e.target) && !socialIcons.contains(e.target) && socialIcons.classList.contains('show')) {
                    socialIcons.classList.remove('show');
                    socialToggle.classList.remove('active');
                }
            });
        }
    });
</script>

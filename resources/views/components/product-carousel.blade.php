@props([
    'title' => 'Lối tắt',
    'products' => [],
    'carouselId' => 'productCarousel',
    'routeName' => 'products.show', // Default to products.show, can be 'services.show' for services
])


<div class="">
    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>

    <div class="swiper {{ $carouselId }} shortcutsSwiper">
        <div class="swiper-wrapper">
            @forelse($products as $product)
                <div class="swiper-slide">
                    <a href="{{ route($routeName, $product['slug'] ?? ($product['id'] ?? 1)) }}"
                        class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group h-full flex flex-col block">
                        <div class="relative h-48 bg-gray-100 overflow-hidden flex-shrink-0">
                            <img src="{{ asset($product['image'] ?? 'images/placeholder.jpg') }}"
                                alt="{{ $product['title'] ?? 'Product' }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'300\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                        </div>
                        <div class="p-4 flex flex-col flex-grow">
                            <h5
                                class="text-sm font-semibold mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                {{ $product['title'] ?? 'Product Title' }}
                            </h5>
                            <div class="flex items-center gap-1 mb-2">
                                <div class="flex text-yellow-400">
                                    @php
                                        $rating = $product['rating'] ?? 5;
                                        $fullStars = floor($rating);
                                        $hasHalfStar = $rating - $fullStars >= 0.5;
                                    @endphp
                                    @for ($i = 0; $i < $fullStars; $i++)
                                        <i class="fas fa-star text-xs"></i>
                                    @endfor
                                    @if ($hasHalfStar)
                                        <i class="fas fa-star-half-alt text-xs"></i>
                                    @endif
                                    @for ($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++)
                                        <i class="far fa-star text-xs"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500 ml-1">
                                    {{ number_format($product['reviews'] ?? 0, 0, ',', '.') }} Reviews
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="text-xs text-gray-700">{{ $product['category'] ?? 'Category' }}</span>
                                <span
                                    class="text-xs text-primary ml-1">{{ $product['subcategory'] ?? 'Subcategory' }}</span>
                            </div>
                            <div class="text-sm font-bold text-primary mt-auto">
                                @if (isset($product['price_min']) && isset($product['price_max']) && $product['price_max'] != $product['price_min'])
                                    {{ number_format($product['price_min'] ?? 0, 0, ',', '.') }} -
                                    {{ number_format($product['price_max'] ?? 0, 0, ',', '.') }}
                                @else
                                    {{ number_format($product['price_min'] ?? ($product['price'] ?? 0), 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="swiper-slide">
                    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                        <p class="text-gray-500">Không có sản phẩm nào</p>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="swiper-pagination swiper-pagination-{{ $carouselId }} mt-6"></div>
    </div>
</div>

@push('styles')
    <style>
        .{{ $carouselId }}.shortcutsSwiper {
            padding-bottom: 50px;
        }

        .{{ $carouselId }}.shortcutsSwiper .swiper-slide {
            height: auto;
        }

        .{{ $carouselId }}.shortcutsSwiper .swiper-slide>a {
            height: 100%;
        }

        .{{ $carouselId }}.shortcutsSwiper .swiper-pagination-{{ $carouselId }} {
            position: relative;
            bottom: 0;
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .{{ $carouselId }}.shortcutsSwiper .swiper-pagination-{{ $carouselId }}.swiper-pagination-lock,
        .{{ $carouselId }}.shortcutsSwiper .swiper-pagination-{{ $carouselId }}[style*="display: none"] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            margin-top: 0 !important;
        }

        .{{ $carouselId }}.shortcutsSwiper .swiper-pagination-{{ $carouselId }} .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: #d1d5db;
            opacity: 1;
            transition: all 0.3s ease;
            margin: 0 4px;
        }

        .{{ $carouselId }}.shortcutsSwiper .swiper-pagination-{{ $carouselId }} .swiper-pagination-bullet-active {
            background: var(--color-primary);
            width: 32px;
            border-radius: 6px;
        }
    </style>
@endpush




@push('scripts')
    <script>
        (function() {
            const carouselId = '{{ $carouselId }}';
            const carouselSelector = '.' + carouselId;
            const paginationSelector = '.swiper-pagination-' + carouselId;

            function initCarousel() {
                const carousel = document.querySelector(carouselSelector);
                const paginationEl = document.querySelector(paginationSelector);

                if (!carousel) {
                    console.error('Carousel container not found:', carouselSelector);
                    return false;
                }

                if (!paginationEl) {
                    console.error('Pagination element not found:', paginationSelector);
                    return false;
                }

                if (typeof Swiper === 'undefined') {
                    console.warn('Swiper library not loaded yet, retrying...');
                    return false;
                }

                try {
                    const productsCount = {{ count($products) }};

                    const swiperInstance = new Swiper(carouselSelector, {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        loop: false,
                        grabCursor: true,
                        watchOverflow: false,
                        autoplay: false,
                        allowSlideNext: true,
                        allowSlidePrev: true,
                        pagination: {
                            el: paginationSelector,
                            clickable: true,
                            dynamicBullets: false,
                            renderBullet: function(index, className) {
                                return '<span class="' + className + '"></span>';
                            },
                            type: 'bullets',
                        },
                        observer: true,
                        observeParents: true,
                        breakpoints: {
                            640: {
                                slidesPerView: 2,
                                spaceBetween: 20,
                            },
                            768: {
                                slidesPerView: 3,
                                spaceBetween: 24,
                            },
                            1024: {
                                slidesPerView: 4,
                                spaceBetween: 24,
                            },
                            1280: {
                                slidesPerView: 5,
                                spaceBetween: 24,
                            },
                        },
                        speed: 600,
                        effect: 'slide',
                        on: {
                            init: function() {
                                const swiper = this;
                                const totalSlides = {{ count($products) }};

                                setTimeout(() => {
                                    const pagEl = document.querySelector(paginationSelector);
                                    if (pagEl) {
                                        if (totalSlides > 5) {
                                            pagEl.classList.remove('swiper-pagination-lock');
                                            pagEl.style.display = 'flex';
                                            pagEl.style.visibility = 'visible';
                                            pagEl.style.opacity = '1';
                                            pagEl.style.height = 'auto';

                                            if (swiper.pagination) {
                                                swiper.pagination.render();
                                                swiper.pagination.update();
                                            }
                                        } else {
                                            pagEl.style.display = 'none';
                                            pagEl.style.visibility = 'hidden';
                                            pagEl.style.opacity = '0';
                                            pagEl.style.height = '0';
                                        }
                                    }
                                }, 100);
                            },
                            afterInit: function() {
                                const swiper = this;
                                const totalSlides = {{ count($products) }};

                                if (totalSlides > 5) {
                                    setTimeout(() => {
                                        const pagEl = document.querySelector(paginationSelector);
                                        if (pagEl) {
                                            pagEl.classList.remove('swiper-pagination-lock');
                                            pagEl.style.display = 'flex';
                                            pagEl.style.visibility = 'visible';
                                            pagEl.style.opacity = '1';
                                            if (swiper.pagination) {
                                                swiper.pagination.render();
                                                swiper.pagination.update();
                                            }
                                        }
                                    }, 200);
                                } else {
                                    setTimeout(() => {
                                        const pagEl = document.querySelector(paginationSelector);
                                        if (pagEl) {
                                            pagEl.style.display = 'none';
                                        }
                                    }, 200);
                                }
                            },
                            resize: function() {
                                const swiper = this;
                                const totalSlides = {{ count($products) }};

                                if (totalSlides > 5) {
                                    setTimeout(() => {
                                        const pagEl = document.querySelector(paginationSelector);
                                        if (pagEl) {
                                            pagEl.classList.remove('swiper-pagination-lock');
                                            pagEl.style.display = 'flex';
                                            if (swiper.pagination) {
                                                swiper.pagination.render();
                                                swiper.pagination.update();
                                            }
                                        }
                                    }, 100);
                                } else {
                                    setTimeout(() => {
                                        const pagEl = document.querySelector(paginationSelector);
                                        if (pagEl) {
                                            pagEl.style.display = 'none';
                                        }
                                    }, 100);
                                }
                            }
                        }
                    });
                    const totalSlides = {{ count($products) }};
                    setTimeout(() => {
                        const pagEl = document.querySelector(paginationSelector);
                        if (pagEl) {
                            if (totalSlides > 5) {
                                pagEl.classList.remove('swiper-pagination-lock');
                                pagEl.style.display = 'flex';
                                pagEl.style.visibility = 'visible';
                                pagEl.style.opacity = '1';
                                pagEl.style.height = 'auto';
                            } else {
                                pagEl.style.display = 'none';
                                pagEl.style.visibility = 'hidden';
                                pagEl.style.opacity = '0';
                                pagEl.style.height = '0';
                                pagEl.style.marginTop = '0';
                            }
                        }
                    }, 500);

                    return true;
                } catch (error) {
                    console.error('❌ Error initializing carousel:', carouselId, error);
                    return false;
                }
            }

            function waitForSwiper(attempts = 0) {
                if (attempts > 50) {
                    console.error('Failed to load Swiper after 50 attempts');
                    return;
                }

                if (typeof Swiper !== 'undefined') {
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', function() {
                            setTimeout(initCarousel, 100);
                        });
                    } else {
                        setTimeout(initCarousel, 100);
                    }
                } else {
                    setTimeout(function() {
                        waitForSwiper(attempts + 1);
                    }, 100);
                }
            }

            waitForSwiper();
        })();
    </script>
@endpush

@php
    use App\Models\AuctionBanner;
    use Illuminate\Support\Facades\Storage;
    
    // Get active banners for left position
    $leftBanners = AuctionBanner::active()
        ->byPosition('left')
        ->orderBy('display_order', 'asc')
        ->with(['bannerable', 'bid.seller'])
        ->get();
    
    // Get active banners for right position
    $rightBanners = AuctionBanner::active()
        ->byPosition('right')
        ->orderBy('display_order', 'asc')
        ->with(['bannerable', 'bid.seller'])
        ->get();
@endphp

@if($leftBanners->count() > 0 || $rightBanners->count() > 0)
    <div class="auction-banners-wrapper">
        <!-- Left Banner -->
        @if($leftBanners->count() > 0)
            <div class="auction-banner-left">
                <div class="auction-banner-sticky">
                    @foreach($leftBanners as $banner)
                        @php
                            $bannerable = $banner->bannerable;
                            $routeName = $banner->bannerable_type === 'App\Models\Product' ? 'products.show' : 'services.show';
                        @endphp
                        <div class="auction-banner-item mb-4">
                            <a href="{{ route($routeName, $bannerable->slug) }}" class="auction-banner-link" target="_blank">
                                <div class="auction-banner-card">
                                    @if($bannerable->image)
                                        <div class="auction-banner-image">
                                            <img src="{{ Storage::url($bannerable->image) }}" alt="{{ $bannerable->name }}" 
                                                onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                        </div>
                                    @endif
                                    <div class="auction-banner-content">
                                        <h4 class="auction-banner-title">{{ Str::limit($bannerable->name, 50) }}</h4>
                                        @if($bannerable_type === 'App\Models\Product')
                                            @php
                                                $minPrice = $bannerable->variants->min('price') ?? 0;
                                                $maxPrice = $bannerable->variants->max('price') ?? 0;
                                            @endphp
                                            <p class="auction-banner-price">
                                                @if($maxPrice != $minPrice)
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫ - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                                @else
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫
                                                @endif
                                            </p>
                                        @else
                                            @php
                                                $minPrice = $bannerable->variants->min('price') ?? 0;
                                                $maxPrice = $bannerable->variants->max('price') ?? 0;
                                            @endphp
                                            <p class="auction-banner-price">
                                                @if($maxPrice != $minPrice)
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫ - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                                @else
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫
                                                @endif
                                            </p>
                                        @endif
                                        <div class="auction-banner-badge">
                                            <i class="fas fa-fire"></i> Đấu giá
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Right Banner -->
        @if($rightBanners->count() > 0)
            <div class="auction-banner-right">
                <div class="auction-banner-sticky">
                    @foreach($rightBanners as $banner)
                        @php
                            $bannerable = $banner->bannerable;
                            $routeName = $banner->bannerable_type === 'App\Models\Product' ? 'products.show' : 'services.show';
                        @endphp
                        <div class="auction-banner-item mb-4">
                            <a href="{{ route($routeName, $bannerable->slug) }}" class="auction-banner-link" target="_blank">
                                <div class="auction-banner-card">
                                    @if($bannerable->image)
                                        <div class="auction-banner-image">
                                            <img src="{{ Storage::url($bannerable->image) }}" alt="{{ $bannerable->name }}" 
                                                onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                        </div>
                                    @endif
                                    <div class="auction-banner-content">
                                        <h4 class="auction-banner-title">{{ Str::limit($bannerable->name, 50) }}</h4>
                                        @if($banner->bannerable_type === 'App\Models\Product')
                                            @php
                                                $minPrice = $bannerable->variants->min('price') ?? 0;
                                                $maxPrice = $bannerable->variants->max('price') ?? 0;
                                            @endphp
                                            <p class="auction-banner-price">
                                                @if($maxPrice != $minPrice)
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫ - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                                @else
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫
                                                @endif
                                            </p>
                                        @else
                                            @php
                                                $minPrice = $bannerable->variants->min('price') ?? 0;
                                                $maxPrice = $bannerable->variants->max('price') ?? 0;
                                            @endphp
                                            <p class="auction-banner-price">
                                                @if($maxPrice != $minPrice)
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫ - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                                @else
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫
                                                @endif
                                            </p>
                                        @endif
                                        <div class="auction-banner-badge">
                                            <i class="fas fa-fire"></i> Đấu giá
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <style>
        .auction-banners-wrapper {
            position: relative;
            width: 100%;
        }

        .auction-banner-left,
        .auction-banner-right {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            width: 200px;
            z-index: 40;
            max-height: 80vh;
            overflow-y: auto;
        }

        .auction-banner-left {
            left: 20px;
        }

        .auction-banner-right {
            right: 20px;
        }

        .auction-banner-sticky {
            position: sticky;
            top: 20px;
        }

        .auction-banner-item {
            width: 100%;
        }

        .auction-banner-link {
            display: block;
            text-decoration: none;
            color: inherit;
        }

        .auction-banner-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid #3b82f6;
        }

        .auction-banner-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
        }

        .auction-banner-image {
            width: 100%;
            height: 150px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .auction-banner-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .auction-banner-content {
            padding: 12px;
        }

        .auction-banner-title {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .auction-banner-price {
            font-size: 14px;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 8px;
        }

        .auction-banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .auction-banner-left,
            .auction-banner-right {
                width: 160px;
            }
        }

        @media (max-width: 1200px) {
            .auction-banner-left,
            .auction-banner-right {
                display: none;
            }
        }

        /* Scrollbar */
        .auction-banner-sticky::-webkit-scrollbar {
            width: 4px;
        }

        .auction-banner-sticky::-webkit-scrollbar-track {
            background: transparent;
        }

        .auction-banner-sticky::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        .auction-banner-sticky::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endif

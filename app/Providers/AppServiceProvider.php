<?php

namespace App\Providers;

use App\Models\Promotion;
use App\Models\ProductValue;
use App\Models\ProductVariant;
use App\Models\SellerRegistration;
use App\Models\Dispute;
use App\Models\Refund;
use App\Models\Withdrawal;
use App\Models\Category;
use App\Models\ServiceCategory;
use App\Enums\WithdrawalStatus;
use App\Policies\ProductValuePolicy;
use App\Enums\SellerRegistrationStatus;
use App\Enums\DisputeStatus;
use App\Enums\RefundStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Log;
use App\Observers\PromotionObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Observers\ProductVariantObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Register Policies
        Gate::policy(ProductValue::class, ProductValuePolicy::class);

        // ============================================
        // Eloquent Strict Mode - Tối ưu và phát hiện lỗi
        // ============================================
        $this->configureEloquentStrictMode();

        // ============================================
        // Database Query Monitoring - Phát hiện query chậm
        // ============================================
        $this->configureQueryMonitoring();

        // ============================================
        // Request/Command Lifecycle Monitoring
        // ============================================
        $this->configureLifecycleMonitoring();

        $logoSite = null;
        try {
            if (Schema::hasTable('logo_sites')) {
                $logoSite = \App\Models\LogoSite::first();
            }
        } catch (\Exception $e) {
        }

        $logoPath = $logoSite && $logoSite->logo
            ? Storage::url($logoSite->logo)
            : asset('images/logo/Logo-site-1050-x-300.webp');

        $faviconPath = $logoSite && $logoSite->favicon
            ? Storage::url($logoSite->favicon)
            : asset('favicon.ico');

        view()->share('faviconPath', $faviconPath);
        view()->share('logoPath', $logoPath);

        View::composer('admin.layouts.sidebar', function ($view) {
            try {
                $app = app();
                
                if (!$app->bound('pending_products_count')) {
                    if (Schema::hasTable('products')) {
                        $count = \App\Models\Product::where('status', \App\Enums\ProductStatus::PENDING)->count();
                        $app->instance('pending_products_count', $count);
                    } else {
                        $app->instance('pending_products_count', 0);
                    }
                }
                $view->with('pendingProductsCount', $app->make('pending_products_count'));
                
                // Pending Services Count
                if (!$app->bound('pending_services_count')) {
                    if (Schema::hasTable('services')) {
                        $count = \App\Models\Service::where('status', \App\Enums\ServiceStatus::PENDING)->count();
                        $app->instance('pending_services_count', $count);
                    } else {
                        $app->instance('pending_services_count', 0);
                    }
                }
                $view->with('pendingServicesCount', $app->make('pending_services_count'));
                
                if (!$app->bound('pending_seller_registrations_count')) {
                    if (Schema::hasTable('seller_registrations')) {
                        $count = SellerRegistration::where('status', SellerRegistrationStatus::PENDING)->count();
                        $app->instance('pending_seller_registrations_count', $count);
                    } else {
                        $app->instance('pending_seller_registrations_count', 0);
                    }
                }
                $view->with('pendingSellerRegistrationsCount', $app->make('pending_seller_registrations_count'));

                if (!$app->bound('reviewing_disputes_count')) {
                    if (Schema::hasTable('disputes')) {
                        $count = Dispute::where('status', DisputeStatus::REVIEWING)->count();
                        $app->instance('reviewing_disputes_count', $count);
                    } else {
                        $app->instance('reviewing_disputes_count', 0);
                    }
                }
                $view->with('reviewingDisputesCount', $app->make('reviewing_disputes_count'));

                if (!$app->bound('reviewing_service_disputes_count')) {
                    if (Schema::hasTable('service_disputes')) {
                        $count = \App\Models\ServiceDispute::where('status', \App\Enums\ServiceDisputeStatus::REVIEWING)->count();
                        $app->instance('reviewing_service_disputes_count', $count);
                    } else {
                        $app->instance('reviewing_service_disputes_count', 0);
                    }
                }
                $view->with('reviewingServiceDisputesCount', $app->make('reviewing_service_disputes_count'));

                if (!$app->bound('pending_refunds_count')) {
                    if (Schema::hasTable('refunds')) {
                        $count = Refund::where('status', RefundStatus::PENDING)->count();
                        $app->instance('pending_refunds_count', $count);
                    } else {
                        $app->instance('pending_refunds_count', 0);
                    }
                }
                $view->with('pendingRefundsCount', $app->make('pending_refunds_count'));

                if (!$app->bound('pending_withdrawals_count')) {
                    if (Schema::hasTable('withdrawals')) {
                        $count = Withdrawal::whereIn('status', [WithdrawalStatus::PENDING, WithdrawalStatus::PROCESSING])->count();
                        $app->instance('pending_withdrawals_count', $count);
                    } else {
                        $app->instance('pending_withdrawals_count', 0);
                    }
                }
                $view->with('pendingWithdrawalsCount', $app->make('pending_withdrawals_count'));

                if (!$app->bound('unread_contact_submissions_count')) {
                    if (Schema::hasTable('contact_submissions')) {
                        $count = \App\Models\ContactSubmission::whereNull('read_at')->count();
                        $app->instance('unread_contact_submissions_count', $count);
                    } else {
                        $app->instance('unread_contact_submissions_count', 0);
                    }
                }
                $view->with('unreadContactSubmissionsCount', $app->make('unread_contact_submissions_count'));

                if (!$app->bound('pending_shares_count')) {
                    if (Schema::hasTable('shares')) {
                        $count = \App\Models\Share::where('status', \App\Enums\ShareStatus::PENDING)->count();
                        $app->instance('pending_shares_count', $count);
                    } else {
                        $app->instance('pending_shares_count', 0);
                    }
                }
                $view->with('pendingSharesCount', $app->make('pending_shares_count'));
            } catch (\Exception $e) {
                $view->with('pendingProductsCount', 0);
                $view->with('pendingServicesCount', 0);
                $view->with('pendingSellerRegistrationsCount', 0);
                $view->with('reviewingDisputesCount', 0);
                $view->with('reviewingServiceDisputesCount', 0);
                $view->with('pendingRefundsCount', 0);
                $view->with('pendingWithdrawalsCount', 0);
                $view->with('unreadContactSubmissionsCount', 0);
                $view->with('pendingSharesCount', 0);
            }
        });

        View::composer('seller.layouts.sidebar', function ($view) {
            try {
                $app = app();
                $user = Auth::user();

                if ($user) {
                    $cacheKey = 'seller_open_disputes_count_' . $user->id;
                    if (!$app->bound($cacheKey)) {
                        if (Schema::hasTable('disputes')) {
                            $count = Dispute::where('status', DisputeStatus::OPEN)
                                ->whereHas('order', function ($query) use ($user) {
                                    $query->where('seller_id', $user->id);
                                })
                                ->count();
                            $app->instance($cacheKey, $count);
                        } else {
                            $app->instance($cacheKey, 0);
                        }
                    }
                    $view->with('openDisputesCount', $app->make($cacheKey));

                    // Count pending service orders (PAID status - cần seller xử lý)
                    $cacheKeyServiceOrders = 'seller_pending_service_orders_count_' . $user->id;
                    if (!$app->bound($cacheKeyServiceOrders)) {
                        if (Schema::hasTable('service_orders')) {
                            $count = \App\Models\ServiceOrder::where('seller_id', $user->id)
                                ->where('status', \App\Enums\ServiceOrderStatus::PAID)
                                ->count();
                            $app->instance($cacheKeyServiceOrders, $count);
                        } else {
                            $app->instance($cacheKeyServiceOrders, 0);
                        }
                    }
                    $view->with('pendingServiceOrdersCount', $app->make($cacheKeyServiceOrders));

                    // Count open service disputes (cần seller phản hồi)
                    $cacheKeyServiceDisputes = 'seller_open_service_disputes_count_' . $user->id;
                    if (!$app->bound($cacheKeyServiceDisputes)) {
                        if (Schema::hasTable('service_disputes')) {
                            $count = \App\Models\ServiceDispute::where('status', \App\Enums\ServiceDisputeStatus::OPEN)
                                ->whereHas('serviceOrder', function ($query) use ($user) {
                                    $query->where('seller_id', $user->id);
                                })
                                ->count();
                            $app->instance($cacheKeyServiceDisputes, $count);
                        } else {
                            $app->instance($cacheKeyServiceDisputes, 0);
                        }
                    }
                    $view->with('openServiceDisputesCount', $app->make($cacheKeyServiceDisputes));
                } else {
                    $view->with('openDisputesCount', 0);
                    $view->with('pendingServiceOrdersCount', 0);
                    $view->with('openServiceDisputesCount', 0);
                }
            } catch (\Exception $e) {
                $view->with('openDisputesCount', 0);
                $view->with('pendingServiceOrdersCount', 0);
                $view->with('openServiceDisputesCount', 0);
            }
        });

        View::composer('client.layouts.partials.header', function ($view) {
            try {
                if (Schema::hasTable('categories')) {
                    $categories = Category::with(['subCategories' => function ($q) {
                        $q->active()->ordered();
                    }])->active()->ordered()->get();
                } else {
                    $categories = collect();
                }

                if (Schema::hasTable('service_categories')) {
                    $serviceCategories = ServiceCategory::with(['serviceSubCategories' => function ($q) {
                        $q->active()->ordered();
                    }])->active()->ordered()->get();
                } else {
                    $serviceCategories = collect();
                }

                // Load header configs
                $supportBar = null;
                $promotionalBanner = null;
                if (Schema::hasTable('header_configs')) {
                    $supportBar = \App\Models\HeaderConfig::getSupportBar();
                    $promotionalBanner = \App\Models\HeaderConfig::getPromotionalBanner();
                }

                $view->with('headerCategories', $categories);
                $view->with('headerServiceCategories', $serviceCategories);
                $view->with('supportBar', $supportBar);
                $view->with('promotionalBanner', $promotionalBanner);
            } catch (\Exception $e) {
                $view->with('headerCategories', collect());
                $view->with('headerServiceCategories', collect());
                $view->with('supportBar', null);
                $view->with('promotionalBanner', null);
            }
        });
    }

    /**
     * Cấu hình Eloquent Strict Mode
     * - Prevent lazy loading (N+1 queries)
     * - Prevent accessing missing attributes
     * - Prevent silently discarding attributes
     */
    private function configureEloquentStrictMode(): void
    {
        // Bật strict mode cho Eloquent (3 tính năng cùng lúc)
        Model::shouldBeStrict();

        // Trong production, log lazy loading thay vì ném exception
        if ($this->app->environment('production')) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);
                Log::warning("Attempted to lazy load [{$relation}] on model [{$class}]", [
                    'model' => $class,
                    'relation' => $relation,
                    'model_id' => $model->getKey(),
                ]);
            });
        }

        // Hai tính năng này liên quan tính đúng đắn - bật mọi môi trường
        // Prevent accessing missing attributes (khi select một vài cột)
        Model::preventAccessingMissingAttributes();
        
        // Prevent silently discarding attributes (khi fill không fillable)
        Model::preventSilentlyDiscardingAttributes();

        // Lazy loading chỉ là vấn đề hiệu năng - không chặn production
        // Ở dev/test: throw exception ngay
        // Ở production: chỉ log warning
        Model::preventLazyLoading(!$this->app->environment('production'));
    }

    /**
     * Cấu hình monitoring cho database queries
     * Phát hiện và log các query chậm
     */
    private function configureQueryMonitoring(): void
    {
        // Tổng thời gian query > 2000ms trong một request/command
        DB::whenQueryingForLongerThan(2000, function (Connection $connection) {
            Log::warning("Database queries exceeded 2 seconds", [
                'connection' => $connection->getName(),
                'queries' => $connection->getQueryLog(),
            ]);
        });

        //Log tất cả queries chậm hơn 500ms (optional - có thể comment nếu quá nhiều log)
        DB::listen(function ($query) {
            if ($query->time > 500) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                ]);
            }
        });
    }

    /**
     * Cấu hình monitoring cho request/command lifecycle
     * Phát hiện và log các request/command chạy chậm
     * 
     * Note: Có thể implement bằng middleware hoặc event listeners
     */
    private function configureLifecycleMonitoring(): void
    {
        // Log request chậm bằng event listener
        if (!$this->app->runningInConsole()) {
            $this->app['events']->listen('Illuminate\Foundation\Http\Events\RequestHandled', function ($event) {
                $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT', microtime(true));
                $duration = (microtime(true) - $startTime) * 1000;
                
                if ($duration > 5000) {
                    Log::warning('A request took longer than 5 seconds', [
                        'path' => $event->request->path(),
                        'method' => $event->request->method(),
                        'status' => $event->response->getStatusCode(),
                        'duration' => round($duration, 2) . 'ms',
                        'ip' => $event->request->ip(),
                    ]);
                }
            });
        }
    }
}

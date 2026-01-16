<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['orders', 'product_variants', 'disputes', 'refunds', 'wallet_transactions', 'product_values'];
        
        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'slug')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->string('slug')->nullable()->after('id');
                });
            }
        }

        $this->generateSlugsForExistingData();

        foreach ($tables as $table) {
            $indexName = $table . '_slug_unique';
            
            $hasIndex = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            
            if (empty($hasIndex)) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unique('slug');
                });
            }
            
            $indexName2 = $table . '_slug_index';
            $hasIndex2 = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName2]);
            
            if (empty($hasIndex2)) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->index('slug');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('disputes', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('product_values', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }

    /**
     * Generate slugs for existing data
     */
    private function generateSlugsForExistingData(): void
    {
        // Orders
        $orders = \DB::table('orders')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($orders as $order) {
            \DB::table('orders')
                ->where('id', $order->id)
                ->update(['slug' => 'order-' . Str::lower(Str::random(12))]);
        }

        // Product Variants
        $variants = \DB::table('product_variants')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($variants as $variant) {
            $slug = Str::slug($variant->name);
            if (empty($slug)) {
                $slug = 'variant';
            }
            if (strlen($slug) > 50) {
                $slug = substr($slug, 0, 50);
            }
            \DB::table('product_variants')
                ->where('id', $variant->id)
                ->update(['slug' => $slug . '-' . Str::lower(Str::random(8))]);
        }

        // Disputes
        $disputes = \DB::table('disputes')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($disputes as $dispute) {
            \DB::table('disputes')
                ->where('id', $dispute->id)
                ->update(['slug' => 'dispute-' . Str::lower(Str::random(12))]);
        }

        // Refunds
        $refunds = \DB::table('refunds')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($refunds as $refund) {
            \DB::table('refunds')
                ->where('id', $refund->id)
                ->update(['slug' => 'refund-' . Str::lower(Str::random(12))]);
        }

        // Wallet Transactions
        $transactions = \DB::table('wallet_transactions')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($transactions as $transaction) {
            \DB::table('wallet_transactions')
                ->where('id', $transaction->id)
                ->update(['slug' => 'txn-' . Str::lower(Str::random(12))]);
        }

        // Product Values
        $values = \DB::table('product_values')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($values as $value) {
            \DB::table('product_values')
                ->where('id', $value->id)
                ->update(['slug' => 'val-' . Str::lower(Str::random(12))]);
        }
    }
};


<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RegenerateSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slugs:regenerate 
                            {model? : Tên class model (ví dụ: Product, Category)}
                            {--all : Tạo lại slug cho tất cả các model}
                            {--force : Tạo lại slug ngay cả khi slug đã tồn tại}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo lại slug cho các model sử dụng trait HasSlug';

    /**
     * Models that use HasSlug trait
     */
    protected array $sluggableModels = [
        'Category' => \App\Models\Category::class,
        'SubCategory' => \App\Models\SubCategory::class,
        'Product' => \App\Models\Product::class,
        'ProductVariant' => \App\Models\ProductVariant::class,
        'ProductValue' => \App\Models\ProductValue::class,
        'Order' => \App\Models\Order::class,
        'Dispute' => \App\Models\Dispute::class,
        'Refund' => \App\Models\Refund::class,
        'WalletTransaction' => \App\Models\WalletTransaction::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $modelName = $this->argument('model');
        $all = $this->option('all');
        $force = $this->option('force');

        if (!$modelName && !$all) {
            $this->error('Vui lòng chỉ định tên model hoặc sử dụng flag --all');
            $this->info('Available models: ' . implode(', ', array_keys($this->sluggableModels)));
            return 1;
        }

        if ($all) {
            foreach ($this->sluggableModels as $name => $class) {
                $this->regenerateForModel($name, $class, $force);
            }
        } else {
            if (!isset($this->sluggableModels[$modelName])) {
                $this->error("Model '{$modelName}' không tồn tại hoặc không sử dụng trait HasSlug");
                $this->info('Available models: ' . implode(', ', array_keys($this->sluggableModels)));
                return 1;
            }

            $this->regenerateForModel($modelName, $this->sluggableModels[$modelName], $force);
        }

        $this->newLine();
        $this->info('Hoàn thành tạo slug!');

        return 0;
    }

    /**
     * Regenerate slugs for a specific model
     */
    protected function regenerateForModel(string $name, string $class, bool $force): void
    {
        $this->newLine();
        $this->info("Đang xử lý {$name}...");

        $query = $class::query();

        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('slug')
                    ->orWhere('slug', '')
                    ->orWhereRaw("LENGTH(slug) - LENGTH(REPLACE(slug, '-', '')) < 2");
            });
        }

        $count = $query->count();

        if ($count === 0) {
            $this->line("Không có bản ghi cần cập nhật");
            return;
        }

        $this->line("Tìm thấy {$count} bản ghi cần cập nhật");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;
        $query->chunkById(100, function ($records) use (&$updated, $bar) {
            foreach ($records as $record) {
                try {
                    $record->regenerateSlug();
                    $updated++;
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Lỗi khi cập nhật ID {$record->id}: {$e->getMessage()}");
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->line("Cập nhật {$updated} bản ghi");
    }
}

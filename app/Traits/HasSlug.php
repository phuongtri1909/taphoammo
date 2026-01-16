<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });

        static::updating(function ($model) {
            if ($model->shouldRegenerateSlugOnUpdate() && $model->isDirty($model->getSlugSource())) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    public function generateUniqueSlug(): string
    {
        $source = $this->getSlugSourceValue();
        
        $slug = Str::slug($source);
        
        if (empty($slug)) {
            $slug = $this->getSlugPrefix();
        }
        
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $maxLength = $this->getSlugMaxLength();
        if (strlen($slug) > $maxLength) {
            $slug = substr($slug, 0, $maxLength);
            $slug = rtrim($slug, '-');
        }
        
        $randomLength = $this->getRandomStringLength();
        $randomString = Str::lower(Str::random($randomLength));
        $finalSlug = $slug . '-' . $randomString;
        
        $count = 0;
        while ($this->slugExists($finalSlug)) {
            $randomString = Str::lower(Str::random($randomLength));
            $finalSlug = $slug . '-' . $randomString;
            
            $count++;
            if ($count > 10) {
                $finalSlug = $slug . '-' . time() . '-' . Str::random(4);
                break;
            }
        }
        
        return $finalSlug;
    }

    protected function slugExists(string $slug): bool
    {
        $query = static::where('slug', $slug);
        
        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }
        
        return $query->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function getSlugSourceValue(): string
    {
        $source = $this->getSlugSource();
        
        if (is_array($source)) {
            $values = [];
            foreach ($source as $field) {
                if (!empty($this->{$field})) {
                    $values[] = $this->{$field};
                }
            }
            return implode(' ', $values);
        }
        
        return $this->{$source} ?? '';
    }

    protected function getSlugSource(): string|array
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }

    protected function getSlugPrefix(): string
    {
        return property_exists($this, 'slugPrefix') ? $this->slugPrefix : 'item';
    }

    protected function getSlugMaxLength(): int
    {
        return property_exists($this, 'slugMaxLength') ? $this->slugMaxLength : 50;
    }

    protected function getRandomStringLength(): int
    {
        return property_exists($this, 'randomStringLength') ? $this->randomStringLength : 8;
    }

    protected function shouldRegenerateSlugOnUpdate(): bool
    {
        return property_exists($this, 'regenerateSlugOnUpdate') ? $this->regenerateSlugOnUpdate : false;
    }

    public function regenerateSlug(): void
    {
        $this->slug = $this->generateUniqueSlug();
        $this->save();
    }

    public function getUrlAttribute(): ?string
    {
        $routeName = $this->getRouteNameForUrl();
        
        if ($routeName && \Route::has($routeName)) {
            return route($routeName, $this);
        }
        
        return null;
    }

    protected function getRouteNameForUrl(): ?string
    {
        return property_exists($this, 'routeNameForUrl') ? $this->routeNameForUrl : null;
    }
}


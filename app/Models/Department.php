<?php

namespace App\Models;

use App\Support\Sizing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;

    /** @var list<string> Hidden on the storefront until they have active products. */
    public const GATED_UNTIL_STOCK_SLUGS = [
        'womens-wear',
        'kids-girls',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'hero_image_path',
        'size_chart_image_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Department $department) {
            if (empty($department->slug)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('sort_order');
    }

    public function activeCategories(): HasMany
    {
        return $this->categories()->where('is_active', true);
    }

    public function heroImageUrl(): ?string
    {
        return $this->hero_image_path
            ? Storage::disk('public')->url($this->hero_image_path)
            : null;
    }

    public function usesAgeSizing(): bool
    {
        return Sizing::departmentUsesAgeSizing($this);
    }

    public function hasActiveProducts(): bool
    {
        return $this->categories()
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->exists();
    }

    public function isVisibleOnStorefront(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! in_array($this->slug, self::GATED_UNTIL_STOCK_SLUGS, true)) {
            return true;
        }

        return $this->hasActiveProducts();
    }

    public function scopeVisibleOnStorefront(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $inner) {
                $inner->whereNotIn('slug', self::GATED_UNTIL_STOCK_SLUGS)
                    ->orWhereHas('categories', function (Builder $categoryQuery) {
                        $categoryQuery->where('is_active', true)
                            ->whereHas('products', fn (Builder $productQuery) => $productQuery->where('is_active', true));
                    });
            });
    }

    public static function forStorefront(): Collection
    {
        return static::query()
            ->visibleOnStorefront()
            ->with(['activeCategories' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

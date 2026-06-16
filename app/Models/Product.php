<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'fabric',
        'size_chart_image_path',
        'wash_care_content',
        'sleeve',
        'color_label',
        'base_price',
        'currency',
        'is_active',
        'is_featured',
        'sort_order',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'base_price' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->orderByRaw("CASE size WHEN 'S' THEN 1 WHEN 'M' THEN 2 WHEN 'L' THEN 3 WHEN 'XL' THEN 4 ELSE 5 END");
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function publishedReviews(): HasMany
    {
        return $this->reviews()->where('is_published', true);
    }

    public function averageRating(): ?float
    {
        $average = $this->published_reviews_avg_rating ?? $this->publishedReviews()->avg('rating');

        return $average !== null ? round((float) $average, 1) : null;
    }

    public function reviewsCount(): int
    {
        return (int) ($this->published_reviews_count ?? $this->publishedReviews()->count());
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images->where('is_primary', true)->first() ?? $this->images->first();
    }

    public function formattedPrice(): string
    {
        return '₹' . number_format($this->base_price);
    }

    public function resolvedSizeChartImagePath(): ?string
    {
        if ($this->size_chart_image_path) {
            return $this->size_chart_image_path;
        }

        return $this->category?->size_chart_image_path;
    }

    public function resolvedSizeChartImageUrl(): ?string
    {
        $path = $this->resolvedSizeChartImagePath();

        return $path ? Storage::disk('public')->url($path) : null;
    }

    public function resolvedWashCareContent(): ?string
    {
        if (filled($this->wash_care_content)) {
            return $this->wash_care_content;
        }

        return $this->category?->wash_care_content;
    }

    public function washCareHtml(): string
    {
        $fabric = '<p><strong>Fabric:</strong> '.e($this->fabric).'</p>';
        $content = $this->resolvedWashCareContent();

        if ($content) {
            return $fabric.'<div class="mt-2 prose prose-sm max-w-none text-brand-black/75">'.$content.'</div>';
        }

        return $fabric.'<p class="mt-2">Wash cold with similar colours. Line dry in shade. Light iron on the reverse to retain handfeel.</p>';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

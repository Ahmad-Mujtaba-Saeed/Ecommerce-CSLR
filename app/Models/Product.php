<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'product_type',
        'listing_type',
        'sku',
        'category_id',
        'price',
        'price_discounted',
        'currency',
        'discount_rate',
        'vat_rate',
        'user_id',
        'status',
        'is_promoted',
        'promote_start_date',
        'promote_end_date',
        'promote_plan',
        'promote_day',
        'is_special_offer',
        'special_offer_date',
        'visibility',
        'rating',
        'pageviews',
        'demo_url',
        'external_link',
        'files_included',
        'stock',
        'shipping_class_id',
        'shipping_delivery_time_id',
        'multiple_sale',
        'digital_file_download_link',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'zip_code',
        'brand_id',
        'is_sold',
        'is_deleted',
        'is_draft',
        'is_edited',
        'is_active',
        'is_free_product',
        'is_rejected',
        'reject_reason',
        'is_affiliate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'price_discounted' => 'integer',
        'discount_rate' => 'integer',
        'vat_rate' => 'double',
        'status' => 'boolean',
        'is_promoted' => 'boolean',
        'is_special_offer' => 'boolean',
        'visibility' => 'boolean',
        'stock' => 'integer',
        'multiple_sale' => 'boolean',
        'is_sold' => 'boolean',
        'is_deleted' => 'boolean',
        'is_draft' => 'boolean',
        'is_edited' => 'boolean',
        'is_active' => 'boolean',
        'is_free_product' => 'boolean',
        'is_rejected' => 'boolean',
        'is_affiliate' => 'boolean',
        'promote_start_date' => 'datetime',
        'promote_end_date' => 'datetime',
        'special_offer_date' => 'datetime',
    ];

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the product details (multilingual).
     */
    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    /**
     * Get the license keys for the product.
     */
    public function licenseKeys()
    {
        return $this->hasMany(ProductLicenseKey::class);
    }

    /**
     * Get the search indexes for the product.
     */
    public function searchIndexes()
    {
        return $this->hasMany(ProductSearchIndex::class);
    }

    /**
     * Get the user that owns the product.
     */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('is_deleted', false)
                    ->where('status', true);
    }

    /**
     * Scope a query to only include promoted products.
     */
    public function scopePromoted($query)
    {
        $now = now();
        return $query->where('is_promoted', true)
                    ->where('promote_start_date', '<=', $now)
                    ->where('promote_end_date', '>=', $now);
    }

    /**
     * Scope a query to only include special offer products.
     */
    public function scopeSpecialOffers($query)
    {
        return $query->where('is_special_offer', true)
                    ->orderBy('special_offer_date', 'DESC');
    }

    /**
     * Get the product's price in a formatted way.
     */
    public function getFormattedPriceAttribute()
    {
        $currency = $this->currency ?: 'USD';
        return number_format($this->price / 100, 2) . ' ' . $currency;
    }

    /**
     * Get the product's discounted price in a formatted way.
     */
    public function getFormattedDiscountedPriceAttribute()
    {
        $price = $this->price_discounted ?: $this->price;
        $currency = $this->currency ?: 'USD';
        return number_format($price / 100, 2) . ' ' . $currency;
    }

    /**
     * Check if the product is on sale.
     */
    public function getIsOnSaleAttribute()
    {
        return $this->price_discounted > 0 && $this->price_discounted < $this->price;
    }

    /**
     * Get the discount percentage.
     */
    /**
     * Get the variations for the product.
     */
    public function variations()
    {
        return $this->hasMany(Variation::class)->where('parent_id', 0);
    }

    /**
     * Get all variations including child variations.
     */
    public function allVariations()
    {
        return $this->hasMany(Variation::class);
    }

    /**
     * Get all images for the product.
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get the main image for the product.
     */
    public function mainImage()
    {
        return $this->hasOne(Image::class)->where('is_main', true);
    }

    /**
     * Get the default variation options for the product.
     */
    public function defaultVariationOptions()
    {
        return $this->hasManyThrough(
            VariationOption::class,
            Variation::class,
            'product_id',
            'variation_id'
        )->where('is_default', true);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->price_discounted > 0 && $this->price > 0) {
            return round((($this->price - $this->price_discounted) / $this->price) * 100);
        }
        return 0;
    }
}

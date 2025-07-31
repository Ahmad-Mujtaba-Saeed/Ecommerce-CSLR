<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VariationOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'variation_id',
        'parent_id',
        'option_names',
        'stock',
        'color',
        'price',
        'price_discounted',
        'discount_rate',
        'is_default',
        'use_default_price'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'option_names' => 'array',
        'stock' => 'integer',
        'price' => 'integer',
        'price_discounted' => 'integer',
        'discount_rate' => 'integer',
        'is_default' => 'boolean',
        'use_default_price' => 'boolean',
    ];

    /**
     * Get the variation that owns this option.
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    /**
     * Get the parent option if this is a child option.
     */
    public function parent()
    {
        return $this->belongsTo(VariationOption::class, 'parent_id');
    }

    /**
     * Get the child options.
     */
    public function children()
    {
        return $this->hasMany(VariationOption::class, 'parent_id');
    }

    /**
     * Scope a query to only include default options.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the option name for a specific language.
     */
    public function getOptionName($langId = 1)
    {
        if (!is_array($this->option_names)) {
            return null;
        }

        foreach ($this->option_names as $option) {
            if ($option['lang_id'] == $langId) {
                return $option['option_name'];
            }
        }

        return null;
    }

    /**
     * Get the final price (discounted if available).
     */
    public function getFinalPrice()
    {
        if ($this->price_discounted > 0 && $this->price_discounted < $this->price) {
            return $this->price_discounted;
        }
        return $this->price;
    }

    /**
     * Check if the option is on sale.
     */
    public function isOnSale()
    {
        return $this->price_discounted > 0 && $this->price_discounted < $this->price;
    }
}

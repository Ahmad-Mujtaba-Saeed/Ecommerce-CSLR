<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'parent_id',
        'label_names',
        'variation_type',
        'insert_type',
        'option_display_type',
        'show_images_on_slider',
        'use_different_price',
        'is_visible'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'label_names' => 'array',
        'show_images_on_slider' => 'boolean',
        'use_different_price' => 'boolean',
        'is_visible' => 'boolean',
    ];

    /**
     * Get the product that owns the variation.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that owns the variation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent variation if this is a child variation.
     */
    public function parent()
    {
        return $this->belongsTo(Variation::class, 'parent_id');
    }

    /**
     * Get the child variations.
     */
    public function children()
    {
        return $this->hasMany(Variation::class, 'parent_id');
    }

    /**
     * Get the options for this variation.
     */
    public function options()
    {
        return $this->hasMany(VariationOption::class);
    }

    /**
     * Get the default option for this variation.
     */
    public function defaultOption()
    {
        return $this->hasOne(VariationOption::class)->where('is_default', true);
    }

    /**
     * Scope a query to only include visible variations.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Get the label name for a specific language.
     */
    public function getLabel($langId = 1)
    {
        if (!is_array($this->label_names)) {
            return null;
        }

        foreach ($this->label_names as $label) {
            if ($label['lang_id'] == $langId) {
                return $label['label'];
            }
        }

        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ProductLicenseKey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_license_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'license_key',
        'is_used',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'is_used' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'license_key', // Hide license key by default for security
    ];

    /**
     * Get the product that owns the license key.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include used license keys.
     */
    public function scopeUsed(Builder $query): Builder
    {
        return $query->where('is_used', true);
    }

    /**
     * Scope a query to only include unused license keys.
     */
    public function scopeUnused(Builder $query): Builder
    {
        return $query->where('is_used', false);
    }

    /**
     * Scope a query to only include license keys for a specific product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Mark the license key as used.
     *
     * @return bool
     */
    public function markAsUsed(): bool
    {
        return $this->update(['is_used' => true]);
    }

    /**
     * Mark the license key as unused.
     *
     * @return bool
     */
    public function markAsUnused(): bool
    {
        return $this->update(['is_used' => false]);
    }

    /**
     * Check if the license key is valid (exists and not used).
     *
     * @param string $licenseKey
     * @return bool
     */
    public static function isValid(string $licenseKey): bool
    {
        return static::where('license_key', $licenseKey)
                    ->where('is_used', false)
                    ->exists();
    }

    /**
     * Generate a new license key.
     *
     * @param int $productId
     * @param string|null $key If null, a random key will be generated
     * @return self
     */
    public static function generate(int $productId, ?string $key = null): self
    {
        return static::create([
            'product_id' => $productId,
            'license_key' => $key ?? self::generateRandomKey(),
            'is_used' => false,
        ]);
    }

    /**
     * Generate a random license key.
     *
     * @param int $segments Number of segments in the key
     * @param int $segmentLength Length of each segment
     * @return string
     */
    public static function generateRandomKey(int $segments = 5, int $segmentLength = 5): string
    {
        $key = '';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        for ($i = 0; $i < $segments; $i++) {
            $segment = '';
            for ($j = 0; $j < $segmentLength; $j++) {
                $segment .= $characters[rand(0, strlen($characters) - 1)];
            }
            $key .= $segment;
            if ($i < $segments - 1) {
                $key .= '-';
            }
        }
        
        return $key;
    }

    /**
     * Get the masked license key (shows only first and last segments).
     *
     * @return string
     */
    public function getMaskedKeyAttribute(): string
    {
        $segments = explode('-', $this->license_key);
        $count = count($segments);
        
        if ($count <= 2) {
            return str_repeat('*', strlen($this->license_key));
        }
        
        return $segments[0] . '-****-****-' . $segments[$count - 1];
    }

    /**
     * Get the full license key (only for authorized users).
     *
     * @return string|null
     */
    public function getFullKeyAttribute(): ?string
    {
        // Add your authorization logic here
        // For example: if (auth()->check() && auth()->user()->can('view-license-keys'))
        return $this->license_key;
        
        // Return null or throw an exception if not authorized
        // return null;
    }
}

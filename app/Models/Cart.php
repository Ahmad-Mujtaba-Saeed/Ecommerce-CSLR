<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $table = 'cart_products';
    
    protected $fillable = [
        'user_id',
        'product_id',
        'variation_option_id',
        'quantity',
        'cart_item_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($cart) {
            if (empty($cart->cart_item_id)) {
                $cart->cart_item_id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variation option associated with the cart item.
     */
    public function variationOption(): BelongsTo
    {
        return $this->belongsTo(VariationOption::class, 'variation_option_id');
    }

    /**
     * Scope a query to only include items for the current authenticated user.
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', Auth::id());
    }

    /**
     * Add or update a product in the cart.
     */
    public static function addOrUpdateItem(array $data): self
    {
        $userId = Auth::id();
        $cartItemId = $data['cart_item_id'] ?? null;
        
        $item = self::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->when(isset($data['variation_option_id']), function($query) use ($data) {
                $query->where('variation_option_id', $data['variation_option_id']);
            }, function($query) {
                $query->whereNull('variation_option_id');
            })
            ->first();

        if ($item) {
            // Update existing item
            $item->update([
                'quantity' => $item->quantity + ($data['quantity'] ?? 1),
            ]);
            return $item;
        }

        // Create new item
        return self::create([
            'user_id' => $userId,
            'product_id' => $data['product_id'],
            'variation_option_id' => $data['variation_option_id'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
        ]);
    }

    /**
     * Get the cart items count for the current user.
     */
    public static function getCartCount(): int
    {
        return self::where('user_id', Auth::id())->sum('quantity');
    }
}

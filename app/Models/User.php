<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'slug',
        'email',
        'email_status',
        'token',
        'password',
        'role_id',
        'balance',
        'number_of_sales',
        'user_type',
        'facebook_id',
        'google_id',
        'vkontakte_id',
        'avatar',
        'cover_image',
        'cover_image_type',
        'banned',
        'first_name',
        'last_name',
        'about_me',
        'phone_number',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'zip_code',
        'show_email',
        'show_phone',
        'show_location',
        'social_media_data',
        'last_seen',
        'show_rss_feeds',
        'send_email_new_message',
        'is_active_shop_request',
        'shop_request_reject_reason',
        'shop_request_date',
        'vendor_documents',
        'is_membership_plan_expired',
        'is_used_free_plan',
        'cash_on_delivery',
        'is_fixed_vat',
        'fixed_vat_rate',
        'vat_rates_data',
        'vat_rates_data_state',
        'is_affiliate',
        'vendor_affiliate_status',
        'affiliate_commission_rate',
        'affiliate_discount_rate',
        'tax_registration_number',
        'vacation_mode',
        'vacation_message',
        'commission_debt',
        'account_delete_req',
        'account_delete_req_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_status' => 'boolean',
        'banned' => 'boolean',
        'show_email' => 'boolean',
        'show_phone' => 'boolean',
        'show_location' => 'boolean',
        'show_rss_feeds' => 'boolean',
        'send_email_new_message' => 'boolean',
        'is_active_shop_request' => 'boolean',
        'is_membership_plan_expired' => 'boolean',
        'is_used_free_plan' => 'boolean',
        'cash_on_delivery' => 'boolean',
        'is_fixed_vat' => 'boolean',
        'is_affiliate' => 'boolean',
        'vendor_affiliate_status' => 'boolean',
        'vacation_mode' => 'boolean',
        'account_delete_req' => 'boolean',
        'shop_request_date' => 'datetime',
        'account_delete_req_date' => 'datetime',
        'last_seen' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the social media data as an array.
     *
     * @param  string  $value
     * @return array
     */
    public function getSocialMediaDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the social media data.
     *
     * @param  array  $value
     * @return void
     */
    public function setSocialMediaDataAttribute($value)
    {
        $this->attributes['social_media_data'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the vendor documents as an array.
     *
     * @param  string  $value
     * @return array
     */
    public function getVendorDocumentsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the vendor documents.
     *
     * @param  array  $value
     * @return void
     */
    public function setVendorDocumentsAttribute($value)
    {
        $this->attributes['vendor_documents'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the VAT rates data as an array.
     *
     * @param  string  $value
     * @return array
     */
    public function getVatRatesDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the VAT rates data.
     *
     * @param  array  $value
     * @return void
     */
    public function setVatRatesDataAttribute($value)
    {
        $this->attributes['vat_rates_data'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the VAT rates data state as an array.
     *
     * @param  string  $value
     * @return array
     */
    public function getVatRatesDataStateAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the VAT rates data state.
     *
     * @param  array  $value
     * @return void
     */
    public function setVatRatesDataStateAttribute($value)
    {
        $this->attributes['vat_rates_data_state'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the user's role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param  string  $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role->name === $role;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role_id === 1; // Assuming 1 is the admin role ID
    }

    /**
     * Check if the user is a vendor.
     *
     * @return bool
     */
    public function isVendor()
    {
        return $this->role_id === 2; // Assuming 2 is the vendor role ID
    }

    /**
     * Check if the user is a member.
     *
     * @return bool
     */
    public function isMember()
    {
        return $this->role_id === 3; // Assuming 3 is the member role ID
    }

    /**
     * Check if the user is a moderator.
     *
     * @return bool
     */
    public function isModerator()
    {
        return $this->role_id === 4; // Assuming 4 is the moderator role ID
    }

    /**
     * Get the products for the user (if vendor).
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the country that the user belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that the user belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that the user belongs to.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the user's full address.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $address = [];
        
        if ($this->address) {
            $address[] = $this->address;
        }
        
        if ($this->city) {
            $address[] = $this->city->name;
        }
        
        if ($this->state) {
            $address[] = $this->state->name;
        }
        
        if ($this->zip_code) {
            $address[] = $this->zip_code;
        }
        
        if ($this->country) {
            $address[] = $this->country->name;
        }
        
        return implode(', ', $address);
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\SoftDeletes;




class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    /**
     * @return HasMany
     */
    public function carts()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany
     */
    public function wishlists()
    {
        return $this->hasOne(Wishlist::class);
    }

    /**
     * @return HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(UserInfo::class);
    }

    /**
     * @return BelongsToMany
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'users_discounts', 'user_id', 'discount_id');
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users', 'user_id', 'role_id')->withTimestamps()->withPivot('active');
    }

    /**
     * @return bool
     */
    public function hasRole($role)
    {
        // remove all the white space
        $role = (preg_replace('/\s+/', '', $role));

        // check user role in pivot table
        foreach ($this->roles as $userRole) {
            // if user role is active and matches the role passed in
            if ($userRole->name == $role && $userRole->pivot->active == 1) {
                return true;
            }

            // if user's role is superadmin then return true
            if ($userRole->name == UserRole::getKey(UserRole::Admin)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {

            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function activeRoles()
    {
        return $this->belongsToMany(Role::class, 'roles_users', 'user_id', 'role_id')->withTimeStamps()->wherePivot('active', true);
    }

    public function sendPasswordResetNotification($token)
    {

        $spaUrl = env('SPA_URL') ? env('SPA_URL') : 'http://localhost:8080';

        $url = $spaUrl . '/reset-password?token=' . $token;

        $this->notify(new ResetPasswordNotification($url));
    }
}

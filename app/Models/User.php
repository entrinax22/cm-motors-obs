<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'zip',
        'country',
        'profile_photo_url',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedPhoneAttribute()
    {
        // Format Philippine phone numbers
        $phone = preg_replace('/\D/', '', $this->phone);
        
        if (strlen($phone) === 11 && substr($phone, 0, 2) === '09') {
            return '+63' . substr($phone, 1);
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) === '9') {
            return '+63' . $phone;
        }
        
        return $this->phone;
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->attributes['profile_photo_url']
            ? asset('storage/' . $this->attributes['profile_photo_url'])
            : asset('images/default-profile.png');
    }

}

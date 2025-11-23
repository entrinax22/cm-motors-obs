<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';
    protected $fillable = [
        'booking_number',
        'user_id',
        'service_id',
        'scheduled_datetime',
        'status',
        'notes',
        'total_amount',
    ];

    protected $casts = [
        'scheduled_datetime' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }
}

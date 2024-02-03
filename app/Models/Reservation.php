<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'listing_id'
    ];

    public function listing() {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function forms() {
        return $this->hasMany(ReservationForm::class, 'reservation_id');
    }
}

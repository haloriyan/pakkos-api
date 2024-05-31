<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'listing_id',
        'payment_code', 'payment_type', 'payment_amount', 'payment_status', 'payment_payloads'
    ];

    public function listing() {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id', 'type_id', 'host_id', 'resident_id', 'amount',
        'payment_method', 'payment_channel', 'payment_status', 'payment_issued', 'payment_period'
    ];
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id', 'user_id', 'name', 'max_capacity', 'price_monthly',
    ];

    public function listing() {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}

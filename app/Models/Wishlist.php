<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'listing_id'
    ];

    public function listing() {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}

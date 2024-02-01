<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id', 'facility_id'
    ];
    
    public function facility() {
        return $this->belongsTo(Facility::class, 'facility_id');
    }
}

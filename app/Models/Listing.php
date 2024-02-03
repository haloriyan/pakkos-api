<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'consumer_name', 'slug', 'description',
        'province', 'city', 'subdistrict', 'address', 'address_note',
        'price', 'price_inclusion', 'room_size', 'room_total', 'room_available',
        'front_building_photo', 'inside_building_photo', 'streetview_building_photo',
        'front_room_photo', 'inside_room_photo', 'bath_room_photo', 'other_photo',
        'is_approved'
    ];
    
    public function facilities() {
        // return $this->hasMany(ListingFacility::class, 'listing_id');
        return $this->belongsToMany(Facility::class, 'listing_facilities', 'listing_id', 'facility_id');
    }
    public function facilities_raw() {
        return $this->hasMany(ListingFacility::class, 'listing_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id', 'template_id', 'answer'
    ];

    public function template() {
        return $this->belongsTo(Template::class, 'template_id');
    }
}

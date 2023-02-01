<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'image',
        'phone',
        'wishlist',
        'address',
        'presentSent',
        'presentReceived'
    ];
}

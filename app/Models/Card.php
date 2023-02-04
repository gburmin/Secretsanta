<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'box_id',
        'card_infos_id',
        'gift_sent',
        'gift_received',
        'wish_list'
    ];
    
     public function box()
    {
        return $this->belongsTo(Box::class, 'box_id')->first();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->first();
    }
    
    
}

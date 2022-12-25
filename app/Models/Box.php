<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cover',
        'email',
        'isPrivate',
        'cost',
        'max_people_in_box',
        'draw_starts_at',
        'creator_id'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id')->first();
    }
}

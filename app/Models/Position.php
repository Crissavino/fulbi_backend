<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sport_id',
        'name_key',
    ];

    public function sport() {
        return $this->belongsTo(Sport::class);
    }

    public function players() {
        return $this->belongsToMany(Player::class);
    }
}

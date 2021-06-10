<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_key',
    ];

    public function matches() {
        return $this->hasMany(Match::class);
    }

    public function players() {
        return $this->hasMany(Player::class);
    }
}

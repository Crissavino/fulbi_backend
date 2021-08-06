<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, SoftDeletes;

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

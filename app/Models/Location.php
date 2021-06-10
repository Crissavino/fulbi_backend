<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lat',
        'lng',
        'country',
        'country_code',
        'province',
        'province_code',
        'city',
        'place_id',
        'formatted_address',
    ];

    public function match() {
        return $this->belongsTo(Match::class);
    }

    public function player() {
        return $this->belongsTo(Player::class);
    }
}

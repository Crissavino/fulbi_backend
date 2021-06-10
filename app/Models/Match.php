<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'when_play',
        'genre_id',
        'type_id',
        'num_players',
        'cost',
        'chat_id',
        'owner_id',
    ];

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function genre() {
        return $this->belongsTo(Genre::class);
    }

    public function type() {
        return $this->belongsTo(Type::class);
    }

    public function chat() {
        return $this->belongsTo(Chat::class);
    }

    public function owner() {
        return $this->belongsTo(User::class);
    }
}

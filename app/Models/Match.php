<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Match extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'when_play',
        'genre_id',
        'type_id',
        'num_players',
        'cost',
        'chat_id',
        'owner_id',
        'currency_id',
    ];

    //protected $with = ['location', 'genre', 'type', 'chat', 'owner', 'players'];

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function currency() {
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

    public function players()
    {
        return $this->belongsToMany(Player::class);
    }

//    public function playersWithUser()
//    {
//        return $this->belongsToMany(Player::class)->with('user');
//    }


}

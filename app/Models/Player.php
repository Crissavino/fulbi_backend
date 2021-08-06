<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'team_id',
        'genre_id',
        'location_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

//    public function team() {
//        return $this->belongsTo(Team::class);
//    }

    public function genre() {
        return $this->belongsTo(Genre::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function matches()
    {
        return $this->belongsToMany(Match::class);
    }

    public function messages()
    {
        return $this->belongsToMany(Message::class);
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class);
    }
}

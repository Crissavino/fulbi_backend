<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    const TYPES = [
        'text' => 1,
        'image' => 2,
        'audio' => 3,
        'header' => 4,
    ];

    protected $fillable = [
        'text',
        'owner_id',
        'chat_id',
        'language',
        'type',
    ];

    public function players()
    {
        return $this->belongsToMany(Player::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function devices()
    {
        return $this->belongsToMany(Device::class);
    }

}

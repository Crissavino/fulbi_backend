<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

//    protected $fillable = [
//        'chat_id',
//    ];

    public function match() {
        return $this->belongsTo(Match::class);
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }
}

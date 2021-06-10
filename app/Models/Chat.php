<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

//    protected $fillable = [
//        'chat_id',
//    ];

    public function match() {
        return $this->belongsTo(Match::class);
    }
}

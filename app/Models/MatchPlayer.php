<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'player_id',
        'have_notifications'
    ];

    public function scoopMatchWithNotification($query)
    {
        dd($query);
        $query->where('have_notifications', 1);
    }
}

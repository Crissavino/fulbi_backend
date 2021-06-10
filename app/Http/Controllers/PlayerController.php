<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function showAll()
    {
        $matches = Match::where('owner_id', auth()->user()->id)->get();

        $players = [];
        foreach ($matches as $match) {
            foreach ($match->players as $player) {
                $players[] = $player;
            }
        }

        return view('players.index', [
            'players' => $players
        ]);

    }
}

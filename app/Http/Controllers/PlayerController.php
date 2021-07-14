<?php

namespace App\Http\Controllers;

use App\Models\Match;

class PlayerController extends Controller
{
    public function showAll()
    {
        $matches = Match::where('owner_id', auth()->user()->id)->get();

        $players = [];
        foreach ($matches as $match) {
            foreach ($match->players as $player) {
                $players[$player->id] = $player;
            }
        }
        $collect = collect($players);

        return view('players.index', [
            'players' => $collect->unique()
        ]);

    }
}

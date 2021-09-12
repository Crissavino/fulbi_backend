<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function showAll()
    {
        $players = Player::with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_fully_set', 1);
            })->simplePaginate(8);

        return view('players.index', [
            'players' => $players
        ]);

    }

    public function edit(Request $request, $id)
    {
        $player = Player::find($id);
        $player->user;

        return view('players.edit', [
            'player' => $player
        ]);
    }

    public function update(Request $request, $id)
    {
        $player = Player::find($id);

        if (!User::where('id', '!=', $player->user->id)->whereNickname($request->nickname)->exists()) {
            $player->user->update([
                'name' => $request->name,
                'nickname' => $request->nickname,
                'email' => $request->email,
            ]);

            return redirect()->route('players.all')->with('message', __('general.messages.playerUpdated'));
        } else {
            return redirect()->back()->with('message', __('errors.auth.nicknameTaken'));
        }
    }

    public function showAllMatchPlayers()
    {
        // $matches = Match::where('owner_id', auth()->user()->id)->get();
        $matches = Match::all();

        $allPlayers = [];
        foreach ($matches as $match) {
            $players = $match->players()->where('is_confirmed', true)->with(['user'])->where('is_existing_player', 0)->get();
            foreach ($players as $player) {
                $allPlayers[$player->id] = $player;
            }
        }
        $collect = collect($allPlayers);

        return view('players.allMatchPlayers', [
            'players' => $collect->unique()
        ]);

    }
}

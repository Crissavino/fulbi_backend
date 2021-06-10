<?php


namespace App\src\Infrastructure\Services;


use App\Models\Match;
use App\src\Domain\Services\MatchService;

class EloquentMatchService implements MatchService
{

    public function create($locationId, $when_play, $genre_id, $type_id, $num_players, $cost, $chatId, $userId)
    {
        return Match::create([
            'location_id' => $locationId,
            'when_play' => $when_play,
            'genre_id' => $genre_id,
            'type_id' => $type_id,
            'num_players' => $num_players,
            'cost' => $cost,
            'chat_id' => $chatId,
            'owner_id' => $userId
        ]);
    }
}

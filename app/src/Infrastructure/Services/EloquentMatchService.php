<?php


namespace App\src\Infrastructure\Services;


use App\Models\Match;
use App\src\Domain\Services\MatchService;

class EloquentMatchService implements MatchService
{

    public function create($locationId, $when_play, $genre_id, $type_id, $num_players, $is_free_match, $currency_id, $cost, $chatId, $userId, $description)
    {
        return Match::create([
            'location_id' => $locationId,
            'when_play' => $when_play,
            'genre_id' => $genre_id,
            'type_id' => $type_id,
            'num_players' => $num_players,
            'is_free_match' => $is_free_match,
            'currency_id' => $currency_id,
            'cost' => $cost,
            'chat_id' => $chatId,
            'owner_id' => $userId,
            'description' => $description
        ]);
    }

    public function get($matchId)
    {
        return Match::find($matchId);
    }

    public function update($matchId, $when_play, $genre_id, $type_id, $num_players, $is_free_match, $currency_id, $cost, $description)
    {
        Match::find($matchId)->update([
            'when_play' => $when_play,
            'genre_id' => $genre_id,
            'type_id' => $type_id,
            'num_players' => $num_players,
            'is_free_match' => $is_free_match,
            'currency_id' => $currency_id,
            'cost' => $cost,
            'description' => $description
        ]);

        return Match::find($matchId);
    }
}

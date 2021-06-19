<?php


namespace App\src\Domain\Services;


interface MatchService
{
    public function create($locationId, $when_play, $genre_id, $type_id, $num_players, $currency_id, $cost, $chatId, $userId);

    public function update($matchId, $when_play, $genre_id, $type_id, $num_players, $currency_id, $cost);

    public function get($matchId);
}

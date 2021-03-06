<?php


namespace App\src\Domain\Services;


interface MatchService
{
    public function create($locationId, $when_play, $genre_id, $type_id, $num_players, $is_free_match, $currency_id, $cost, $chatId, $userId, $description);

    public function update($matchId, $when_play, $genre_id, $type_id, $num_players, $is_free_match, $currency_id, $cost, $description);

    public function get($matchId);
}

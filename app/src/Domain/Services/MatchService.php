<?php


namespace App\src\Domain\Services;


interface MatchService
{
    public function create($locationId, $when_play, $genre_id, $type_id, $num_players, $cost, $chatId, $userId);
}

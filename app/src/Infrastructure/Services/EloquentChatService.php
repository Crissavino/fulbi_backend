<?php


namespace App\src\Infrastructure\Services;


use App\Models\Chat;
use App\src\Domain\Services\ChatService;

class EloquentChatService implements ChatService
{

    public function create()
    {
        return Chat::create();
    }
}

<?php


namespace App\src\Infrastructure\Services;


use App\Models\User;
use App\src\Domain\Services\UserService;

class EloquentUserService implements UserService
{

    public function get($userId)
    {
        return User::find($userId);
    }

    public function addOneCreatedMatch(User $user)
    {
        $add = $user->matches_created + 1;
        $user->update([
            'matches_created' => $add
        ]);
    }
}

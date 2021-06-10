<?php


namespace App\src\Domain\Services;


use App\Models\User;

interface UserService
{
    public function get($userId);

    public function addOneCreatedMatch(User $user);
}

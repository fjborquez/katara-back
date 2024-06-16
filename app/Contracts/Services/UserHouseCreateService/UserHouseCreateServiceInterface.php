<?php

namespace App\Contracts\Services\UserHouseCreateService;

interface UserHouseCreateServiceInterface
{
    public function create(int $userId, array $data): object;
}

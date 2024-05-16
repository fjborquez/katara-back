<?php

namespace App\Contracts\Services\UserHouseUpdateService;

interface UserHouseUpdateServiceInterface
{
    public function update(int $userId, array $data): void;
}

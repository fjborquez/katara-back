<?php

namespace App\Contracts\Services\UserHousesGetService;

interface UserHousesGetServiceInterface
{
    public function getAll(int $userId): array;
}

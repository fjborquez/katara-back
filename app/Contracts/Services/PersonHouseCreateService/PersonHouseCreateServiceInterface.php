<?php

namespace App\Contracts\Services\PersonHouseCreateService;

interface PersonHouseCreateServiceInterface
{
    public function create(int $addingUserId, int $houseId, array $residentData): void;
}

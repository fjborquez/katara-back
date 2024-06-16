<?php

namespace App\Contracts\Services\ResidentGetService;

interface ResidentGetServiceInterface
{
    public function get(int $userId, int $houseId, int $residentId): array;
}

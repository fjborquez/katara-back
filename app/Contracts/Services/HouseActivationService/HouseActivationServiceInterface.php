<?php

namespace App\Contracts\Services\HouseActivationService;

interface HouseActivationServiceInterface
{
    public function enable(int $houseId): void;

    public function disable(int $houseId): void;
}

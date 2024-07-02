<?php

namespace App\Http\Controllers;

use App\Contracts\Services\HouseActivationService\HouseActivationServiceInterface;

class HouseActivation extends Controller
{
    public function __construct(private readonly HouseActivationServiceInterface $houseActivationService) {}

    public function enable(int $userId, int $houseId)
    {
        $this->houseActivationService->enable($houseId);
    }

    public function disable(int $userId, int $houseId)
    {
        $this->houseActivationService->disable($houseId);
    }
}

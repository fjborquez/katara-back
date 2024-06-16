<?php

namespace App\Services\ResidentListService;

use App\Contracts\Services\ResidentListService\ResidentListServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;

class ResidentListService implements ResidentListServiceInterface
{
    public function __construct(
        private readonly UserExternalServiceInterface $userExternalService
    ) {}

    public function get(int $houseId)
    {
        return $this->userExternalService->getResidents($houseId);
    }
}

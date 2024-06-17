<?php

namespace App\Services\HouseActivationService;

use App\Contracts\Services\HouseActivationService\HouseActivationServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use Exception;

class HouseActivationService implements HouseActivationServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService)
    {
    }

    public function enable(int $houseId): void
    {
        try {
            $this->userExternalService->enableHouse($houseId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function disable(int $houseId): void
    {
        try {
            $this->userExternalService->disableHouse($houseId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

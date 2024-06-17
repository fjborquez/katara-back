<?php

namespace App\Services\ResidentGetService;

use App\Contracts\Services\ResidentGetService\ResidentGetServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use Exception;

class ResidentGetService implements ResidentGetServiceInterface
{
    public function __construct(
        private readonly UserExternalServiceInterface $userExternalService
    ) {
    }

    public function get(int $userId, int $houseId, int $residentId): array
    {
        $user = $this->userExternalService->getUser($userId);

        if (! $user) {
            throw new Exception('User not found');
        }

        $house = $this->userExternalService->getHouse($houseId);

        if (! $house) {
            throw new Exception('House not found');
        }

        return $this->userExternalService->getPerson($residentId);
    }
}

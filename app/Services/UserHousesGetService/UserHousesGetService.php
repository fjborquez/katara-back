<?php

namespace App\Services\UserHousesGetService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Contracts\Services\UserHousesGetService\UserHousesGetServiceInterface;
use Exception;

class UserHousesGetService implements UserHousesGetServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService)
    {
    }

    public function getAll(int $userId): array
    {
        try {
            $user = $this->userExternalService->getUser($userId);

            return $user->person->houses;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}

<?php

namespace App\Services\UserGetService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Contracts\Services\UserGetService\UserGetServiceInterface;

class UserGetService implements UserGetServiceInterface
{
    public function __construct(
        private readonly UserExternalServiceInterface $userExternalService
    ) {}

    public function get(int $userId): object
    {
        return $this->userExternalService->getUser($userId);
    }
}

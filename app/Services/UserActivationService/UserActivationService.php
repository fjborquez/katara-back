<?php

namespace App\Services\UserActivationService;

use App\Contracts\Services\UserActivationService\UserActivationServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use Exception;

class UserActivationService implements UserActivationServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService)
    {
    }

    public function enable(int $id): void
    {
        try {
            $this->userExternalService->enable($id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function disable(int $id): void
    {
        try {
            $this->userExternalService->disable($id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

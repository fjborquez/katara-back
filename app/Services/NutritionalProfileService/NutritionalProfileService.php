<?php

namespace App\Services\NutritionalProfileService;

use App\Contracts\Services\NutritionalProfileService\NutritionalProfileServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;

class NutritionalProfileService implements NutritionalProfileServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService)
    {

    }

    public function getProfile(int $userId): array
    {
        $user = $this->userExternalService->getUser($userId);
        return $this->userExternalService->getNutritionalProfile($user->person->id);
    }
}

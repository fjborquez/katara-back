<?php

namespace App\Services\NutritionalRestrictionService;

use App\Contracts\Services\NutritionalRestrictionService\NutritionalRestrictionServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;

class NutritionalRestrictionService implements NutritionalRestrictionServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService) {}

    public function getAll(): array
    {
        return $this->userExternalService->nutritionalRestrictionList();
    }
}

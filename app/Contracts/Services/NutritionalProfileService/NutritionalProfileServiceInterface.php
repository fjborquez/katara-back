<?php

namespace App\Contracts\Services\NutritionalProfileService;

interface NutritionalProfileServiceInterface
{
    public function getProfile(int $userId): array;
}

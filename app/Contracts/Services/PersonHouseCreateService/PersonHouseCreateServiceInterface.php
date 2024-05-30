<?php

namespace App\Contracts\Services\PersonHouseCreateService;

interface PersonHouseCreateServiceInterface
{
    public function create(array $residentData): array;
}

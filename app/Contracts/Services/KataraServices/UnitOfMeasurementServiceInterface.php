<?php

namespace App\Contracts\Services\KataraServices;

interface UnitOfMeasurementServiceInterface
{
    public function list(array $filter = []): array;
}

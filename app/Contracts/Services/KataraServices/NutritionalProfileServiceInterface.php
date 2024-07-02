<?php

namespace App\Contracts\Services\KataraServices;

interface NutritionalProfileServiceInterface
{
    public function get(int $userId): array;
}

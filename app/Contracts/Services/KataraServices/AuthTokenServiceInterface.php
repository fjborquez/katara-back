<?php

namespace App\Contracts\Services\KataraServices;

interface AuthTokenServiceInterface
{
    public function create(array $data = []): array;
}

<?php

namespace App\Contracts\Services\KataraServices;

interface ProductTypeServiceInterface
{
    public function list(): array;

    public function create(array $data = []): array;
}

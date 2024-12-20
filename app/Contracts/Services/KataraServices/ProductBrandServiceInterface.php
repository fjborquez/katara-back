<?php

namespace App\Contracts\Services\KataraServices;

interface ProductBrandServiceInterface
{
    public function list(): array;

    public function create(array $data = []): array;
}

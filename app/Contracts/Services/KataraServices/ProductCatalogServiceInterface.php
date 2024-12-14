<?php

namespace App\Contracts\Services\KataraServices;

interface ProductCatalogServiceInterface
{
    public function list(): array;

    public function create(array $data = []): array;
}

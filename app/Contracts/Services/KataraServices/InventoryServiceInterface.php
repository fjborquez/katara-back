<?php

namespace App\Contracts\Services\KataraServices;

interface InventoryServiceInterface
{
    public function create(array $data = []): array;

    public function list(): array;
}

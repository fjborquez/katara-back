<?php

namespace App\Contracts\Services\KataraServices;

interface InventoryServiceInterface
{
    public function create(array $data = []): array;

    public function list(): array;

    public function discard(int $id): array;

    public function update(int $id, array $data = []): array;
}

<?php

namespace App\Contracts\Services\KataraServices;

interface UserHouseServiceInterface
{
    public function list(int $userId): array;

    public function create(int $userId, array $data): array;

    public function update(int $userId, array $data): array;

    public function enable(int $houseId): array;

    public function disable(int $houseId): array;
}

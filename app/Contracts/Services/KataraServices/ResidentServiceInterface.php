<?php

namespace App\Contracts\Services\KataraServices;

interface ResidentServiceInterface
{
    public function get(int $userId, int $houseId, int $residentId): array;

    public function list(int $houseId): array;

    public function create(int $addingUserId, int $houseId, array $residentData): array;

    public function update(int $personId, array $data): array;
}

<?php

namespace App\Contracts\Services\KataraServices;

interface UserServiceInterface
{
    public function get(int $id): array;

    public function list(): array;

    public function create(array $data = []): array;

    public function update(int $id, array $data): array;

    public function enable(int $id): array;

    public function disable(int $id): array;
}

<?php

namespace App\Contracts\Services\UserUpdateService;

interface UserUpdateServiceInterface
{
    public function update(int $id, array $data): void;
}

<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface UserServiceInterface
{
    public function create(array $data = []): Response;

    public function list(): Response;

    public function update(int $id, array $data = []): Response;

    public function enable(int $id): Response;

    public function disable(int $id): Response;

    public function get(int $id): Response;
}

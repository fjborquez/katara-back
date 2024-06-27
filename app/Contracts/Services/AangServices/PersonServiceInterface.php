<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface PersonServiceInterface
{
    public function create(array $data = []): Response;
    public function delete(int $id): Response;
    public function update(int $id, array $data = []): Response;
    public function get(int $id): Response;
}

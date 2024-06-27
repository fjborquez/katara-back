<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface NutritionalProfileServiceInterface
{
    public function create(int $id, array $data = []): Response;
    public function get(int $id): Response;
    public function update(int $id, array $data = []): Response;
}

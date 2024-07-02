<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface HouseServiceInterface
{
    public function create(array $data = []): Response;

    public function update(int $houseId, array $data = []): Response;

    public function get(int $houseId): Response;

    public function enable(int $houseId): Response;

    public function disable(int $houseId): Response;
}

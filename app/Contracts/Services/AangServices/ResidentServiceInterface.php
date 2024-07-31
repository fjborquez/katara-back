<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface ResidentServiceInterface
{
    public function list(int $houseId): Response;

    public function delete(int $houseId, int $residentId): Response;
}

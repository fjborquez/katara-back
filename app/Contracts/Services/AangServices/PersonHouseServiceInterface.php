<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface PersonHouseServiceInterface
{
    public function create(int $personId, array $houses): Response;
    public function update(int $personId, array $houses): Response;
}

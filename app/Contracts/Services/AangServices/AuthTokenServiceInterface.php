<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface AuthTokenServiceInterface
{
    public function create(array $data = []): Response;
}

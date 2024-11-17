<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface CityServiceInterface
{
    public function list(): Response;
}

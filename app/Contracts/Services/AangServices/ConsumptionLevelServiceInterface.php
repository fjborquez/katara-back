<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface ConsumptionLevelServiceInterface
{
    public function list(): Response;
}

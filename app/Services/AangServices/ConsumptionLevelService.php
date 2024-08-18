<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\ConsumptionLevelServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ConsumptionLevelService implements ConsumptionLevelServiceInterface
{
    public function list(): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('aang.url').'/consumption-level');
    }
}

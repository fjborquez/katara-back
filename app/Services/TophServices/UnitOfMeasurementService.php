<?php

namespace App\Services\TophServices;

use App\Contracts\Services\TophServices\UnitOfMeasurementServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class UnitOfMeasurementService implements UnitOfMeasurementServiceInterface
{
    public function get(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('toph.url').'/unit-of-measurement/'.$id);
    }
}

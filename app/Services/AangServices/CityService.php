<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\CityServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CityService implements CityServiceInterface
{
    public function list(): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('aang.url').'/city');
    }
}

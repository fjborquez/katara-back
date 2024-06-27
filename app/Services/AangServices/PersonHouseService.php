<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\PersonHouseServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PersonHouseService implements PersonHouseServiceInterface
{
    public function create(int $personId, array $houses): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url') . '/person/' . $personId . '/house', $houses);
    }

    public function update(int $personId, array $houses): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url') . '/person/' . $personId . '/house', $houses);
    }
}

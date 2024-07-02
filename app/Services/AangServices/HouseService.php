<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\HouseServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class HouseService implements HouseServiceInterface
{
    public function create(array $data = []): Response
    {

        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url').'/house', $data);
    }

    public function update(int $houseId, array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url').'/house/'.$houseId, $data);
    }

    public function get(int $houseId): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('aang.url').'/house/'.$houseId);
    }

    public function enable(int $houseId): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url').'/house/'.$houseId.'/enable');
    }

    public function disable(int $houseId): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url').'/house/'.$houseId.'/disable');
    }
}

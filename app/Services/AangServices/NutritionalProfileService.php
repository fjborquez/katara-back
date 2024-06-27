<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\NutritionalProfileServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class NutritionalProfileService implements NutritionalProfileServiceInterface
{
    public function create(int $id, array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url') . '/person/' . $id . '/nutritional-profile', $data);
    }

    public function get(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('aang.url') . '/person/' . $id . '/nutritional-profile');
    }

    public function update(int $id, array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url') . '/person/'. $id . '/nutritional-profile', $data);
    }
}

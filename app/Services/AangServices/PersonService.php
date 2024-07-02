<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\PersonServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PersonService implements PersonServiceInterface
{
    public function create(array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url').'/person', $data);
    }

    public function delete(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->delete(Config::get('aang.url').'/person/'.$id);
    }

    public function update(int $id, array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url').'/person/'.$id, $data);
    }

    public function get(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('aang.url').'/person/'.$id);
    }
}

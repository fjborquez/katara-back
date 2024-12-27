<?php

namespace App\Services\AzulaServices;

use App\Contracts\Services\AzulaServices\InventoryServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class InventoryService implements InventoryServiceInterface
{
    public function create(array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('azula.url').'/inventory', $data);
    }

    public function list(array $params = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('azula.url').'/inventory', $params);
    }

    public function update(int $id, array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('azula.url').'/inventory/'.$id, $data);
    }

    public function discard(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('azula.url').'/inventory/'.$id.'/discard');
    }

    public function get(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('azula.url').'/inventory/'.$id);
    }
}

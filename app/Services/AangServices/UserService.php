<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\UserServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class UserService implements UserServiceInterface
{
    public function create(array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url').'/user', $data);
    }

    public function list(): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('aang.url').'/user');
    }

    public function update(int $id, array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url').'/user/'.$id, $data);
    }

    public function enable(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url').'/user/'.$id.'/enable');
    }

    public function disable(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->put(Config::get('aang.url').'/user/'.$id.'/disable');
    }

    public function get(int $id): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->get(Config::get('aang.url').'/user/'.$id);
    }
}

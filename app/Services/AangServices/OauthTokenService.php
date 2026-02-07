<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\OauthTokenServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class OauthTokenService implements OauthTokenServiceInterface
{
    public function create(array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url').'/oauth/token', $data);
    }
}

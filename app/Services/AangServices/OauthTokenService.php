<?php

namespace App\Services\AangServices;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Contracts\Services\AangServices\OauthTokenServiceInterface;

class OauthTokenService implements OauthTokenServiceInterface
{
    public function create(array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url').'/oauth/token', $data);
    }
}

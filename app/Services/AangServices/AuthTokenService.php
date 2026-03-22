<?php

namespace App\Services\AangServices;

use App\Contracts\Services\AangServices\AuthTokenServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class AuthTokenService implements AuthTokenServiceInterface
{
    public function create(array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->post(Config::get('aang.url').'/auth/token', $data);
    }

    public function check(string $token): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)->withHeader('Authorization', 'Bearer '.$token)
            ->get(Config::get('aang.url').'/auth/token/check');
    }
}

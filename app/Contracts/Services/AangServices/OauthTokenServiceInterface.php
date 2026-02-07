<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface OauthTokenServiceInterface
{
    public function create(array $data = []): Response;
}

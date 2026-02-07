<?php

namespace App\Contracts\Services\KataraServices;

interface OauthTokenServiceInterface
{
    public function create(array $data = []): array;
}

<?php

namespace App\Contracts\Services\UserGetService;

interface UserGetServiceInterface
{
    public function get(int $id): object;
}

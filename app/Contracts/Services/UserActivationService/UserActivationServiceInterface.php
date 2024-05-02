<?php

namespace App\Contracts\Services\UserActivationService;

interface UserActivationServiceInterface
{
    public function enable(int $id): void;

    public function disable(int $id): void;
}

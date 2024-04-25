<?php

namespace App\Contracts\Services\UserRegistrationService;

interface UserRegistrationServiceInterface {
    public function register(array $data = []): object;
}

<?php

namespace App\Services\UserListService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Contracts\Services\UserListService\UserListServiceInterface;

class UserListService implements UserListServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService){}

    public function get(): array
    {
        return $this->userExternalService->userList();
    }
}

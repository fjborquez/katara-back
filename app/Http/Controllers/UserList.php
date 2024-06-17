<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserListService\UserListServiceInterface;

class UserList extends Controller
{
    public function __construct(private readonly UserListServiceInterface $userListService)
    {
    }

    public function getList()
    {
        return $this->userListService->get();
    }
}

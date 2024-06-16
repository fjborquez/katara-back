<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserGetService\UserGetServiceInterface;

class UserGet extends Controller
{
    public function __construct(
        private readonly UserGetServiceInterface $userGetService
    ) { }

    public function getUser(int $id)
    {
        return $this->userGetService->get($id);
    }
}

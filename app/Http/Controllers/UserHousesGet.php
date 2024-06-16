<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserHousesGetService\UserHousesGetServiceInterface;
use Exception;

class UserHousesGet extends Controller
{
    public function __construct(
        private readonly UserHousesGetServiceInterface $userHousesGetService
    )
    {}

    public function getAll(int $userId): array
    {
        return $this->userHousesGetService->getAll($userId);
    }
}

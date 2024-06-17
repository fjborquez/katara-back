<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserActivationService\UserActivationServiceInterface;

class UserActivation extends Controller
{
    public function __construct(private readonly UserActivationServiceInterface $userActivationService)
    {
    }

    public function enable(int $id)
    {
        $this->userActivationService->enable($id);
    }

    public function disable(int $id)
    {
        $this->userActivationService->disable($id);
    }
}

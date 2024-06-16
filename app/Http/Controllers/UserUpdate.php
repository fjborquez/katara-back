<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserUpdateService\UserUpdateServiceInterface;
use Illuminate\Http\Request;

class UserUpdate extends Controller
{
    public function __construct(private readonly UserUpdateServiceInterface $userUpdateService) {}

    public function update(int $id, Request $request)
    {
        $this->userUpdateService->update($id, $request->input());
    }
}

<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserHouseUpdateService\UserHouseUpdateServiceInterface;
use Exception;
use Illuminate\Http\Request;

class UserHouseUpdate extends Controller
{
    public function __construct(
        private readonly UserHouseUpdateServiceInterface $userHouseUpdateService
    ) {}

    public function update(int $userId, Request $request)
    {
        try {
            $this->userHouseUpdateService->update($userId, $request->all());
        } catch (Exception $exception) {
            $response = $exception->getMessage();
            $message = trim(explode(',', $response)[0], "\"");
            throw new Exception($message);
        }

        return response()->json(['message' => 'House updated'], 201);
    }
}

<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserHouseUpdateService\UserHouseUpdateServiceInterface;
use App\Http\Requests\UserHouseRequest;
use Exception;

class UserHouseUpdate extends Controller
{
    public function __construct(
        private readonly UserHouseUpdateServiceInterface $userHouseUpdateService
    ) {}

    public function update(int $userId, UserHouseRequest $request)
    {
        try {
            $this->userHouseUpdateService->update($userId, $request->all());
        } catch (Exception $exception) {
            $response = $exception->getMessage();

            if (str_contains($response, ','))
            {
                $message = trim(explode(',', $response)[0], "\"");
            } else {
                $message = $response;
            }

            throw new Exception($message);
        }

        return response()->json(['message' => 'House updated'], 201);
    }
}

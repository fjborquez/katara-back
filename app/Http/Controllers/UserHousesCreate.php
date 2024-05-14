<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserHouseCreateService\UserHouseCreateServiceInterface;
use App\Http\Requests\UserHouseRequest;
use Exception;

class UserHousesCreate extends Controller
{
    public function __construct(private readonly UserHouseCreateServiceInterface $userHouseCreateService)
    {}

    public function create(int $userId, UserHouseRequest $request)
    {
        try {
            $this->userHouseCreateService->create($userId, $request->all());
        }catch (Exception $exception) {
            $response = $exception->getMessage();
            $message = trim(explode(',', $response)[0], "\"");
            throw new Exception($message);
        }

        return response()->json(['message' => 'House added'], 201);
    }
}

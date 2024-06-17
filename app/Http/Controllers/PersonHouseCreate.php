<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PersonHouseCreateService\PersonHouseCreateServiceInterface;
use App\Exceptions\AangResponseException;
use App\Http\Requests\PersonHouseRequest;

class PersonHouseCreate extends Controller
{
    public function __construct(
        private readonly PersonHouseCreateServiceInterface $personHouseCreateService,
    ) {
    }

    public function create(int $userId, int $houseId, PersonHouseRequest $request)
    {
        try {
            return $this->personHouseCreateService->create($userId, $houseId, $request->all());
        } catch (AangResponseException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

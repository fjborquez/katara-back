<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PersonHouseUpdateService\PersonHouseUpdateServiceInterface;
use App\Http\Requests\PersonHouseRequest;
use Exception;

class PersonHouseUpdate extends Controller
{
    public function __construct(
        private readonly PersonHouseUpdateServiceInterface $personHouseUpdateService
    ) {
    }

    public function update(int $personId, PersonHouseRequest $request)
    {
        try {
            $this->personHouseUpdateService->update($personId, $request->all());

            return response()->json(['message' => 'Resident updated'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

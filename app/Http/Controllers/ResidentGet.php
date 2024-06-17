<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ResidentGetService\ResidentGetServiceInterface;
use Exception;

class ResidentGet extends Controller
{
    public function __construct(
        private readonly ResidentGetServiceInterface $residentGetService
    ) {
    }

    public function get(int $userId, int $houseId, int $residentId)
    {
        try {
            return $this->residentGetService->get($userId, $houseId, $residentId);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

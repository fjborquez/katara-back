<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ResidentListService\ResidentListServiceInterface;
use Exception;

class ResidentList extends Controller
{
    public function __construct(
        private readonly ResidentListServiceInterface $residentListService
    ) {

    }

    public function getList(int $houseId)
    {
        try {
            return $this->residentListService->get($houseId);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Exceptions\UnexpectedErrorException;
use App\Services\KataraServices\InventoryService;
use Symfony\Component\HttpFoundation\Response;

class InventoryHouseController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function list(int $userId, int $houseId)
    {
        try {
            $response = $this->inventoryService->list([
                'house_id' => $houseId
            ]);

            return response()->json(['message' => $response['message']], Response::HTTP_OK);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

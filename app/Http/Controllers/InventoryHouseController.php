<?php

namespace App\Http\Controllers;

use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\InventoryHouseRequest;
use App\Services\KataraServices\InventoryService;
use Symfony\Component\HttpFoundation\Response;

class InventoryHouseController extends Controller
{
    private $fields = ['id', 'house_id', 'house_description', 'quantity', 'uom_id', 'uom_abbreviation', 'purchase_date', 'expiration_date',
        'catalog_id', 'catalog_description', 'brand_id', 'brand_name', 'category_id', 'category_name'];

    public function __construct(private readonly InventoryService $inventoryService) {}

    public function list(int $userId, int $houseId)
    {
        try {
            $response = $this->inventoryService->list([
                'house_id' => $houseId,
            ]);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(int $userId, int $houseId, InventoryHouseRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->inventoryService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function discard(int $userId, int $houseId, int $inventoryId)
    {
        try {
            $response = $this->inventoryService->discard($inventoryId);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $userId, int $houseId, int $inventoryId, InventoryHouseRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->inventoryService->update($inventoryId, $validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get(int $userId, int $houseId, int $inventoryId)
    {
        try {
            $response = $this->inventoryService->get($inventoryId);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

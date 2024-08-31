<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\InventoryServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\InventoryRequest;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends Controller
{
    private $fields = ['house_id', 'quantity', 'uom_id', 'uom_abbreviation', 'purchase_date', 'expiration_date',
        'catalog_id', 'catalog_description', 'brand_id', 'brand_name', 'category_id', 'category_name'];

    public function __construct(
        private readonly InventoryServiceInterface $inventoryService
    ) {}

    public function store(InventoryRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->inventoryService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

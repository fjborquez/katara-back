<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductCatalogServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\ProductCatalogRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductCatalogController extends Controller
{
    private $fields = ['category_id', 'brand_id', 'type_id', 'presentation_id'];

    public function __construct(
        private readonly ProductCatalogServiceInterface $productCatalogService
    ) {}

    public function list()
    {
        try {
            $response = $this->productCatalogService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ProductCatalogRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->productCatalogService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

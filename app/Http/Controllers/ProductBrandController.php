<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductBrandServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\ProductBrandRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductBrandController extends Controller
{
    private $fields = ['name', 'description'];

    public function __construct(
        private readonly ProductBrandServiceInterface $productBrandService
    ) {}

    public function list()
    {
        try {
            $response = $this->productBrandService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ProductBrandRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->productBrandService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

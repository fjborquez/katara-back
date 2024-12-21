<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductTypeServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\ProductTypeRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductTypeController extends Controller
{
    private $fields = ["description"];

    public function __construct(
        private readonly ProductTypeServiceInterface $productTypeService
    ) {}

    public function list()
    {
        try {
            $response = $this->productTypeService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ProductTypeRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->productTypeService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

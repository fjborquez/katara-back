<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductCategoryServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\ProductCategoryRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductCategoryController extends Controller
{
    private $fields = ['name'];

    public function __construct(
        private readonly ProductCategoryServiceInterface $productCategoryService
    ) {}

    public function list()
    {
        try {
            $response = $this->productCategoryService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ProductCategoryRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->productCategoryService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

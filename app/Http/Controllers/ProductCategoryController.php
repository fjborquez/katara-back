<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductCategoryServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductCategoryController extends Controller
{
    public function __construct(
        private readonly ProductCategoryServiceInterface $productCategoryService
    ) {}

    public function list()
    {
        try {
            $response = $this->productCategoryService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

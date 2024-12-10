<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductCatalogServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductCatalogController extends Controller
{
    public function __construct(
        private readonly ProductCatalogServiceInterface $productCatalogService
    ) {}

    public function list()
    {
        try {
            $response = $this->productCatalogService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

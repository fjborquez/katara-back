<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductBrandServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductBrandController extends Controller
{
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
}

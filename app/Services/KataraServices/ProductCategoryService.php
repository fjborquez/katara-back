<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\ProductCategoryServiceInterface;
use App\Contracts\Services\ZukoServices\ProductCategoryServiceInterface as ZukoProductCategoryServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductCategoryService implements ProductCategoryServiceInterface
{
    public function __construct(
        private readonly ZukoProductCategoryServiceInterface $zukoProductCategoryService
    ) {}

    public function list(): array {
        $productCategoryListResponse = $this->zukoProductCategoryService->list();

        if ($productCategoryListResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $productCategoryListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\ProductBrandServiceInterface;
use App\Contracts\Services\ZukoServices\ProductBrandServiceInterface as ZukoProductBrandServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductBrandService implements ProductBrandServiceInterface
{
    public function __construct(
        private readonly ZukoProductBrandServiceInterface $zukoProductBrandService
    ) {}

    public function list(): array
    {
        $productBrandListResponse = $this->zukoProductBrandService->list();

        if ($productBrandListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $productBrandListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\ProductTypeServiceInterface;
use App\Contracts\Services\ZukoServices\ProductTypeServiceInterface as ZukoProductTypeServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductTypeService implements ProductTypeServiceInterface
{
    public function __construct(
        private readonly ZukoProductTypeServiceInterface $zukoProductTypeService
    ) {}

    public function list(): array
    {
        $productTypeListResponse = $this->zukoProductTypeService->list();

        if ($productTypeListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $productTypeListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

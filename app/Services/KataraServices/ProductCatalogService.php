<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\ProductCatalogServiceInterface;
use App\Contracts\Services\ZukoServices\ProductCatalogServiceInterface as ZukoProductCatalogServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductCatalogService implements ProductCatalogServiceInterface
{
    public function __construct(
        private readonly ZukoProductCatalogServiceInterface $zukoProductCatalogService
    ) {}

    public function list(): array
    {
        $productCatalogListResponse = $this->zukoProductCatalogService->list();

        if ($productCatalogListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $productCatalogListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }

    public function create(array $data = []): array
    {
        $createProductCatalogResponse = $this->zukoProductCatalogService->create($data);

        if ($createProductCatalogResponse->unprocessableEntity()) {
            $message = $createProductCatalogResponse->json('message');
            $code = $createProductCatalogResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($createProductCatalogResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => 'Product catalog created successfully',
            'code' => Response::HTTP_CREATED,
            'headers' => [
                'Location' => $createProductCatalogResponse->header('Location'),
            ],
        ];
    }
}

<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\ProductPresentationServiceInterface;
use App\Contracts\Services\ZukoServices\ProductPresentationServiceInterface as ZukoProductPresentationServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ProductPresentationService implements ProductPresentationServiceInterface
{
    public function __construct(
        private readonly ZukoProductPresentationServiceInterface $zukoProductPresentationService
    ) {}

    public function list(): array
    {
        $productPresentationListResponse = $this->zukoProductPresentationService->list();

        if ($productPresentationListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $productPresentationListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

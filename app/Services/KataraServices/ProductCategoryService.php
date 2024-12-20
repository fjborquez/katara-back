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

    public function list(): array
    {
        $params = [
            'sort' => 'name',
        ];
        $productCategoryListResponse = $this->zukoProductCategoryService->list($params);

        if ($productCategoryListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $productCategoryListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }

    public function create(array $data = []): array
    {
        $productCategoryCreateResponse = $this->zukoProductCategoryService->create($data);

        if ($productCategoryCreateResponse->unprocessableEntity()) {
            $message = $productCategoryCreateResponse->json('message');
            $code = $productCategoryCreateResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($productCategoryCreateResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => 'Product category created successfully',
            'code' => Response::HTTP_CREATED,
        ];
    }
}

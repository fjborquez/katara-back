<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\HouseServiceInterface as AangHouseServiceInterface;
use App\Contracts\Services\AzulaServices\InventoryServiceInterface as AzulaInventoryServiceInterface;
use App\Contracts\Services\KataraServices\InventoryServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class InventoryService implements InventoryServiceInterface
{
    public function __construct(
        private readonly AangHouseServiceInterface $aangHouseService,
        private readonly AzulaInventoryServiceInterface $azulaInventoryService,
    ) {}

    public function create(array $data = []): array
    {
        $houseGetResponse = $this->aangHouseService->get($data['house_id']);

        if ($houseGetResponse->notFound()) {
            $message = 'House not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($houseGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $house = $houseGetResponse->json();

        if (! $house['is_active']) {
            $message = 'House is not active';
            $code = Response::HTTP_CONFLICT;

            return [
                'message' => $message,
                'code' => $code,
            ];
        }

        // TODO: filtrar inventarios expirados y descartados
        $inventoryParams = [
            "filter[house_id]" => $data['house_id'],
        ];

        $inventoryGetResponse = $this->azulaInventoryService->list($inventoryParams);

        if ($inventoryGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $inventory = $inventoryGetResponse->json();
        $newInventoryData = $data;
        $newInventoryData['house_description'] = $house['description'];

        if (empty($inventory)) {
            $inventoryCreateResponse = $this->azulaInventoryService->create($newInventoryData);

            if ($inventoryCreateResponse->unprocessableEntity()) {
                $message = $inventoryCreateResponse->json('message');
                $code = Response::HTTP_UNPROCESSABLE_ENTITY;

                return [
                    'message' => $message,
                    'code' => $code,
                ];
            } elseif ($inventoryCreateResponse->failed()) {
                throw new UnexpectedErrorException;
            }
        } else {
            // TODO: actualizar inventario existente
        }

        return [
            'message' => 'Inventory created successfully',
            'code' => Response::HTTP_CREATED,
        ];
    }
}

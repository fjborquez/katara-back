<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\HouseServiceInterface as AangHouseServiceInterface;
use App\Contracts\Services\AzulaServices\InventoryServiceInterface as AzulaInventoryServiceInterface;
use App\Contracts\Services\KataraServices\InventoryServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Arr;
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
            'filter[house_id]' => $data['house_id'],
        ];

        $inventoryGetResponse = $this->azulaInventoryService->list($inventoryParams);

        if ($inventoryGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $inventory = $inventoryGetResponse->json();
        $newDetailData = $data;
        $newDetailData['house_description'] = $house['description'];

        if (empty($inventory)) {
            $inventoryCreateResponse = $this->azulaInventoryService->create($newDetailData);

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
            // actualizar inventario existente
            $existingDetailsByCatalog = array_filter($inventory, function ($inventoryDetail) use ($newDetailData) {
                return $inventoryDetail['catalog_id'] === (int) $newDetailData['catalog_id'];
            });

            if (empty($existingDetailsByCatalog)) {
                // Si el detalle de inventario no estaba de antes
                $inventoryCreateResponse = $this->azulaInventoryService->create($newDetailData);

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
                // El detalle de inventario ya estaba de antes
                $existingDetailByUomAndExpirationDate = Arr::first($existingDetailsByCatalog, function ($inventoryDetail) use ($newDetailData) {
                    return $inventoryDetail['uom_id'] === (int) $newDetailData['uom_id']
                        && $inventoryDetail['expiration_date'] === $newDetailData['expiration_date'];
                });

                if ($existingDetailByUomAndExpirationDate) {
                    // Si tienen la misma UOM y la misma fecha de expiración: sumar y actualizar
                    $existingDetailByUomAndExpirationDate['quantity'] += $newDetailData['quantity'];
                    $inventoryUpdateResponse = $this->azulaInventoryService->update($existingDetailByUomAndExpirationDate['id'], $existingDetailByUomAndExpirationDate);

                    if ($inventoryUpdateResponse->unprocessableEntity()) {
                        $message = $inventoryUpdateResponse->json('message');
                        $code = Response::HTTP_UNPROCESSABLE_ENTITY;

                        return [
                            'message' => $message,
                            'code' => $code,
                        ];
                    } elseif ($inventoryUpdateResponse->failed()) {
                        throw new UnexpectedErrorException;
                    }
                } else {
                    $existingDetailByUom = Arr::first($existingDetailsByCatalog, function ($inventoryDetail) use ($newDetailData) {
                        return $inventoryDetail['uom_id'] === (int) $newDetailData['uom_id'];
                    });

                    $existingDetailByExpirationDate = Arr::first($existingDetailsByCatalog, function ($inventoryDetail) use ($newDetailData) {
                        return $inventoryDetail['expiration_date'] === $newDetailData['expiration_date'];
                    });

                    if ($existingDetailByUom) {
                        // Si tienen misma UOM pero distinta fecha de expiración: crear nuevo detalle
                        $inventoryCreateResponse = $this->azulaInventoryService->create($newDetailData);

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
                    } else if ($existingDetailByExpirationDate) {
                        // Si tienen distinta UOM pero misma fecha de expiración: convertir UOM y sumar
                    } else {
                        // Si tienen distinta UOM y distinta fecha de expiración: crear nuevo detalle
                        $inventoryCreateResponse = $this->azulaInventoryService->create($newDetailData);

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
                    }
                }
            }
        }

        return [
            'message' => 'Inventory created successfully',
            'code' => Response::HTTP_CREATED,
        ];
    }
}

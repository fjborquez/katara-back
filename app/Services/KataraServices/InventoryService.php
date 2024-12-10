<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\HouseServiceInterface as AangHouseServiceInterface;
use App\Contracts\Services\AzulaServices\InventoryServiceInterface as AzulaInventoryServiceInterface;
use App\Contracts\Services\KataraServices\InventoryServiceInterface;
use App\Contracts\Services\TophServices\UnitOfMeasurementServiceInterface as TophUnitOfMeasurementServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class InventoryService implements InventoryServiceInterface
{
    public function __construct(
        private readonly AangHouseServiceInterface $aangHouseService,
        private readonly AzulaInventoryServiceInterface $azulaInventoryService,
        private readonly TophUnitOfMeasurementServiceInterface $tophUnitOfMeasurementService,
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

        if (! array_key_exists('expiration_date', $newDetailData)) {
            $newDetailData['expiration_date'] = null;
        }

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
                    } elseif ($existingDetailByExpirationDate) {
                        // Si tienen distinta UOM pero misma fecha de expiración: convertir UOM y sumar
                        $newUomGetResponse = $this->tophUnitOfMeasurementService->get($newDetailData['uom_id']);
                        $oldUomGetResponse = $this->tophUnitOfMeasurementService->get($existingDetailByExpirationDate['uom_id']);

                        if ($newUomGetResponse->notFound() || $oldUomGetResponse->notFound()) {
                            $message = 'Unit of measurement not found';
                            $code = Response::HTTP_NOT_FOUND;

                            return [
                                'message' => $message,
                                'code' => $code,
                            ];
                        } elseif ($newUomGetResponse->failed() || $oldUomGetResponse->failed()) {
                            throw new UnexpectedErrorException;
                        }

                        $newUom = $newUomGetResponse->json();
                        $oldUom = $oldUomGetResponse->json();

                        $newFromConversion = Arr::first($newUom['from_conversions'], function ($fromConversion) use ($existingDetailByExpirationDate) {
                            return $fromConversion['to_unit_id'] === (int) $existingDetailByExpirationDate['uom_id'];
                        });

                        $oldFromConversion = Arr::first($oldUom['from_conversions'], function ($fromConversion) use ($newDetailData) {
                            return $fromConversion['to_unit_id'] === (int) $newDetailData['uom_id'];
                        });

                        if ($newFromConversion == null && $oldFromConversion == null) {
                            throw new UnexpectedErrorException;
                        }

                        if ($newFromConversion == null || $oldFromConversion == null) {
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
                            } else {
                                return [
                                    'message' => 'Inventory created successfully',
                                    'code' => Response::HTTP_CREATED,
                                ];
                            }
                        }

                        if ($newFromConversion['factor'] >= $oldFromConversion['factor']) {
                            $quantity = ($existingDetailByExpirationDate['quantity'] * $oldFromConversion['factor']) + $newDetailData['quantity'];
                            $uomAbbreviation = $newDetailData['uom_abbreviation'];
                            $uomId = $newDetailData['uom_id'];
                        } else {
                            $quantity = ($newDetailData['quantity'] * $newFromConversion['factor']) + $existingDetailByExpirationDate['quantity'];
                            $uomAbbreviation = $existingDetailByExpirationDate['uom_abbreviation'];
                            $uomId = $existingDetailByExpirationDate['uom_id'];
                        }

                        $newDetailData['quantity'] = $quantity;
                        $newDetailData['uom_abbreviation'] = $uomAbbreviation;
                        $newDetailData['uom_id'] = $uomId;
                        $inventoryCreateResponse = $this->azulaInventoryService->update($existingDetailByExpirationDate['id'], $newDetailData);

                        if ($inventoryCreateResponse->unprocessableEntity()) {
                            $message = $inventoryCreateResponse->json('message');
                            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

                            return [
                                'message' => $message,
                                'code' => $code,
                            ];
                        } elseif ($inventoryCreateResponse->failed()) {
                            throw new UnexpectedErrorException;
                        } else {
                            return [
                                'message' => 'Inventory created successfully',
                                'code' => Response::HTTP_CREATED,
                            ];
                        }
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

    public function list(array $data = []): array
    {
        $params = [];

        if (array_key_exists('house_id', $data)) {
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

            $params['filter[house_id]'] = $data['house_id'];
            $params['filter[has_active_product_status]'] = true;
            $params['include'] = 'productStatus';
        }

        $inventoryListResponse = $this->azulaInventoryService->list($params);

        if ($inventoryListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $inventoryListCollection = $inventoryListResponse->collect();
        $sortedInventoryListCollection = $inventoryListCollection->map(function ($item, int $key) {
            $item['expiration_date'] = $item['expiration_date'] != null ? new Carbon($item['expiration_date']) : null;
            $item['purchase_date'] = $item['purchase_date'] != null ? new Carbon($item['purchase_date']) : null;

            return $item;
        })->sort(function ($a, $b) {
            if ($a['purchase_date'] < $b['purchase_date']) {
                return -1;
            }
            if ($a['purchase_date'] > $b['purchase_date']) {
                return 1;
            }

            $aWeight = 0;
            $bWeight = 0;

            if (array_key_exists('product_status', $a)) {
                $aStatus = Arr::first($a['product_status'], function ($productStatus) {
                    return $productStatus['pivot']['is_active'];
                });

                $aWeight = sortWeight($aStatus);
            }

            if (array_key_exists('product_status', $b)) {
                $bStatus = Arr::first($b['product_status'], function ($productStatus) {
                    return $productStatus['pivot']['is_active'];
                });

                $bWeight = sortWeight($bStatus);
            }

            if ($aWeight < $bWeight) {
                return -1;
            }
            if ($aWeight > $bWeight) {
                return 1;
            }

            if ($a['expiration_date'] < $b['expiration_date']) {
                return -1;
            }
            if ($a['expiration_date'] > $b['expiration_date']) {
                return 1;
            }

            if ($a['catalog_description'] < $b['catalog_description']) {
                return -1;
            }
            if ($a['catalog_description'] > $b['catalog_description']) {
                return 1;
            }

            return 0;
        });

        return [
            'message' => array_values($sortedInventoryListCollection->toArray()),
            'code' => Response::HTTP_OK,
        ];
    }
}

function sortWeight($status)
{
    if ($status['id'] == 2) {
        return 1;
    }
    if ($status['id'] == 1) {
        return 2;
    }
    if ($status['id'] == 6) {
        return 3;
    }
    if ($status['id'] == 3) {
        return 4;
    }

    return 0;
}

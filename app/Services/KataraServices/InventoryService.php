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
        $house = $this->searchActiveHouse($data['house_id']);

        if ($this->isError($house)) {
            return $house;
        }

        $inventory = $this->searchInventoryByParams([
            'house_id' => $data['house_id'],
        ]);
        $newDetailData = $data;
        $newDetailData['house_description'] = $house['description'];
        $newDetailData['expiration_date'] = $this->getExpirationDateOrNull($newDetailData);

        if (empty($inventory)) {
            $createdInventory = $this->createInventoryDetail($newDetailData);

            if ($this->isError($createdInventory)) {
                return $createdInventory;
            }
        } else {
            // actualizar inventario existente
            // Obtener las diferentes coincidencias del item en el inventario
            $existingDetailsByCatalog = $this->searchItem($inventory, $newDetailData);

            if (empty($existingDetailsByCatalog)) {
                // Si el detalle de inventario no estaba de antes
                $createdInventory = $this->createInventoryDetail($newDetailData);

                if ($this->isError($createdInventory)) {
                    return $createdInventory;
                }
            } else {
                // El detalle de inventario ya estaba de antes
                $existingDetailByUomAndExpirationDate = $this->searchItemDetails($existingDetailsByCatalog, $newDetailData);

                if ($existingDetailByUomAndExpirationDate) {
                    // Si tienen la misma UOM y la misma fecha de expiración: sumar y actualizar
                    $existingDetailByUomAndExpirationDate['quantity'] += $newDetailData['quantity'];
                    $updatedInventory = $this->updateInventory($existingDetailByUomAndExpirationDate['id'], $existingDetailByUomAndExpirationDate);

                    if ($this->isError($updatedInventory)) {
                        return $updatedInventory;
                    }
                } else {
                    $existingDetailByUom = $this->searchItemDetailsByProperty($existingDetailsByCatalog, $newDetailData, 'uom_id');
                    $existingDetailByExpirationDate = $this->searchItemDetailsByProperty($existingDetailsByCatalog, $newDetailData, 'expiration_date');

                    if ($existingDetailByUom) {
                        // Si tienen misma UOM pero distinta fecha de expiración: crear nuevo detalle
                        $createdInventory = $this->createInventoryDetail($newDetailData);

                        if ($this->isError($createdInventory)) {
                            return $createdInventory;
                        }
                    } elseif ($existingDetailByExpirationDate) {
                        // Si tienen distinta UOM pero misma fecha de expiración: convertir UOM y sumar
                        $newFromConversion = $this->searchFromUom($newDetailData['uom_id'], $existingDetailByExpirationDate['uom_id']);
                        $oldFromConversion = $this->searchFromUom($existingDetailByExpirationDate['uom_id'], $newDetailData['uom_id']);

                        if ($newFromConversion != null && $this->isError($newFromConversion)) {
                            return $newFromConversion;
                        }

                        if ($oldFromConversion != null && $this->isError($oldFromConversion)) {
                            return $oldFromConversion;
                        }

                        if ($newFromConversion == null || $oldFromConversion == null) {
                            $createdInventory = $this->createInventoryDetail($newDetailData);

                            if ($this->isError($createdInventory)) {
                                return $createdInventory;
                            } else {
                                return [
                                    'message' => 'Inventory created successfully',
                                    'code' => Response::HTTP_CREATED,
                                ];
                            }
                        }

                        if ($newFromConversion['factor'] >= $oldFromConversion['factor']) {
                            $quantityWithUom = $this->calculateQuantity($existingDetailByExpirationDate, $newDetailData, $oldFromConversion);
                        } else {
                            $quantityWithUom = $this->calculateQuantity($newDetailData, $existingDetailByExpirationDate, $newFromConversion);
                        }

                        $newDetailData['quantity'] = $quantityWithUom['quantity'];
                        $newDetailData['uom_abbreviation'] = $quantityWithUom['uom']['abbreviation'];
                        $newDetailData['uom_id'] = $quantityWithUom['uom']['id'];
                        $updatedInventory = $this->updateInventory($existingDetailByExpirationDate['id'], $newDetailData);

                        if ($this->isError($updatedInventory)) {
                            return $updatedInventory;
                        } else {
                            return [
                                'message' => 'Inventory created successfully',
                                'code' => Response::HTTP_CREATED,
                            ];
                        }
                    } else {
                        // Si tienen distinta UOM y distinta fecha de expiración: crear nuevo detalle
                        $createdInventory = $this->createInventoryDetail($newDetailData);

                        if ($this->isError($createdInventory)) {
                            return $createdInventory;
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

    public function update(int $detailId, array $data = []): array
    {
        $house = $this->searchActiveHouse($data['house_id']);

        if ($this->isError($house)) {
            return $house;
        }

        $detail = $this->searchDetailById($detailId);

        if ($this->isError($detail)) {
            return $detail;
        }

        if (empty($detail)) {
            return [
                'message' => 'Inventory not exists',
                'code' => Response::HTTP_NOT_FOUND,
            ];
        }

        $newDetailData = $data;
        $newDetailData['house_description'] = $house['description'];
        $newDetailData['expiration_date'] = $this->getExpirationDateOrNull($newDetailData);

        $inventory = $this->searchInventoryByParams([
            'house_id' => $data['house_id'],
        ]);
        $existingDetailsByCatalog = $this->searchItem($inventory, $newDetailData);
        $existingDetailsByCatalogAndExclude = array_values($this->inventoryExcludingItem($existingDetailsByCatalog, $newDetailData));

        if (empty($existingDetailsByCatalogAndExclude)) {
            $updatedDetail = $this->updateInventory($detailId, $newDetailData);

            if ($this->isError($updatedDetail)) {
                return $updatedDetail;
            }

            return [
                'message' => 'Inventory updated successfully',
                'code' => Response::HTTP_OK,
            ];
        }

        $existingDetailByUomAndExpirationDate = $this->searchItemDetails($existingDetailsByCatalogAndExclude, $newDetailData);

        if (! empty($existingDetailByUomAndExpirationDate)) {
            $updatedDetail = $this->updateInventory($detailId, $newDetailData);

            if ($this->isError($updatedDetail)) {
                return $updatedDetail;
            }

            return [
                'message' => 'Inventory updated successfully',
                'code' => Response::HTTP_OK,
            ];
        } else {
            $newFromConversion = $this->searchFromUom($newDetailData['uom_id'], $existingDetailsByCatalogAndExclude[0]['uom_id']);
            $oldFromConversion = $this->searchFromUom($existingDetailsByCatalogAndExclude[0]['uom_id'], $newDetailData['uom_id']);

            if ($newFromConversion != null && $this->isError($newFromConversion)) {
                return $newFromConversion;
            }

            if ($oldFromConversion != null && $this->isError($oldFromConversion)) {
                return $oldFromConversion;
            }

            if ($newFromConversion == null || $oldFromConversion == null) {
                $updatedInventory = $this->updateInventory($detailId, $newDetailData);

                if ($this->isError($updatedInventory)) {
                    return $updatedInventory;
                } else {
                    return [
                        'message' => 'Inventory updated successfully',
                        'code' => Response::HTTP_NO_CONTENT,
                    ];
                }
            }

            if ($newFromConversion['factor'] >= $oldFromConversion['factor']) {
                $quantityWithUom = $this->calculateQuantity($existingDetailsByCatalogAndExclude[0], $newDetailData, $oldFromConversion);
            } else {
                $quantityWithUom = $this->calculateQuantity($newDetailData, $existingDetailsByCatalogAndExclude[0], $newFromConversion);
            }

            $newDetailData['quantity'] = $quantityWithUom['quantity'];
            $newDetailData['uom_abbreviation'] = $quantityWithUom['uom']['abbreviation'];
            $newDetailData['uom_id'] = $quantityWithUom['uom']['id'];
            $updatedInventory = $this->updateInventory($existingDetailsByCatalogAndExclude[0]['id'], $newDetailData);

            if ($this->isError($updatedInventory)) {
                return $updatedInventory;
            }

            $discardedInventory = $this->discard($existingDetailsByCatalogAndExclude[]['id']);

            if ($this->isError($discardedInventory) && $discardedInventory['code'] != 200) {
                return $updatedInventory;
            } else {
                return [
                    'message' => 'Inventory created successfully',
                    'code' => Response::HTTP_CREATED,
                ];
            }

        }
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
        })->sort(function ($product, $toCompare) {
            $aWeight = 0;
            $bWeight = 0;

            $aWeight = $this->calculateProductStatusWeight($product);
            $bWeight = $this->calculateProductStatusWeight($toCompare);

            if ($aWeight < $bWeight) {
                return -1;
            }
            if ($aWeight > $bWeight) {
                return 1;
            }

            $expirationDateComparison = $this->productComparation($product, $toCompare, 'expiration_date');
            if ($expirationDateComparison != 0) {
                return $expirationDateComparison;
            }

            $catalogDescriptionComparison = $this->productComparation($product, $toCompare, 'catalog_description');
            if ($catalogDescriptionComparison != 0) {
                return $catalogDescriptionComparison;
            }

            $purchaseDateComparison = $this->productComparation($product, $toCompare, 'purchase_date');
            if ($purchaseDateComparison != 0) {
                return $purchaseDateComparison;
            }

            return 0;
        });

        return [
            'message' => array_values($sortedInventoryListCollection->toArray()),
            'code' => Response::HTTP_OK,
        ];
    }

    public function get(int $inventoryId): array
    {
        $inventoryGetResponse = $this->azulaInventoryService->get($inventoryId);

        if ($inventoryGetResponse->notFound()) {
            $message = 'Inventory not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($inventoryGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $inventory = $inventoryGetResponse->json();

        return [
            'message' => $inventory,
            'code' => Response::HTTP_OK,
        ];
    }

    public function discard(int $id): array
    {
        $inventoryGetResponse = $this->azulaInventoryService->get($id);

        if ($inventoryGetResponse->notFound()) {
            $message = 'Inventory item not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($inventoryGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $inventory = $inventoryGetResponse->json();
        $inventoryPutResponse = $this->azulaInventoryService->discard($id);

        if ($inventoryPutResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => 'Item: '.$inventory['quantity'].' '.$inventory['uom_abbreviation'].' '.$inventory['catalog_description'].' has been discarded',
            'code' => Response::HTTP_OK,
        ];
    }

    private function productStatusSortWeight($status)
    {
        if ($status['id'] == 2) {
            return 1;
        }
        if ($status['id'] == 6) {
            return 2;
        }
        if ($status['id'] == 1) {
            return 3;
        }
        if ($status['id'] == 3) {
            return 4;
        }

        return 0;
    }

    private function extractActiveProductStatus($inventory)
    {
        return Arr::first($inventory['product_status'], function ($productStatus) {
            return $productStatus['pivot']['is_active'];
        });
    }

    private function calculateProductStatusWeight($product)
    {
        if (array_key_exists('product_status', $product)) {
            $aStatus = $this->extractActiveProductStatus($product);

            if ($aStatus == null) {
                return 0;
            }

            return $this->productStatusSortWeight($aStatus);
        }

        return 0;
    }

    private function productComparation($product, $toCompare, $property)
    {
        if ($product[$property] < $toCompare[$property]) {
            return -1;
        }
        if ($product[$property] > $toCompare[$property]) {
            return 1;
        }

        return 0;
    }

    /**
     * Search an item in the complete inventory items,
     * with the different occurencies.
     *
     * @param  mixed  $inventory  Complete inventory items
     * @param  mixed  $item  An item to be searched in the complete inventory items
     * @return array An array with the searched items if they exists, else null
     */
    private function searchItem($inventory, $item)
    {
        return array_filter($inventory, function ($inventoryDetail) use ($item) {
            return $inventoryDetail['catalog_id'] === (int) $item['catalog_id'];
        });
    }

    /**
     * @param  mixed  $items  an array of inventory items of the same product catalog
     * @param  mixed  $item  an item to be searched in the inventory items
     * @return array with the item properties if them exists, else null
     */
    private function searchItemDetails($items, $item)
    {
        return Arr::first($items, function ($detail) use ($item) {
            return $detail['uom_id'] === (int) $item['uom_id']
                && $detail['expiration_date'] === $item['expiration_date'];
        });
    }

    /**
     * @param  mixed  $items  array with the items of a product catalog in the inventory
     * @param  mixed  $item  array with the item properties to be searched in the inventory
     * @param  mixed  $property  property to be searched in the item
     * @return array with the item properties if them exists, else null
     */
    private function searchItemDetailsByProperty($items, $item, $property)
    {
        return Arr::first($items, function ($detail) use ($item, $property) {
            return $detail[$property] === $item[$property];
        });
    }

    /**
     * @param  mixed  $uoms  array with the uoms
     * @param  mixed  $uomId  id to search
     * @return array with the uom properties, if exists
     */
    private function getFromUomById($uoms, $uomId)
    {
        return Arr::first($uoms, function ($fromUom) use ($uomId) {
            return $fromUom['to_unit_id'] === $uomId;
        });
    }

    /**
     * @param  mixed  $baseItemDetail  Item to multiply with factor
     * @param  mixed  $toApplyItemDetail  Item to sum to the multiply with factor result
     * @param  mixed  $uom  the uom to be applied
     * @return array with the new quantity and uom data
     */
    private function calculateQuantity($baseItemDetail, $toApplyItemDetail, $uom)
    {
        $itemQuantity = [];

        $itemQuantity['quantity'] = ($baseItemDetail['quantity'] * $uom['factor']) + $toApplyItemDetail['quantity'];
        $itemQuantity['uom']['abbreviation'] = $toApplyItemDetail['uom_abbreviation'];
        $itemQuantity['uom']['id'] = $toApplyItemDetail['uom_id'];

        return $itemQuantity;
    }

    /**
     * @param  array  $detail  An array with the inventory detail to be created
     * @return array an empty array if the inventory detail was created, or an error
     */
    private function createInventoryDetail(array $detail)
    {
        $inventoryCreateResponse = $this->azulaInventoryService->create($detail);

        if ($inventoryCreateResponse->unprocessableEntity()) {
            return [
                'message' => $inventoryCreateResponse->json('message'),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        } elseif ($inventoryCreateResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [];
    }

    /**
     * @param  array  $response  The http content response
     * @return bool true if the response is an error (message and code), false otherwise
     */
    private function isError(array $response)
    {
        return array_key_exists('message', $response) && array_key_exists('code', $response);
    }

    /**
     * @param  mixed  $houseId  Id of the house to be searched
     * @return array with the house properties
     */
    private function searchHouseById($houseId)
    {
        $houseGetResponse = $this->aangHouseService->get($houseId);

        if ($houseGetResponse->notFound()) {
            return [
                'message' => 'House not found',
                'code' => Response::HTTP_NOT_FOUND,
            ];
        } elseif ($houseGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return $houseGetResponse->json();
    }

    /**
     * @param  array  $params  to filter the inventory
     * @return array with the inventory details
     */
    private function searchInventoryByParams($params = [])
    {
        // TODO: filtrar inventarios expirados y descartados
        $filterParams = $params;
        $filterParams = [
            'filter[house_id]' => $params['house_id'],
        ];

        $inventoryGetResponse = $this->azulaInventoryService->list($filterParams);

        if ($inventoryGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return $inventoryGetResponse->json();
    }

    /**
     * @param  mixed  $uomId  Uom id to be searched
     * @return array the properties of the searched uom
     */
    private function getUom($uomId)
    {
        $uomGetResponse = $this->tophUnitOfMeasurementService->get($uomId);

        if ($uomGetResponse->notFound()) {
            return [
                'message' => 'Unit of measurement not found',
                'code' => Response::HTTP_NOT_FOUND,
            ];
        } elseif ($uomGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return $uomGetResponse->json();
    }

    /**
     * @param  mixed  $detailId  The id of the inventory detail to be modified
     * @param  array  $data  The inventory with changes to update
     * @return array An array with the error message and the error code if the inventory detail was not updated
     *
     * @throws UnexpectedErrorException If the inventory detail was not updated because an unexpected error
     */
    private function updateInventory($detailId, $data = [])
    {
        $inventoryUpdateResponse = $this->azulaInventoryService->update($detailId, $data);

        if ($inventoryUpdateResponse->unprocessableEntity()) {
            return [
                'message' => $inventoryUpdateResponse->json('message'),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        } elseif ($inventoryUpdateResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [];
    }

    /**
     * @param  mixed  $originalUomId  The if of the uom to find the list
     * @param  mixed  $uomToBeSearchId  The id of the uom to be searched in the list
     * @return array The new uom or an error array
     */
    private function searchFromUom($originalUom, $uomToBeSearch)
    {
        $newUom = $this->getUom($originalUom);

        if ($this->isError($newUom)) {
            return $newUom;
        }

        return $this->getFromUomById($newUom['from_conversions'], $uomToBeSearch);
    }

    /**
     * @param  mixed  $detail  Item detail where the expiration date will be searched
     * @return mixed The expiration date if exists, else null|
     */
    private function getExpirationDateOrNull($detail)
    {
        return array_key_exists('expiration_date', $detail) ? $detail['expiration_date'] : null;
    }

    /**
     * @param  mixed  $detailId  The id of the inventory detail to be searched
     * @return array The inventory detail properties
     *
     * @throws UnexpectedErrorException If the inventory detail was not found because an unexpected error
     */
    private function searchDetailById($detailId)
    {
        $detailGetResponse = $this->azulaInventoryService->get($detailId);

        if ($detailGetResponse->notFound()) {
            return [
                'message' => 'Inventory not found',
                'code' => Response::HTTP_NOT_FOUND,
            ];
        } elseif ($detailGetResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return $detailGetResponse->json();
    }

    /**
     * @param  mixed  $houseId  The id of the house to be searched
     * @return array The house properties or an error array
     */
    private function searchActiveHouse($houseId)
    {
        $house = $this->searchHouseById($houseId);

        if ($this->isError($house)) {
            return $house;
        }

        if (! $house['is_active']) {
            return [
                'message' => 'House is not active',
                'code' => Response::HTTP_CONFLICT,
            ];
        }

        return $house;
    }

    /**
     * @param  mixed  $inventory  A list of product details
     * @param  mixed  $detailToExclude  The detail to be excluded from the inventory
     * @return array Inventory without the detail to be excluded
     */
    private function inventoryExcludingItem($inventory, $detailToExclude)
    {
        return array_filter($inventory, function ($item) use ($detailToExclude) {
            return $item['id'] !== $detailToExclude['id'];
        });
    }
}

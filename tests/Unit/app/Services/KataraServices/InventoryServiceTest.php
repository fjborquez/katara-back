<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\HouseService as AangHouseService;
use App\Services\AzulaServices\InventoryService as AzulaInventoryService;
use App\Services\KataraServices\InventoryService;
use App\Services\TophServices\UnitOfMeasurementService as TophUnitOfMeasurementService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    private $aangHouseService;

    private $azulaInventoryService;

    private $tophUnitOfMeasurementService;

    private $inventoryService;

    private $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->aangHouseService = Mockery::mock(AangHouseService::class);
        $this->azulaInventoryService = Mockery::mock(AzulaInventoryService::class);
        $this->tophUnitOfMeasurementService = Mockery::mock(TophUnitOfMeasurementService::class);
        $this->inventoryService = new InventoryService($this->aangHouseService, $this->azulaInventoryService, $this->tophUnitOfMeasurementService);
        $this->data = [
            'house_id' => 1,
            'quantity' => 3,
            'catalog_id' => 1,
            'catalog_description' => 'A PRODUCT DESCRIPTION',
            'category_id' => 1,
            'uom_id' => 1,
            'uom_abbreviation' => 'mg',
            'purchase_date' => '2024-08-31',
            'expiration_date' => '2024-09-30',
            'brand_id' => 1,
            'brand_name' => 'Ideal',
        ];
    }

    public function test_create_inventory_should_create_a_new_inventory_detail_when_there_the_house_inventory_is_empty()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
    }

    public function test_create_inventory_should_return_not_found_when_house_is_not_found()
    {
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_create_inventory_should_throw_an_unexpected_error_exception_when_get_house_failed()
    {
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->inventoryService->create($this->data);
        }, UnexpectedErrorException::class);
    }

    public function test_create_inventory_should_return_conflict_when_house_is_not_active()
    {
        $house = [
            'is_active' => false,
            'description' => 'A HOUSE',
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CONFLICT, $response['code']);
    }

    public function test_create_inventory_should_throw_an_unexpected_error_exception_when_get_inventory_failed()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->inventoryService->create($this->data);
        }, UnexpectedErrorException::class);
    }

    public function test_create_inventory_should_return_unprocessable_entity_when_request_data_no_pass_validation()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_create_inventory_should_throw_an_unexpected_error_exception_when_create_inventory_failed()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->inventoryService->create($this->data);
        }, UnexpectedErrorException::class);
    }

    public function test_create_inventory_should_create_new_detail_when_inventory_does_not_have_this_detail()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [
            [
                'house_id' => 1,
                'quantity' => 3,
                'catalog_id' => 2,
                'catalog_description' => 'A PRODUCT DESCRIPTION 2',
                'category_id' => 1,
                'uom_id' => 1,
                'uom_abbreviation' => 'mg',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-09-30',
                'brand_id' => 1,
                'brand_name' => 'Ideal',
            ],
        ];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
    }

    public function test_create_inventory_should_update_existing_detail_when_inventory_has_this_detail()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [
            [
                'id' => 1,
                'house_id' => 1,
                'quantity' => 3,
                'catalog_id' => 1,
                'catalog_description' => 'A PRODUCT DESCRIPTION',
                'category_id' => 1,
                'uom_id' => 1,
                'uom_abbreviation' => 'mg',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-09-30',
                'brand_id' => 1,
                'brand_name' => 'Ideal',
            ],
        ];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
    }

    public function test_create_inventory_should_return_unprocessable_entity_when_update_form_no_pass_validation()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [
            [
                'id' => 1,
                'house_id' => 1,
                'quantity' => 3,
                'catalog_id' => 1,
                'catalog_description' => 'A PRODUCT DESCRIPTION',
                'category_id' => 1,
                'uom_id' => 1,
                'uom_abbreviation' => 'mg',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-09-30',
                'brand_id' => 1,
                'brand_name' => 'Ideal',
            ],
        ];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_create_inventory_should_create_new_detail_when_inventory_has_detail_with_same_uom_but_different_expiration_date()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [
            [
                'id' => 1,
                'house_id' => 1,
                'quantity' => 3,
                'catalog_id' => 2,
                'catalog_description' => 'A PRODUCT DESCRIPTION 2',
                'category_id' => 1,
                'uom_id' => 1,
                'uom_abbreviation' => 'mg',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-10-30',
                'brand_id' => 1,
                'brand_name' => 'Ideal',
            ],
        ];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
    }

    public function test_create_inventory_should_create_new_detail_when_inventory_has_detail_with_different_uom_and_different_expiration_date()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];

        $inventory = [
            [
                'id' => 1,
                'house_id' => 1,
                'quantity' => 3,
                'catalog_id' => 2,
                'catalog_description' => 'A PRODUCT DESCRIPTION 2',
                'category_id' => 1,
                'uom_id' => 2,
                'uom_abbreviation' => 'g',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-10-30',
                'brand_id' => 1,
                'brand_name' => 'Ideal',
            ],
        ];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
    }

    public function test_create_inventory_should_update_existing_detail_when_uom_are_different()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];
        $inventory = [
            [
                'id' => 1,
                'catalog_id' => 1,
                'catalog_description' => 'A PRODUCT DESCRIPTION',
                'uom_id' => 2,
                'uom_abbreviation' => 'g',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-09-30',
                'quantity' => 100
            ]
        ];
        $newUom = [
            'from_conversions' => [
                [
                    'to_unit_id' => 2,
                    'factor' => 10.00
                ]
            ]
        ];
        $oldUom = [
            'from_conversions' => [
                [
                    'to_unit_id' => 1,
                    'factor' => 0.10
                ]
            ]
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $this->azulaInventoryService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->tophUnitOfMeasurementService->shouldReceive('get')
            ->twice()
            ->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($newUom))),
                        new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($oldUom))));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
    }

    public function test_create_inventory_should_return_not_found_when_uom_are_not_found()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];
        $inventory = [
            [
                'id' => 1,
                'catalog_id' => 1,
                'catalog_description' => 'A PRODUCT DESCRIPTION',
                'uom_id' => 2,
                'uom_abbreviation' => 'g',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-09-30',
                'quantity' => 100
            ]
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $this->azulaInventoryService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->tophUnitOfMeasurementService->shouldReceive('get')
            ->twice()
            ->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)),
                        new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_create_inventory_should_create_new_inventory_detail_when_uom_is_null()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];
        $inventory = [
            [
                'id' => 1,
                'catalog_id' => 1,
                'catalog_description' => 'A PRODUCT DESCRIPTION',
                'uom_id' => 2,
                'uom_abbreviation' => 'g',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-09-30',
                'quantity' => 100
            ]
        ];
        $newUom = [
            'from_conversions' => [
                [
                    'to_unit_id' => 2,
                    'factor' => 10.00
                ]
            ]
        ];
        $oldUom = [
            'from_conversions' => []
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $this->azulaInventoryService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->tophUnitOfMeasurementService->shouldReceive('get')
            ->twice()
            ->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($newUom))),
                        new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($oldUom))));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
    }

    public function test_create_inventory_should_return_unprocessable_entity_when_create_uom_is_null()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE',
        ];
        $inventory = [
            [
                'id' => 1,
                'catalog_id' => 1,
                'catalog_description' => 'A PRODUCT DESCRIPTION',
                'uom_id' => 2,
                'uom_abbreviation' => 'g',
                'purchase_date' => '2024-08-31',
                'expiration_date' => '2024-09-30',
                'quantity' => 100
            ]
        ];
        $newUom = [
            'from_conversions' => [
                [
                    'to_unit_id' => 2,
                    'factor' => 10.00
                ]
            ]
        ];
        $oldUom = [
            'from_conversions' => []
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY)));
        $this->tophUnitOfMeasurementService->shouldReceive('get')
            ->twice()
            ->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($newUom))),
                        new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($oldUom))));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }
}

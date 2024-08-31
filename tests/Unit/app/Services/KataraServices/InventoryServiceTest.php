<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\HouseService as AangHouseService;
use App\Services\AzulaServices\InventoryService as AzulaInventoryService;
use App\Services\KataraServices\InventoryService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    private $aangHouseService;
    private $azulaInventoryService;
    private $inventoryService;
    private $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->aangHouseService = Mockery::mock(AangHouseService::class);
        $this->azulaInventoryService = Mockery::mock(AzulaInventoryService::class);
        $this->inventoryService = new InventoryService($this->aangHouseService, $this->azulaInventoryService);
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
            'brand_name' => 'Ideal'
        ];
    }

    public function test_create_inventory_should_create_a_new_inventory_detail_when_there_the_house_inventory_is_empty()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE'
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
        $this->assertThrows(function() {
            $this->inventoryService->create($this->data);
        }, UnexpectedErrorException::class);
    }

    public function test_create_inventory_should_return_conflict_when_house_is_not_active()
    {
        $house = [
            'is_active' => false,
            'description' => 'A HOUSE'
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $response = $this->inventoryService->create($this->data);
        $this->assertEquals(HttpFoundationResponse::HTTP_CONFLICT, $response['code']);
    }

    public function test_create_inventory_should_throw_an_unexpected_error_exception_when_get_inventory_failed()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE'
        ];

        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function() {
            $this->inventoryService->create($this->data);
        }, UnexpectedErrorException::class);
    }

    public function test_create_inventory_should_return_unprocessable_entity_when_request_data_no_pass_validation()
    {
        $house = [
            'is_active' => true,
            'description' => 'A HOUSE'
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
            'description' => 'A HOUSE'
        ];

        $inventory = [];
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($house))));
        $this->azulaInventoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode($inventory))));
        $this->azulaInventoryService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function() {
            $this->inventoryService->create($this->data);
        }, UnexpectedErrorException::class);
    }
}

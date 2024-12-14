<?php
namespace App\Services\KataraServices;

use App\Exceptions\UnexpectedErrorException;
use App\Services\KataraServices\ProductCatalogService;
use App\Services\ZukoServices\ProductCatalogService as ZukoProductCatalogService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Mockery;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class ProductCatalogServiceTest extends TestCase
{
    private $zukoProductCatalogService;

    private $kataraProductCatalogService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->zukoProductCatalogService = Mockery::mock(ZukoProductCatalogService::class);
        $this->kataraProductCatalogService = new ProductCatalogService($this->zukoProductCatalogService);
    }

    public function test_create_should_create_a_new_product_catalog()
    {
        $this->zukoProductCatalogService->shouldReceive('create')->andReturn(new Response(
            new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => '/api/product-category/1'])));
        $response = $this->kataraProductCatalogService->create([]);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
        $this->assertEquals('Product catalog created successfully', $response['message']);

    }

    public function test_create_should_not_create_a_new_product_catalog_when_form_is_unprocessable()
    {
        $this->zukoProductCatalogService->shouldReceive('create')->andReturn(new Response(
            new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY)));
        $response = $this->kataraProductCatalogService->create([]);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_create_should_throw_an_exception_when_failed()
    {
        $this->zukoProductCatalogService->shouldReceive('create')->andReturn(new Response(
            new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraProductCatalogService->create([]);
        }, UnexpectedErrorException::class);

    }
}

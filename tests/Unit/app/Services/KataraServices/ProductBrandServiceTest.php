<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\KataraServices\ProductBrandService;
use App\Services\ZukoServices\ProductBrandService as ZukoProductBrandService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class ProductBrandServiceTest extends TestCase
{
    private $zukoProductBrandService;

    private $kataraProductBrandService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zukoProductBrandService = Mockery::mock(ZukoProductBrandService::class);
        $this->kataraProductBrandService = new ProductBrandService($this->zukoProductBrandService);
    }

    public function test_get_product_brand_list()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Ideal',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Soprole',
                'created_at' => null,
                'updated_at' => null,
            ],
        ];
        $this->zukoProductBrandService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, $data)));
        $response = $this->kataraProductBrandService->list();
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_get_product_brand_list_throws_an_unexpected_error_exception_when_there_failed()
    {
        $this->zukoProductBrandService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, [])));
        $this->assertThrows(function () {
            $this->kataraProductBrandService->list();
        }, UnexpectedErrorException::class);
    }
}

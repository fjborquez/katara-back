<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\KataraServices\ProductTypeService;
use App\Services\ZukoServices\ProductTypeService as ZukoProductTypeService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class ProductTypeServiceTest extends TestCase
{
    private $zukoProductTypeService;

    private $kataraProductTypeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zukoProductTypeService = Mockery::mock(ZukoProductTypeService::class);
        $this->kataraProductTypeService = new ProductTypeService($this->zukoProductTypeService);
    }

    public function test_get_product_type_list()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Cheese',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Butter',
                'created_at' => null,
                'updated_at' => null,
            ],
        ];
        $this->zukoProductTypeService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, $data)));
        $response = $this->kataraProductTypeService->list();
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_get_product_type_list_throws_an_unexpected_error_exception_when_there_failed()
    {
        $this->zukoProductTypeService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, [])));
        $this->assertThrows(function () {
            $this->kataraProductTypeService->list();
        }, UnexpectedErrorException::class);
    }
}

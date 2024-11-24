<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\KataraServices\ProductCategoryService;
use App\Services\ZukoServices\ProductCategoryService as ZukoProductCategoryService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class ProductCategoryServiceTest extends TestCase
{
    private $zukoProductCategoryService;

    private $kataraProductCategoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zukoProductCategoryService = Mockery::mock(ZukoProductCategoryService::class);
        $this->kataraProductCategoryService = new ProductCategoryService($this->zukoProductCategoryService);
    }

    public function test_get_product_category_list()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Bakery',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Butchery',
                'created_at' => null,
                'updated_at' => null,
            ],
        ];
        $this->zukoProductCategoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, $data)));
        $response = $this->kataraProductCategoryService->list();
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_get_product_category_list_throws_an_unexpected_error_exception_when_there_failed()
    {
        $this->zukoProductCategoryService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, [])));
        $this->assertThrows(function () {
            $this->kataraProductCategoryService->list();
        }, UnexpectedErrorException::class);
    }
}

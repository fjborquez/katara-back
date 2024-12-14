<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\KataraServices\ProductPresentationService;
use App\Services\ZukoServices\ProductPresentationService as ZukoProductPresentationService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class ProductPresentationServiceTest extends TestCase
{
    private $zukoProductPresentationService;

    private $kataraProductPresentationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zukoProductPresentationService = Mockery::mock(ZukoProductPresentationService::class);
        $this->kataraProductPresentationService = new ProductPresentationService($this->zukoProductPresentationService);
    }

    public function test_get_product_presentation_list()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Bottle',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Box',
                'created_at' => null,
                'updated_at' => null,
            ],
        ];
        $this->zukoProductPresentationService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, $data)));
        $response = $this->kataraProductPresentationService->list();
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_get_product_presentation_list_throws_an_unexpected_error_exception_when_there_failed()
    {
        $this->zukoProductPresentationService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, [])));
        $this->assertThrows(function () {
            $this->kataraProductPresentationService->list();
        }, UnexpectedErrorException::class);
    }
}

<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\ConsumptionLevelService as AangConsumptionLevelService;
use App\Services\KataraServices\ConsumptionLevelService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class ConsumptionLevelServiceTest extends TestCase
{
    private $aangConsumptionLevelService;

    private $kataraConsumptionLevelService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aangConsumptionLevelService = Mockery::mock(AangConsumptionLevelService::class);
        $this->kataraConsumptionLevelService = new ConsumptionLevelService($this->aangConsumptionLevelService);
    }

    public function test_get_consumption_level_list()
    {
        $data = [
            [
                'id' => 1,
                'value' => 0,
                'name' => 'Null',
                'description' => 'This level indicates that the person should not consume products from this category under any circumstances due to their nutritional restriction.',

            ],
            [
                'id' => 2,
                'value' => 1,
                'name' => 'Very Low',
                'description' => 'Extremely limited consumption; almost never consumed. Only occasionally and in very small amounts.',
            ],
        ];
        $this->aangConsumptionLevelService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, $data)));
        $response = $this->kataraConsumptionLevelService->list();
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_get_consumption_level_list_throws_an_unexpected_error_exception_when_there_failed()
    {
        $this->aangConsumptionLevelService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, [])));
        $this->assertThrows(function () {
            $this->kataraConsumptionLevelService->list();
        }, UnexpectedErrorException::class);
    }
}

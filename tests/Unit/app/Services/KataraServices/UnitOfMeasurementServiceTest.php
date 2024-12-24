<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\KataraServices\UnitOfMeasurementService as KataraUnitOfMeasurementService;
use App\Services\TophServices\UnitOfMeasurementService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UnitOfMeasurementServiceTest extends TestCase
{
    private $tophUnitOfMeasurementService;

    private $kataraUnitOfMeasurementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tophUnitOfMeasurementService = Mockery::mock(UnitOfMeasurementService::class);
        $this->kataraUnitOfMeasurementService = new KataraUnitOfMeasurementService($this->tophUnitOfMeasurementService);
    }

    public function test_list_should_return_a_list_of_units_of_measurement()
    {
        $unitOfMeasurementListResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $this->tophUnitOfMeasurementService->shouldReceive('list')->once()->andReturn($unitOfMeasurementListResponse);
        $response = $this->kataraUnitOfMeasurementService->list();
        $this->assertIsArray($response['message']);
    }

    public function test_list_should_throw_an_unexpected_exception()
    {
        $unitOfMeasurementListResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->tophUnitOfMeasurementService->shouldReceive('list')->once()->andReturn($unitOfMeasurementListResponse);
        $this->expectException(UnexpectedErrorException::class);
        $this->kataraUnitOfMeasurementService->list();
    }
}

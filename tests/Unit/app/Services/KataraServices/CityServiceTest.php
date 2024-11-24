<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\CityService as AangCityService;
use App\Services\KataraServices\CityService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class CityServiceTest extends TestCase
{
    private $aangCityService;

    private $kataraCityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aangCityService = Mockery::mock(AangCityService::class);
        $this->kataraCityService = new CityService($this->aangCityService);
    }

    public function test_get_cities_list()
    {
        $data = [
            [
                'id' => 1,
                'description' => 'Arica',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'description' => 'Iquique',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 3,
                'description' => 'Antofagasta',
                'created_at' => null,
                'updated_at' => null,
            ],
        ];
        $this->aangCityService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, $data)));
        $response = $this->kataraCityService->list();
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_get_city_list_throws_an_unexpected_error_exception_when_there_failed()
    {
        $this->aangCityService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, [])));
        $this->assertThrows(function () {
            $this->kataraCityService->list();
        }, UnexpectedErrorException::class);
    }
}

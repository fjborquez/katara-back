<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\NutritionalRestrictionService as AangNutritionalRestrictionService;
use App\Services\KataraServices\NutritionalRestrictionService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class NutritionalRestrictionServiceTest extends TestCase
{
    private $aangNutritionalRestrictionService;
    private $kataraNutritionalRestrictionService;

    public function setUp(): void
    {
        parent::setUp();
        $this->aangNutritionalRestrictionService = Mockery::mock(AangNutritionalRestrictionService::class);
        $this->kataraNutritionalRestrictionService = new NutritionalRestrictionService($this->aangNutritionalRestrictionService);
    }

    public function test_list_should_return_nutritional_restriction_list()
    {
        $getListResponse = new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode([])));
        $this->aangNutritionalRestrictionService->shouldReceive('list')->andReturn($getListResponse);
        $response = $this->kataraNutritionalRestrictionService->list();
        $this->assertEquals([], $response['message']);
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_list_should_throw_an_exception_when_there_is_an_list_server_error()
    {
        $getListResponse = new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangNutritionalRestrictionService->shouldReceive('list')->andReturn($getListResponse);
        $this->assertThrows(function () {
            $this->kataraNutritionalRestrictionService->list();
        }, UnexpectedErrorException::class);
    }
}

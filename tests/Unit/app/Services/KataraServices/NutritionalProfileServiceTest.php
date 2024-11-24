<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\NutritionalProfileService as AangNutritionalProfileService;
use App\Services\AangServices\UserService as AangUserService;
use App\Services\KataraServices\NutritionalProfileService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class NutritionalProfileServiceTest extends TestCase
{
    private $aangNutritionalProfileService;

    private $aangUserService;

    private $kataraNutritionalProfileService;

    protected function setUp(): void
    {
        $this->aangUserService = Mockery::mock(AangUserService::class);
        $this->aangNutritionalProfileService = Mockery::mock(AangNutritionalProfileService::class);
        $this->kataraNutritionalProfileService = new NutritionalProfileService($this->aangNutritionalProfileService, $this->aangUserService);
    }

    public function test_get_should_return_user_nutritional_profile()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]]))));
        $this->aangNutritionalProfileService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode([['id' => 1]]))));
        $response = $this->kataraNutritionalProfileService->get(1);
        $this->assertEquals([['id' => 1]], $response['message']);
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
    }

    public function test_get_should_return_user_not_found_when_user_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraNutritionalProfileService->get(1);
        $this->assertEquals('User not found.', $response['message']);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_get_should_throw_an_exception_when_get_user_response_is_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraNutritionalProfileService->get(1);
        }, UnexpectedErrorException::class);
    }

    public function test_get_should_return_not_found_when_nutritional_profile_user_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]]))));
        $this->aangNutritionalProfileService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraNutritionalProfileService->get(1);
        $this->assertEquals('Nutritional profile not found.', $response['message']);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_get_should_throw_an_exception_when_there_is_a_nutritional_profile_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]]))));
        $this->aangNutritionalProfileService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraNutritionalProfileService->get(1);
        }, UnexpectedErrorException::class);
    }
}

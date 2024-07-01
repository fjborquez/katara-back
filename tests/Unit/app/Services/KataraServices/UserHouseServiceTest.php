<?php

use App\Contracts\Services\AangServices\HouseServiceInterface as AangHouseServiceInterface;
use App\Contracts\Services\AangServices\PersonHouseServiceInterface as AangPersonHouseServiceInterface;
use App\Contracts\Services\AangServices\UserServiceInterface as AangUserServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\HouseRole;
use App\Services\KataraServices\UserHouseService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class UserHouseServiceTest extends TestCase
{
    private $aangHouseService;
    private $aangUserService;
    private $aangPersonHouseService;
    private $kataraUserHouseService;

    public function setUp(): void
    {
        parent::setUp();

        $this->aangHouseService = Mockery::mock(AangHouseServiceInterface::class);
        $this->aangUserService = Mockery::mock(AangUserServiceInterface::class);
        $this->aangPersonHouseService = Mockery::mock(AangPersonHouseServiceInterface::class);
        $this->kataraUserHouseService = new UserHouseService($this->aangHouseService, $this->aangUserService, $this->aangPersonHouseService);
    }

    public function test_create_should_create_a_new_user_house_relationship()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => [['id' => 1, 'pivot' => ['is_default' => 1, 'house_role_id' => HouseRole::HOST]]]]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => url('/api/house/1')])));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED), []));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK), [], json_encode(['id' => 1, 'description' => 'House 1', 'city' => 'City 1'])));
        $response = $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
        $this->assertEquals('Person added to house', $response['message']);
    }

    public function test_create_should_return_not_found_when_user_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('User not found', $response['message']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_get_user_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function() {
            $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_unprocessable_entity_when_house_form_is_incomplete()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => []]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error']))));
        $response = $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
        $this->assertEquals('Error', $response['message']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_create_house_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => []]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function() {
            $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_not_found_when_house_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => []]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => url('/api/house/1')])));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('House not found', $response['message']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_get_house_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => []]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => url('/api/house/1')])));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function() {
            $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_unprocessable_entity_when_user_house_relationship_form_is_incomplete()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => [['id' => 1, 'pivot' => ['is_default'
            => 1, 'house_role_id' => HouseRole::HOST]]]]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => url('/api/house/1')])));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error']))));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK), [], json_encode(['id' => 1, 'description' => 'House 1', 'city' => 'City 1'])));
        $response = $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
        $this->assertEquals('Error', $response['message']);
    }

    public function test_create_should_return_bad_request_when_the_house_description_and_city_exists()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => []]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => url('/api/house/1')])));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK), [], json_encode(['id' => 1, 'description' => 'House 1', 'city' => 'City 1'])));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_BAD_REQUEST)));
        $response = $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        $this->assertEquals(HttpFoundationResponse::HTTP_BAD_REQUEST, $response['code']);
        $this->assertEquals('The person already has a house with description in city', $response['message']);
    }

    public function test_create_should_return_not_found_when_person_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => []]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => url('/api/house/1')])));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK), [], json_encode(['id' => 1, 'description' => 'House 1', 'city' => 'City 1'])));
        $response = $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('Person not found', $response['message']);
    }

    public function test_create_throw_an_exception_when_there_is_a_user_house_relationship_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1, 'houses' => []]]))));
        $this->aangHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => url('/api/house/1')])));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK), [], json_encode(['id' => 1, 'description' => 'House 1', 'city' => 'City 1'])));
        $this->assertThrows(function() {
            $this->kataraUserHouseService->create(1, ['is_default' => 1, 'description' => 'Beach house', 'city_id' => 1]);
        }, UnexpectedErrorException::class);
    }
}

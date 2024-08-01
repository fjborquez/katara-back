<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\HouseService as AangHouseService;
use App\Services\AangServices\NutritionalProfileService as AangNutritionalProfileService;
use App\Services\AangServices\PersonHouseService as AangPersonHouseService;
use App\Services\AangServices\PersonService as AangPersonService;
use App\Services\AangServices\ResidentService as AangResidentService;
use App\Services\AangServices\UserService as AangUserService;
use App\Services\KataraServices\ResidentService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class ResidentServiceTest extends TestCase
{
    private $aangUserService;

    private $aangHouseService;

    private $aangPersonService;

    private $aangResidentService;

    private $aangNutritionalProfileService;

    private $aangPersonHouseService;

    private $kataraResidentService;

    public function setUp(): void
    {
        $this->aangUserService = Mockery::mock(AangUserService::class);
        $this->aangHouseService = Mockery::mock(AangHouseService::class);
        $this->aangPersonService = Mockery::mock(AangPersonService::class);
        $this->aangResidentService = Mockery::mock(AangResidentService::class);
        $this->aangNutritionalProfileService = Mockery::mock(AangNutritionalProfileService::class);
        $this->aangPersonHouseService = Mockery::mock(AangPersonHouseService::class);
        $this->kataraResidentService = new ResidentService($this->aangUserService, $this->aangHouseService,
            $this->aangPersonService, $this->aangResidentService, $this->aangNutritionalProfileService, $this->aangPersonHouseService);
    }

    public function test_create_should_create_new_resident()
    {
        $this->aangPersonService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => '/api/person/1'])));
        $this->aangNutritionalProfileService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $response = $this->kataraResidentService->create(1, 1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_CREATED, $response['code']);
        $this->assertEquals('Resident created successfully', $response['message']);
    }

    public function test_create_should_return_unprocessable_entity_when_there_is_a_person_form_error()
    {
        $this->aangPersonService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error']))));
        $response = $this->kataraResidentService->create(1, 1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
        $this->assertEquals('Error', $response['message']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_server_error()
    {
        $this->aangPersonService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->create(1, 1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_unprocessable_entity_when_there_is_a_nutritional_profile_form_error()
    {
        $this->aangPersonService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => '/api/person/1'])));
        $this->aangNutritionalProfileService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error']))));
        $this->aangPersonService->shouldReceive('delete')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $response = $this->kataraResidentService->create(1, 1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
        $this->assertEquals('Error', $response['message']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_nutritional_profile_server_error()
    {
        $this->aangPersonService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => '/api/person/1'])));
        $this->aangNutritionalProfileService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->aangPersonService->shouldReceive('delete')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->assertThrows(function () {
            $this->kataraResidentService->create(1, 1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_not_found_when_house_is_not_found()
    {
        $this->aangPersonService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => '/api/person/1'])));
        $this->aangNutritionalProfileService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $this->aangPersonService->shouldReceive('delete')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $response = $this->kataraResidentService->create(1, 1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('House not found', $response['message']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_create_person_house_server_error()
    {
        $this->aangPersonService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED, ['Location' => '/api/person/1'])));
        $this->aangNutritionalProfileService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_CREATED)));
        $this->aangPersonHouseService->shouldReceive('create')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->aangPersonService->shouldReceive('delete')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->assertThrows(function () {
            $this->kataraResidentService->create(1, 1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_get_should_return_resident()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $response = $this->kataraResidentService->get(1, 1, 1);
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
        $this->assertEquals(['id' => 1], $response['message']);
    }

    public function test_get_should_return_not_found_when_user_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraResidentService->get(1, 1, 1);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('User not found', $response['message']);
    }

    public function test_get_should_throw_an_exception_when_there_is_a_user_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->get(1, 1, 1);
        }, UnexpectedErrorException::class);
    }

    public function test_get_should_return_not_found_when_house_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraResidentService->get(1, 1, 1);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('House not found', $response['message']);
    }

    public function test_get_should_return_an_exception_when_there_is_a_house_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->get(1, 1, 1);
        }, UnexpectedErrorException::class);
    }

    public function test_get_should_return_not_found_when_person_is_not_found()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraResidentService->get(1, 1, 1);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('Person not found', $response['message']);
    }

    public function test_get_should_return_an_exception_when_there_is_a_person_server_error()
    {
        $this->aangUserService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangHouseService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->get(1, 1, 1);
        }, UnexpectedErrorException::class);
    }

    public function test_list_should_return_resident_list()
    {
        $this->aangResidentService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode([]))));
        $response = $this->kataraResidentService->list(1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
        $this->assertEquals([], $response['message']);
    }

    public function test_list_should_throw_an_exception_when_there_is_a_list_server_error()
    {
        $this->aangResidentService->shouldReceive('list')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->list(1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_update_should_return_resident_updated()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->aangNutritionalProfileService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $response = $this->kataraResidentService->update(1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
        $this->assertEquals('Resident updated successfully', $response['message']);

    }

    public function test_update_should_return_not_found_when_resident_is_not_found()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraResidentService->update(1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('Person not found', $response['message']);
    }

    public function test_update_should_throw_an_exception_when_there_is_a_get_person_server_error()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->update(1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_update_should_return_unprocessable_entity_when_there_is_an_update_form_error()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error']))));
        $response = $this->kataraResidentService->update(1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
        $this->assertEquals('Error', $response['message']);
    }

    public function test_update_should_throw_an_exception_when_there_is_an_update_person_server_error()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->update(1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_update_should_return_bad_request_when_there_is_a_nutritional_profile_service_error()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->aangNutritionalProfileService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_BAD_REQUEST)));
        $response = $this->kataraResidentService->update(1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_BAD_REQUEST, $response['code']);
        $this->assertEquals('BAD REQUEST', $response['message']);
    }

    public function test_update_should_return_not_found_when_nutritional_profile_is_not_found()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->aangNutritionalProfileService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraResidentService->update(1, []);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('Nutritional profile not found', $response['message']);
    }

    public function test_update_should_throw_an_exception_when_there_is_a_nutritional_profile_server_error()
    {
        $this->aangPersonService->shouldReceive('get')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK, [], json_encode(['id' => 1]))));
        $this->aangPersonService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NO_CONTENT)));
        $this->aangNutritionalProfileService->shouldReceive('update')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->update(1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_delete_should_delete_a_resident()
    {
        $this->aangResidentService->shouldReceive('delete')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_OK)));
        $response = $this->kataraResidentService->delete(1, 1, 1);
        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response['code']);
        $this->assertEquals('Resident deleted successfully', $response['message']);
    }

    public function test_delete_should_return_not_found_when_resident_is_not_found()
    {
        $this->aangResidentService->shouldReceive('delete')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_NOT_FOUND)));
        $response = $this->kataraResidentService->delete(1, 1, 1);
        $this->assertEquals(HttpFoundationResponse::HTTP_NOT_FOUND, $response['code']);
        $this->assertEquals('The house or the resident does not exists or resident does not belong to house', $response['message']);
    }

    public function test_delete_should_throw_an_exception_when_there_is_a_resident_server_error()
    {
        $this->aangResidentService->shouldReceive('delete')->andReturn(new Response(new Psr7Response(HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR)));
        $this->assertThrows(function () {
            $this->kataraResidentService->delete(1, 1, 1);
        }, UnexpectedErrorException::class);
    }
}

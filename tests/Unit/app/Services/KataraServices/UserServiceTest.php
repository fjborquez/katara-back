<?php

use App\Exceptions\UnexpectedErrorException;
use App\Services\AangServices\NutritionalProfileService;
use App\Services\AangServices\PersonService;
use App\Services\AangServices\UserService;
use App\Services\KataraServices\UserService as KataraServicesUserService;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private $aangUserService;

    private $aangPersonService;

    private $aangNutritionalProfileService;

    private $kataraUserService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aangUserService = Mockery::mock(UserService::class);
        $this->aangPersonService = Mockery::mock(PersonService::class);
        $this->aangNutritionalProfileService = Mockery::mock(NutritionalProfileService::class);
        $this->kataraUserService = new KataraServicesUserService($this->aangUserService, $this->aangPersonService, $this->aangNutritionalProfileService);
    }

    public function test_create_should_create_new_user()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/user/1')]));
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $nutritionalProfileCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->aangNutritionalProfileService->shouldReceive('create')->andReturn($nutritionalProfileCreateResponse);

        $response = $this->kataraUserService->create([]);

        $this->assertEquals('User created successfully', $response['message']);
        $this->assertEquals(Response::HTTP_CREATED, $response['code']);
    }

    public function test_create_should_return_unprocessable_entity_when_there_is_an_error_in_create_person()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error'])));
        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);

        $response = $this->kataraUserService->create([]);

        $this->assertEquals('Error', $response['message']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_create_person_server_error()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);

        $this->assertThrows(function () {
            $this->kataraUserService->create([]);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_not_found_when_person_is_not_found()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);

        $response = $this->kataraUserService->create([]);

        $this->assertEquals('Person not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_get_person_server_error()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);

        $this->assertThrows(function () {
            $this->kataraUserService->create([]);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_unprocessable_entity_when_there_is_an_error_in_create_user()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error'])));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);

        $response = $this->kataraUserService->create([]);

        $this->assertEquals('Error', $response['message']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_create_user_server_error()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);

        $this->assertThrows(function () {
            $this->kataraUserService->create([]);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_not_found_when_user_is_not_found()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, [], json_encode(['message' => 'Error'])));
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $userDisabledResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($userDisabledResponse);

        $response = $this->kataraUserService->create([]);

        $this->assertEquals('User not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_get_user_server_error()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $personDisabledResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($personDisabledResponse);

        $this->assertThrows(function () {
            $this->kataraUserService->create([]);
        }, UnexpectedErrorException::class);
    }

    public function test_create_should_return_unprocessable_entity_when_there_is_an_error_in_create_nutritional_profile()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, [], json_encode(['message' => 'Error'])));
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $nutritionalProfileCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error'])));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $userDisabledResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->aangNutritionalProfileService->shouldReceive('create')->once()->andReturn($nutritionalProfileCreateResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($userDisabledResponse);

        $user = [
            'nutritionalProfile' => [
                [
                    'consumption_level_id' => 4,
                    'product_category_id' => 5,
                    'product_category_description' => 'Fruits and Vegetables',
                ],
            ],
        ];

        $response = $this->kataraUserService->create($user);

        $this->assertEquals('Error', $response['message']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_create_should_return_not_found_when_there_is_no_person_to_create_nutritional_profile()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, [], json_encode(['message' => 'Error'])));
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $nutritionalProfileCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $userDisabledResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->aangNutritionalProfileService->shouldReceive('create')->once()->andReturn($nutritionalProfileCreateResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($userDisabledResponse);

        $user = [
            'nutritionalProfile' => [
                [
                    'consumption_level_id' => 4,
                    'product_category_id' => 5,
                    'product_category_description' => 'Fruits and Vegetables',
                ],
            ],
        ];
        $response = $this->kataraUserService->create($user);

        $this->assertEquals('Person for nutritional profile not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_create_should_throw_an_exception_when_there_is_a_create_nutritional_profile_server_error()
    {
        $personCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, ['Location' => url('/api/person/1')]));
        $personGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $userCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_CREATED, [], json_encode(['message' => 'Error'])));
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $nutritionalProfileCreateResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $personDeleteResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $userDisabledResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));

        $this->aangPersonService->shouldReceive('create')->once()->andReturn($personCreateResponse);
        $this->aangPersonService->shouldReceive('get')->once()->andReturn($personGetResponse);
        $this->aangUserService->shouldReceive('create')->once()->andReturn($userCreateResponse);
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->aangNutritionalProfileService->shouldReceive('create')->once()->andReturn($nutritionalProfileCreateResponse);
        $this->aangPersonService->shouldReceive('delete')->once()->andReturn($personDeleteResponse);
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($userDisabledResponse);

        $user = [
            'nutritionalProfile' => [
                [
                    'consumption_level_id' => 4,
                    'product_category_id' => 5,
                    'product_category_description' => 'Fruits and Vegetables',
                ],
            ],
        ];
        $this->assertThrows(function () use ($user) {
            $this->kataraUserService->create($user);
        }, UnexpectedErrorException::class);
    }

    public function test_get_should_return_a_user()
    {
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1])));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $response = $this->kataraUserService->get(1);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
        $this->assertEquals(['id' => 1], $response['message']);
    }

    public function test_get_should_return_not_found_when_user_is_not_found()
    {
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $response = $this->kataraUserService->get(1);
        $this->assertEquals('User not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_get_should_throw_an_exception_when_there_is_a_server_error()
    {
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->get(1);
        }, UnexpectedErrorException::class);
    }

    public function test_list_should_return_a_list_of_users()
    {
        $listResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['data' => [['id' => 1], ['id' => 2]]])));
        $this->aangUserService->shouldReceive('list')->once()->andReturn($listResponse);
        $response = $this->kataraUserService->list();
        $this->assertEquals(Response::HTTP_OK, $response['code']);
        $this->assertEquals(['data' => [['id' => 1], ['id' => 2]]], $response['message']);
    }

    public function test_list_should_throw_an_exception_when_there_is_a_server_error()
    {
        $listResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('list')->once()->andReturn($listResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->list();
        }, UnexpectedErrorException::class);
    }

    public function test_update_should_update_a_user()
    {
        $userGetResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $userUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $personUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK));
        $nutritionalProfileUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK));

        $this->aangUserService->shouldReceive('get')->once()->andReturn($userGetResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($userUpdateResponse);
        $this->aangPersonService->shouldReceive('update')->once()->andReturn($personUpdateResponse);
        $this->aangNutritionalProfileService->shouldReceive('update')->once()->andReturn($nutritionalProfileUpdateResponse);

        $response = $this->kataraUserService->update(1, [
            'nutritionalProfile' => [
                'product_category_id' => 1,
                'product_category_name' => 'Lacteos',
                'consumption_level_id' => 3
            ]
        ]);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
        $this->assertEquals('User updated successfully', $response['message']);
    }

    public function test_update_should_return_not_found_when_user_is_not_found()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $response = $this->kataraUserService->update(1, []);
        $this->assertEquals('User not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_update_should_throw_an_exception_when_there_is_a_server_error()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->update(1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_update_should_return_error_when_there_is_an_error_updating_an_user()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error'])));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $response = $this->kataraUserService->update(1, []);
        $this->assertEquals('Error', $response['message']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_update_should_return_not_found_when_updating_user_is_not_found()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $response = $this->kataraUserService->update(1, []);
        $this->assertEquals('User not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_update_should_throw_an_exception_when_there_is_an_updating_server_error()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->update(1, []);
        }, UnexpectedErrorException::class);

    }

    public function test_update_should_return_error_when_there_is_an_error_updating_a_person()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $getPersonUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_UNPROCESSABLE_ENTITY, [], json_encode(['message' => 'Error'])));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $this->aangPersonService->shouldReceive('update')->once()->andReturn($getPersonUpdateResponse);
        $response = $this->kataraUserService->update(1, []);
        $this->assertEquals('Error', $response['message']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response['code']);
    }

    public function test_update_should_return_not_found_when_person_is_not_found()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $getPersonUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $this->aangPersonService->shouldReceive('update')->once()->andReturn($getPersonUpdateResponse);
        $response = $this->kataraUserService->update(1, []);
        $this->assertEquals('Person not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_update_should_throw_an_exception_when_there_is_an_updating_person_server_error()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $getPersonUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $this->aangPersonService->shouldReceive('update')->once()->andReturn($getPersonUpdateResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->update(1, []);
        }, UnexpectedErrorException::class);
    }

    public function test_update_should_return_not_found_when_nutritional_profile_person_is_not_found()
    {
        $userResponseBody = [
            'id' => 1,
            'person' =>
                [
                    'id' => 1,
                    'nutritionalProfile' => [
                        [
                            'person_id' => 1,
                            'product_category_id' => 3,
                            'product_category_name' => 'Cheeses and Cold Cuts',
                        ]
                    ]
                ]
        ];
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode($userResponseBody)));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $getPersonUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $getUpdateNutritionalProfileResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $this->aangPersonService->shouldReceive('update')->once()->andReturn($getPersonUpdateResponse);
        $this->aangNutritionalProfileService->shouldReceive('update')->once()->andReturn($getUpdateNutritionalProfileResponse);
        $response = $this->kataraUserService->update(1, [
            'nutritionalProfile' => [
                'product_category_id' => 1,
                'product_category_name' => 'Lacteos',
                'consumption_level_id' => 3
            ]
        ]);
        $this->assertEquals('Person for nutritional profile not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_update_should_throw_an_exception_when_there_is_an_updating_nutritional_profile_server_error()
    {
        $getUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_OK, [], json_encode(['id' => 1, 'person' => ['id' => 1]])));
        $getUpdateUserResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $getPersonUpdateResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $getUpdateNutritionalProfileResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('get')->once()->andReturn($getUserResponse);
        $this->aangUserService->shouldReceive('update')->once()->andReturn($getUpdateUserResponse);
        $this->aangPersonService->shouldReceive('update')->once()->andReturn($getPersonUpdateResponse);
        $this->aangNutritionalProfileService->shouldReceive('update')->once()->andReturn($getUpdateNutritionalProfileResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->update(1, [
                'nutritionalProfile' => [
                    'product_category_id' => 1,
                    'product_category_name' => 'Lacteos',
                    'consumption_level_id' => 3
                ]
            ]);
        }, UnexpectedErrorException::class);
    }

    public function test_enable_should_enable_an_user()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $this->aangUserService->shouldReceive('enable')->once()->andReturn($getEnableResponse);
        $response = $this->kataraUserService->enable(1);
        $this->assertEquals('User enabled successfully', $response['message']);
        $this->assertEquals(Response::HTTP_OK, $response['code']);

    }

    public function test_enable_should_return_not_found_when_user_is_not_found()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $this->aangUserService->shouldReceive('enable')->once()->andReturn($getEnableResponse);
        $response = $this->kataraUserService->enable(1);
        $this->assertEquals('User not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_enable_should_return_bad_request_when_user_is_enabled()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_BAD_REQUEST));
        $this->aangUserService->shouldReceive('enable')->once()->andReturn($getEnableResponse);
        $response = $this->kataraUserService->enable(1);
        $this->assertEquals('User already enabled', $response['message']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response['code']);
    }

    public function test_enable_should_throw_an_exception_when_enable_has_an_server_error()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('enable')->once()->andReturn($getEnableResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->enable(1);
        }, UnexpectedErrorException::class);
    }

    public function test_disable_should_disable_an_user()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_NO_CONTENT));
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($getEnableResponse);
        $response = $this->kataraUserService->disable(1);
        $this->assertEquals('User disabled successfully', $response['message']);
        $this->assertEquals(Response::HTTP_OK, $response['code']);

    }

    public function test_disable_should_return_not_found_when_user_is_not_found()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_NOT_FOUND));
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($getEnableResponse);
        $response = $this->kataraUserService->disable(1);
        $this->assertEquals('User not found', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['code']);
    }

    public function test_disable_should_return_bad_request_when_user_is_enabled()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_BAD_REQUEST));
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($getEnableResponse);
        $response = $this->kataraUserService->disable(1);
        $this->assertEquals('User already disabled', $response['message']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response['code']);
    }

    public function test_disable_should_throw_an_exception_when_disable_has_an_server_error()
    {
        $getEnableResponse = new ClientResponse(new Psr7Response(Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->aangUserService->shouldReceive('disable')->once()->andReturn($getEnableResponse);
        $this->assertThrows(function () {
            $this->kataraUserService->disable(1);
        }, UnexpectedErrorException::class);
    }
}

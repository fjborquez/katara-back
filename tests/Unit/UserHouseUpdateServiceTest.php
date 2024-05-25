<?php

namespace Tests\Unit;

use App\Exceptions\AangResponseException;
use App\Services\UserExternalService\UserExternalService;
use App\Services\UserHouseUpdateService\UserHouseUpdateService;
use Exception;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class UserHouseUpdateServiceTest extends TestCase
{
    private $fakeHouses;
    private $mockedUserExternalService;
    private $userHouseUpdateService;
    private $fakeUserIdPayload;
    private $fakeHouseDataPayload;
    private $fakeUser;
    private $fakeHouseDataResponse;

    protected function setUp(): void
    {
        $this->fakeHouses = [
            0 => [
                'id' => 56,
                'description' => 'Eevee',
                'city_id' => 1,
                'pivot' => [
                    'is_default' => true,
                ]
            ],
            1 => [
                'id' => 57,
                'description' => 'Blastoise',
                'city_id' => 2,
                'pivot' => [
                    'is_default' => false,
                ]
            ],
            2 => [
                'id' => 58,
                'description' => 'Golem',
                'city_id' => 3,
                'pivot' => [
                    'is_default' => false,
                ]
            ],
            3 => [
                'id' => 59,
                'description' => 'Arcanine',
                'city_id' => 3,
                'pivot' => [
                    'is_default' => false,
                ]
            ]
        ];

        $this->mockedUserExternalService = Mockery::mock(UserExternalService::class);
        $this->userHouseUpdateService = new UserHouseUpdateService($this->mockedUserExternalService);

        $this->fakeUserIdPayload = 16;
        $this->fakeHouseDataPayload = [
            'house_id' => 58,
            'description' => 'Arcanine',
            'city_id' => 3,
            'is_default' => false,
        ];

        $this->fakeHouseDataResponse = [
            'id' => 58,
            'description' => 'Arcanine',
            'city_id' => 3,
            'is_default' => false,
        ];

        $this->fakeUser = new stdClass();
        $this->fakeUser->person = new stdClass();
        $this->fakeUser->person->id = 21;

        foreach ($this->fakeHouses as $fakeHouse) {
            $house = new stdClass();
            $house->id = $fakeHouse['id'];
            $house->description = $fakeHouse['description'];
            $house->city_id = $fakeHouse['city_id'];
            $pivot = new stdClass();
            $pivot->is_default = $fakeHouse['pivot']['is_default'];
            $house->pivot = $pivot;
            $this->fakeUser->person->houses[] = $house;
        }
    }

    public function test_update_house_successfully(): void
    {
        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_update_house_when_is_default_is_true(): void
    {
        $this->fakeHouseDataPayload['is_default'] = true;
        $this->fakeHouseDataResponse['is_default'] = true;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_update_house_when_is_default_is_false(): void
    {
        $this->fakeHouseDataPayload['is_default'] = false;
        $this->fakeHouseDataResponse['is_default'] = false;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_update_house_when_is_default_is_false_and_person_has_at_least_one_house_registered(): void
    {
        $this->fakeHouseDataPayload['is_default'] = false;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_no_update_new_house_when_description_is_empty(): void
    {
        $this->fakeHouseDataPayload['description'] = '';

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_no_update_the_house_when_maximum_allowed_characters_are_reached(): void
    {
        $this->fakeHouseDataPayload['description'] = 'THIS IS A VERY LARGE DESCRIPTION';

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andThrows(Exception::class);

        $this->expectException(AangResponseException::class);
        $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);
    }

    public function test_no_update_house_when_person_has_one_house_with_the_same_description_and_same_city_id(): void
    {
        $this->fakeHouseDataPayload['description'] = 'Blastoise';
        $this->fakeHouseDataPayload['city_id'] = 2;
        $this->fakeHouseDataPayload['is_default'] = false;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andThrows(Exception::class);

        $this->expectException(AangResponseException::class);
        $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);
    }

    public function test_no_update_house_when_city_is_empty(): void
    {
        $this->fakeHouseDataPayload['city_id'] = null;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andThrows(Exception::class);

        $this->expectException(AangResponseException::class);
        $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);
    }

    public function test_update_house_when_person_has_one_house_with_same_description_and_different_city_id(): void
    {
        $this->fakeHouseDataPayload['description'] = 'Golem';
        $this->fakeHouseDataPayload['city_id'] = 1;
        $this->fakeHouseDataPayload['is_default'] = false;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_update_house_when_person_has_one_house_with_same_city_id_and_different_description(): void
    {
        $this->fakeHouseDataPayload['description'] = 'A DIFFERENT DESCRIPTION';
        $this->fakeHouseDataPayload['city_id'] = 1;
        $this->fakeHouseDataPayload['is_default'] = false;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_update_house_when_person_has_one_house_with_different_description_in_different_city_id(): void
    {
        unset($this->fakeUser->person->houses[1]);
        unset($this->fakeUser->person->houses[2]);
        unset($this->fakeUser->person->houses[3]);

        $this->fakeHouseDataPayload['description'] = 'A DIFFERENT DESCRIPTION';
        $this->fakeHouseDataPayload['city_id'] = 1;
        $this->fakeHouseDataPayload['is_default'] = false;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andReturnSelf();
        $this->mockedUserExternalService->shouldReceive('getHouse')->once()->andReturn($this->fakeHouseDataResponse);
        $this->mockedUserExternalService->shouldReceive('updatePersonHouseRelation')->once()->andReturnSelf();

        $updatedHouse = $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);

        $this->assertEquals($this->fakeHouseDataPayload['house_id'], $updatedHouse['id']);
    }

    public function test_no_update_house_successfully_when_is_default_property_changed_from_true_to_false(): void
    {
        $this->fakeHouseDataPayload['is_default'] = false;

        $this->mockedUserExternalService->shouldReceive('getUser')->once()->andReturn($this->fakeUser);
        $this->mockedUserExternalService->shouldReceive('updateHouse')->once()->andThrows(Exception::class);

        $this->expectException(AangResponseException::class);

        $this->userHouseUpdateService->update($this->fakeUserIdPayload, $this->fakeHouseDataPayload);
    }
}

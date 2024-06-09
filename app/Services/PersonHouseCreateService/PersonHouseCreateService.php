<?php

namespace App\Services\PersonHouseCreateService;

use App\Contracts\Services\PersonHouseCreateService\PersonHouseCreateServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Exceptions\AangResponseException;
use App\HouseRole;
use Exception;

class PersonHouseCreateService implements PersonHouseCreateServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService)
    {

    }

    public function create(int $addingUserId, int $houseId, array $residentData): void
    {
        $person = $this->createPerson($residentData);
        $this->createNutritionalProfile($person->id, ['nutritionalProfile' => $residentData['nutritional_profile']]);
        $this->createPersonHouseRelationship($person->id, ['houses' => [
            $houseId => [
                'is_default' => false,
                'house_role_id' => HouseRole::RESIDENT
            ]
        ]]);
    }

    public function createPerson(array $residentData) {
        try {
            return $this->userExternalService->createPerson($residentData);
        } catch (Exception $e) {
            throw new AangResponseException($e);
        }
    }

    public function createNutritionalProfile(int $personId, array $residentData) {
        try {
            $this->userExternalService->nutritionalProfileCreate($personId, $residentData);
        } catch (Exception $e) {
            $this->deletePerson($personId);
            throw new AangResponseException($e);
        }
    }

    public function createPersonHouseRelationship(int $personId, array $housesId) {
        try {
            $this->userExternalService->createPersonHouseRelation($personId, $housesId);
        } catch (Exception $e) {
            $this->deleteNutritionalProfile($personId);
            $this->deletePerson($personId);
            $response = $e->getMessage();
            $message = explode(':', $response)[2];
            $message = trim(explode(', ', $message)[0], "\"");
            throw new AangResponseException($message);
        }
    }

    public function deletePerson(int $personId) {
        try {
            $this->userExternalService->deletePerson($personId);
        } catch (Exception $e) {
            throw new AangResponseException($e);
        }
    }

    public function deleteNutritionalProfile(int $personId) {
        try {
            $this->userExternalService->nutritionalProfileCreate($personId, []);
        } catch (Exception $e) {
            throw new AangResponseException($e);
        }
    }
}

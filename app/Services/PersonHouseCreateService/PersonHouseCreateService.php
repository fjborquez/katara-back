<?php

namespace App\Services\PersonHouseCreateService;

use App\Contracts\Services\PersonHouseCreateService\PersonHouseCreateServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Exceptions\AangResponseException;
use Exception;

class PersonHouseCreateService implements PersonHouseCreateServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService)
    {

    }

    public function create(array $residentData): array
    {
        // TODO: Crear persona
        $person = $this->createPerson($residentData);
        $this->createNutritionalProfile($person->id, $residentData);
        // TODO: Relacionar persona con casa

        return [];
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
            throw new AangResponseException($e);
        }
    }
}

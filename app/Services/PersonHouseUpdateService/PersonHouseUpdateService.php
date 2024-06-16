<?php

namespace App\Services\PersonHouseUpdateService;

use App\Contracts\Services\PersonHouseUpdateService\PersonHouseUpdateServiceInterface;
use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Exceptions\AangResponseException;
use Exception;

class PersonHouseUpdateService implements PersonHouseUpdateServiceInterface
{
    public function __construct(
        private readonly UserExternalServiceInterface $userExternalService
    ) {}

    public function update(int $personId, array $data)
    {
        $person = $this->getPerson($personId);
        $oldPersonData = $this->backupPersonData($person);
        $person = array_merge($person, $data);
        $this->updatePerson($personId, $person);
        $this->updateNutritionalProfile($personId, $data['nutritional_profile'], $oldPersonData);
    }

    public function backupPersonData($person)
    {
        foreach ($person as $key => $value) {
            $oldPersonData[$key] = $value;
        }

        return $oldPersonData;
    }

    public function getPerson(int $personId)
    {
        return $this->userExternalService->getPerson($personId);
    }

    public function updatePerson(int $personId, array $newPersondata)
    {
        $this->userExternalService->personUpdate($personId, $newPersondata);
    }

    public function updateNutritionalProfile(int $personId, array $nutritionalProfile, array $oldPersonData)
    {
        $this->userExternalService->updateNutritionalProfile($personId, ["nutritionalProfile" => $nutritionalProfile]);
    }
}

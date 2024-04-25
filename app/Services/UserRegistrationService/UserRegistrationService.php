<?php

namespace App\Services\UserRegistrationService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Contracts\Services\UserRegistrationService\UserRegistrationServiceInterface;
use Exception;
use stdClass;

class UserRegistrationService implements UserRegistrationServiceInterface {
    public function __construct(
        private readonly UserExternalServiceInterface $userExternalService
    ) {
    }

    public function register(array $data = []): object
    {
        $personCreated = $this->userExternalService->createPerson($data);

        if (!$personCreated) {
            throw new Exception('Error creating person');
        }

        $data['person_id'] = $personCreated->id;

        try {
            $userCreated = $this->userExternalService->createUser($data);
        } catch (Exception $e) {
            $this->userExternalService->deletePerson($personCreated->id);
            throw new Exception($e->getMessage());
        }

        if (!$userCreated) {
            $this->userExternalService->deletePerson($personCreated->id);
            throw new Exception('Error creating user');
        }

        $userRegistered = new stdClass();
        $userRegistered->idPerson = $personCreated->id;
        $userRegistered->idUser = $userCreated->id;
        $userRegistered->email = $userCreated->email;
        $userRegistered->name = $personCreated->name;
        $userRegistered->lastname = $personCreated->lastname;
        $userRegistered->date_of_birth = $personCreated->date_of_birth;

        return $userRegistered;
    }
}

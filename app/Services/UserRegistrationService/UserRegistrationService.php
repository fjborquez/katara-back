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
        $userRegistered = new stdClass();
        $personCreated = $this->userExternalService->createPerson($data);

        if (!$personCreated) {
            throw new Exception('Error creating person');
        }

        $userRegistered->idPerson = $personCreated->id;
        $userRegistered->name = $personCreated->name;
        $userRegistered->lastname = $personCreated->lastname;
        $userRegistered->date_of_birth = $personCreated->date_of_birth;


        $data['person_id'] = $personCreated->id;

        if (!empty($data['email']) && !empty($data['password'])) {
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

            $userRegistered->idUser = $userCreated->id;
            $userRegistered->email = $userCreated->email;
        }

        return $userRegistered;
    }
}

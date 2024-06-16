<?php

namespace App\Services\UserUpdateService;

use App\Contracts\Services\UserUpdateService\UserUpdateServiceInterface;
use App\Services\UserExternalService\UserExternalService;
use Exception;

class UserUpdateService implements UserUpdateServiceInterface
{
    public function __construct(private readonly UserExternalService $userExternalService) {}

    public function update(int $id, array $data): void
    {
        $data['person_id'] = $id;
        try {
            $user = $this->userExternalService->getUser($id);
            $this->userExternalService->userUpdate($user->id, $data);
            $this->userExternalService->personUpdate($user->person->id, $data);
            $this->userExternalService->updateNutritionalProfile($user->person->id, $data);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $message = explode(':', $response)[2];
            $message = trim(explode(',', $message)[0], "\"");
            throw new Exception($message);
        }
    }
}

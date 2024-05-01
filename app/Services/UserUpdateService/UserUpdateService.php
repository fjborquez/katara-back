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
            $person = $this->userExternalService->getPerson($id);
            $this->userExternalService->personUpdate($id, $data);
            $this->userExternalService->userUpdate($person['user']['id'], $data);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $message = explode(':', $response)[2];
            $message = trim(explode(',', $message)[0], "\"");
            throw new Exception($message);
        }
    }
}

<?php

namespace App\Contracts\Services\UserExternalService;

interface UserExternalServiceInterface
{
    public function createPerson(array $data = []): object;

    public function createUser(array $data = []): object;

    public function deletePerson(int $id): string;

    public function userList(): array;
}

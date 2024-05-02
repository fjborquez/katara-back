<?php

namespace App\Contracts\Services\UserExternalService;

interface UserExternalServiceInterface
{
    public function createPerson(array $data = []): object;

    public function createUser(array $data = []): object;

    public function deletePerson(int $id): string;

    public function userList(): array;

    public function personUpdate(int $id, array $data = []): bool;

    public function userUpdate(int $id, array $data = []): bool;

    public function getPerson(int $id): array;

    public function enable(int $id): void;

    public function disable(int $id): void;
}

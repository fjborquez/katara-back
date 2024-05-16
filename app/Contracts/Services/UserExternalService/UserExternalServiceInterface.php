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

    public function nutritionalRestrictionList(): array;

    public function nutritionalProfileCreate(int $id, array $data = []): void;

    public function getNutritionalProfile(int $id): array;

    public function getUser(int $id): object;

    public function updateNutritionalProfile(int $id, array $data = []): void;

    public function createHouse(array $data = []): object;

    public function createPersonHouseRelation(int $personId, array $houses): void;

    public function updateHouse(int $id, array $data = []): void;

    public function updatePersonHouseRelation(int $id, array $houses): void;
}

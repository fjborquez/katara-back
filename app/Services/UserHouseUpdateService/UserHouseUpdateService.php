<?php

namespace App\Services\UserHouseUpdateService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Contracts\Services\UserHouseUpdateService\UserHouseUpdateServiceInterface;
use App\Exceptions\AangResponseException;
use Exception;
use stdClass;

class UserHouseUpdateService implements UserHouseUpdateServiceInterface
{
    public function __construct(
        private readonly UserExternalServiceInterface $userExternalService
    ) {}

    public function update(int $userId, array $data): array
    {
        $user = $this->getUser($userId);
        $houses = $user->person->houses;
        $housesId = [];
        $oldHouse = new stdClass();

        foreach ($houses as $house) {
            $housesId[$house->id] = [
                "is_default" => $this->isDefaultHouse($house, $data),
            ];

            if ($house->id == $data['house_id']) {
                $oldHouse = $house;
                $housesId[$house->id] = [
                    "is_default" => $data['is_default'],
                ];
            }
        }

        $this->updateHouse($data['house_id'], $data);
        $this->updateHousesAndPersonRelationships($user, $housesId, $oldHouse);

        return $this->getHouse($data['house_id']);
    }

    public function getUser(int $userId)
    {
        try {
            return $this->userExternalService->getUser($userId);
        } catch (Exception $e) {
            $message = $this->extractErrorMessage($e->getMessage(), [":", ','], [2, 0]);
            throw new AangResponseException($message);
        }
    }

    public function getHouse(int $houseId): array
    {
        try {
            return $this->userExternalService->getHouse($houseId);
        } catch (Exception $e) {
            $message = $this->extractErrorMessage($e->getMessage(), [":", ','], [2, 0]);
            throw new AangResponseException($message);
        }
    }

    public function updateHousesAndPersonRelationships($user, $housesId, $oldHouse)
    {
        try {
            $this->userExternalService->updatePersonHouseRelation($user->person->id, ['houses' => $housesId]);
        } catch (Exception $e) {
            $message = $this->extractErrorMessage($e->getMessage(), ["\n", ','], [1, 0]);
            $this->updateHouse($oldHouse->id, (array) $oldHouse);
            throw new AangResponseException($message);
        }
    }

    public function updateHouse(int $houseId, array $data): void
    {
        try {
            $this->userExternalService->updateHouse($houseId, $data);
        } catch (Exception $e) {
            $message = $this->extractErrorMessage($e->getMessage(), [':', ','], [2, 0]);
            throw new AangResponseException($message);
        }
    }

    public function isDefaultHouse(stdClass $house, array $data)
    {
        if ($data['is_default']) {
            return 0;
        } else {
            return $house->pivot->is_default;
        }
    }

    public function extractErrorMessage(string $message, array $separators, array $positions)
    {
        if ($message == null)
        {
            return "";
        }

        $errorMessage = explode($separators[0], $message)[$positions[0]];
        $errorMessage = trim(explode($separators[1], $message)[0], "\"");

        return $errorMessage;
    }
}

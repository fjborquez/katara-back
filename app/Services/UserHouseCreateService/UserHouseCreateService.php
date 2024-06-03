<?php

namespace App\Services\UserHouseCreateService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Contracts\Services\UserHouseCreateService\UserHouseCreateServiceInterface;
use App\HouseRole;
use Exception;
use stdClass;

class UserHouseCreateService implements UserHouseCreateServiceInterface
{
    public function __construct(private readonly UserExternalServiceInterface $userExternalService)
    {}

    public function create(int $userId, array $data): object
    {
        try {
            $user = $this->userExternalService->getUser($userId);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $message = explode(':', $response)[2];
            $message = trim(explode(',', $message)[0], "\"");
            throw new Exception($message);
        }

        try {
            $createdHouse = $this->userExternalService->createHouse($data);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $message = explode(':', $response)[2];
            $message = trim(explode(',', $message)[0], "\"");
            throw new Exception($message);
        }

        $houses = $user->person->houses;
        $housesId = [];

        foreach ($houses as $house) {
            $housesId[$house->id] = [
                "is_default" => $data['is_default'] ? 0 : $house->pivot->is_default,
                "house_role_id" =>  $house->pivot->house_role_id,
            ];
        }

        $housesId[$createdHouse->id] = [
            "is_default" => $data['is_default'],
            "house_role_id" => HouseRole::HOST,
        ];

        try {
            $this->userExternalService->createPersonHouseRelation($user->person->id, ['houses' => $housesId]);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $message = explode(':', $response)[2];
            $message = trim(explode(', ', $message)[0], "\"");
            throw new Exception($message);
        }

        return new stdClass;
    }
}

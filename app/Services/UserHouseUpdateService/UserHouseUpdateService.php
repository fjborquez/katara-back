<?php

namespace App\Services\UserHouseUpdateService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Contracts\Services\UserHouseUpdateService\UserHouseUpdateServiceInterface;
use Exception;

class UserHouseUpdateService implements UserHouseUpdateServiceInterface
{
    public function __construct(
        private readonly UserExternalServiceInterface $userExternalService
    ) {}

    public function update(int $userId, array $data): void
    {
        try {
            $user = $this->userExternalService->getUser($userId);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $message = explode(':', $response)[2];
            $message = trim(explode(',', $message)[0], "\"");
            throw new Exception($message);
        }

        $houses = $user->person->houses;
        $housesId = [];
        $oldDescription = '';
        $oldCityId = 0;

        foreach ($houses as $house) {
            $housesId[$house->id] = [
                "is_default" => 0,
            ];

            if ($house->id == $data['house_id']) {
                try {
                    $oldDescription = $house->description;
                    $oldCityId = $house->city_id;
                    $this->userExternalService->updateHouse($house->id, $data);
                    $housesId[$house->id] = [
                        "is_default" => $data['is_default'],
                    ];
                } catch (Exception $e) {
                    $response = $e->getMessage();
                    $message = explode(':', $response)[2];
                    $message = trim(explode(',', $message)[0], "\"");
                    throw new Exception($message);
                }
            }
        }

        try {
            $this->userExternalService->updatePersonHouseRelation($user->person->id, ['houses' => $housesId]);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $message = explode("\n", $response)[1];
            $message = trim(explode(',', $message)[0], "\"");
            $this->userExternalService->updateHouse($house->id, [
                'description' => $oldDescription,
                'city_id' => $oldCityId,
            ]);
            throw new Exception($message);
        }


    }
}

<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\HouseServiceInterface as AangHouseServiceInterface;
use App\Contracts\Services\AangServices\PersonHouseServiceInterface as AangPersonHouseServiceInterface;
use App\Contracts\Services\AangServices\UserServiceInterface as AangUserServiceInterface;
use App\Contracts\Services\KataraServices\UserHouseServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\HouseRole;
use Symfony\Component\HttpFoundation\Response;

class UserHouseService implements UserHouseServiceInterface
{
    public function __construct(
        private readonly AangHouseServiceInterface $aangHouseService,
        private readonly AangUserServiceInterface $aangUserService,
        private readonly AangPersonHouseServiceInterface $aangPersonHouseService
    ) {}

    public function list(int $userId): array
    {
        $response = $this->aangUserService->get($userId);

        if ($response->failed()) {
            throw new UnexpectedErrorException();
        }

        $user = $response->json();

        return [
            'message' => $user['person']['houses'],
            'code' => $response->status()
        ];
    }

    public function create(int $userId, array $data): array
    {
        $message = "Person added to house";
        $code = Response::HTTP_CREATED;
        $getUserResponse = $this->aangUserService->get($userId);

        if ($getUserResponse->notFound()) {
            $message = "User not found";
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($getUserResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $user = $getUserResponse->json();
        $createHouseResponse = $this->aangHouseService->create($data);

        if ($createHouseResponse->unprocessableEntity()) {
            $message = $createHouseResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($createHouseResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $houseUrl = $createHouseResponse->header('Location');
        $houseUrl = explode('/', $houseUrl);
        $houseId = (int)end($houseUrl);

        $getHouseResponse = $this->aangHouseService->get($houseId);

        if ($getHouseResponse->notFound()) {
            $message = "House not found";
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($getHouseResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $houses = $user['person']['houses'];
        $housesId = [];

        foreach ($houses as $house) {
            $housesId[$house['id']] = [
                'is_default' => $data['is_default'] ? 0 : $house['pivot']['is_default'],
                'house_role_id' => $house['pivot']['house_role_id'],
            ];
        }

        $housesId[$houseId] = [
            'is_default' => $data['is_default'],
            'house_role_id' => HouseRole::HOST,
        ];

        $userHouseRelationshipResponse = $this->aangPersonHouseService->create($user['person']['id'], ['houses' => $housesId]);

        if ($userHouseRelationshipResponse->unprocessableEntity()) {
            $message = $userHouseRelationshipResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($userHouseRelationshipResponse->badRequest()) {
            // TODO: Corregir mensaje
            // TODO: Borrar casa
            $message = "The person already has a house with description in city";
            $code = Response::HTTP_BAD_REQUEST;
        } else if ($userHouseRelationshipResponse->notFound()) {
            $message = "Person not found";
            $code = Response::HTTP_NOT_FOUND;
        } else if ($userHouseRelationshipResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $message,
            'code' => $code
        ];
    }

    public function update(int $userId, array $data): array
    {

        $getUserResponse = $this->aangUserService->get($userId);

        if ($getUserResponse->notFound()) {
            $message = "User not found";
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($getUserResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $user = $getUserResponse->json();
        $houses = $user['person']['houses'];
        $housesId = [];
        $oldHouse = [];

        foreach ($houses as $house) {
            $housesId[$house['id']] = [
                'is_default' => $data['is_default'] ? 0 : $house['pivot']['is_default'],
                'house_role_id' => $house['pivot']['house_role_id'],
            ];

            if ($house['id'] == $data['house_id']) {
                $oldHouse = $house;
                $housesId[$house['id']] = [
                    'is_default' => $data['is_default'],
                    'house_role_id' => $house['pivot']['house_role_id'],
                ];
            }
        }

        $updateHouseResponse = $this->aangHouseService->update($data['house_id'], $data);

        if ($updateHouseResponse->unprocessableEntity()) {
            $message = $updateHouseResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $this->aangHouseService->update($data['house_id'], $oldHouse);

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($updateHouseResponse->notFound()) {
            $message = "House not found";
            $code = Response::HTTP_NOT_FOUND;
            $this->aangHouseService->update($data['house_id'], $oldHouse);

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($updateHouseResponse->failed()) {
            $this->aangHouseService->update($data['house_id'], $oldHouse);
            throw new UnexpectedErrorException();
        }

        $personHouseUpdateResponse = $this->aangPersonHouseService->update($user['person']['id'], ['houses' => $housesId]);

        if ($personHouseUpdateResponse->unprocessableEntity()) {
            $message = $personHouseUpdateResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($personHouseUpdateResponse->notFound()) {
            $message = "Person not found";
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($personHouseUpdateResponse->badRequest()) {
            // TODO: COrregir mensaje
            $message = "The person already has a house with description in city";
            $code = Response::HTTP_BAD_REQUEST;
        } else if ($personHouseUpdateResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => "House updated successfully",
            'code' => Response::HTTP_NO_CONTENT
        ];
    }

    public function enable(int $houseId): array
    {
        $response = $this->aangHouseService->enable($houseId);
        $message = "";
        $code = 0;

        if ($response->noContent()) {
            $message = "House enabled successfully";
            $code = Response::HTTP_OK;
        } else if($response->notFound()) {
            $message = "House not found";
            $code = Response::HTTP_NOT_FOUND;
        } else if ($response->badRequest()) {
            $message = "House already enabled";
            $code = Response::HTTP_BAD_REQUEST;
        } else {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $message,
            'code' => $code
        ];
    }

    public function disable(int $houseId): array
    {
        $response = $this->aangHouseService->disable($houseId);
        $message = "";
        $code = 0;

        if ($response->noContent()) {
            $message = "House disabled successfully";
            $code = Response::HTTP_OK;
        } else if($response->notFound()) {
            $message = "House not found";
            $code = Response::HTTP_NOT_FOUND;
        } else if ($response->badRequest()) {
            $message = "House already disabled";
            $code = Response::HTTP_BAD_REQUEST;
        } else {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $message,
            'code' => $code
        ];
    }
}

<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\HouseServiceInterface as AangHouseServiceInterface;
use App\Contracts\Services\AangServices\NutritionalProfileServiceInterface as AangNutritionalProfileServiceInterface;
use App\Contracts\Services\AangServices\PersonHouseServiceInterface as AangPersonHouseServiceInterface;
use App\Contracts\Services\AangServices\PersonServiceInterface as AangPersonServiceInterface;
use App\Contracts\Services\AangServices\ResidentServiceInterface as AangServicesResidentServiceInterface;
use App\Contracts\Services\AangServices\UserServiceInterface as AangUserServiceInterface;
use App\Contracts\Services\KataraServices\ResidentServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\HouseRole;
use Symfony\Component\HttpFoundation\Response;

class ResidentService implements ResidentServiceInterface
{
    public function __construct(
        private readonly AangUserServiceInterface $aangUserService,
        private readonly AangHouseServiceInterface $aangHouseService,
        private readonly AangPersonServiceInterface $aangPersonService,
        private readonly AangServicesResidentServiceInterface $aangResidentService,
        private readonly AangNutritionalProfileServiceInterface $aangNutritionalProfileService,
        private readonly AangPersonHouseServiceInterface $aangPersonHouseService
    ) {}

    public function get(int $userId, int $houseId, int $residentId): array
    {
        $getUserResponse = $this->aangUserService->get($userId);

        if ($getUserResponse->notFound()) {
            $message = 'User not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($getUserResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $user = $getUserResponse->json();
        $getHouseResponse = $this->aangHouseService->get($houseId);

        if ($getHouseResponse->notFound()) {
            $message = 'House not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($getHouseResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $house = $getHouseResponse->json();
        $getPersonResponse = $this->aangPersonService->get($residentId);

        if ($getPersonResponse->notFound()) {
            $message = 'Person not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($getPersonResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $getPersonResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }

    public function list(int $houseId): array
    {
        $residentListResponse = $this->aangResidentService->list($houseId);

        if ($residentListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $residentListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }

    public function create(int $userId, int $houseId, array $data): array
    {
        $createPersonResponse = $this->aangPersonService->create($data);

        if ($createPersonResponse->unprocessableEntity()) {
            $message = $createPersonResponse->json('message');
            $code = $createPersonResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($createPersonResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $person = $createPersonResponse->json();
        $personUrlParts = explode('/', $createPersonResponse->header('Location'));
        $personId = (int) end($personUrlParts);
        $createNutritionalProfileResponse = $this->aangNutritionalProfileService->create($personId, $data);

        if ($createNutritionalProfileResponse->unprocessableEntity()) {
            $message = $createNutritionalProfileResponse->json('message');
            $code = $createNutritionalProfileResponse->status();
            $this->aangPersonService->delete($personId);

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($createNutritionalProfileResponse->failed()) {
            $this->aangPersonService->delete($personId);
            throw new UnexpectedErrorException;
        }

        $createPersonHouseResponse = $this->aangPersonHouseService->create($personId, ['houses' => [
            $houseId => [
                'is_default' => false,
                'house_role_id' => HouseRole::RESIDENT,
            ],
        ]]);

        if ($createPersonHouseResponse->notFound()) {
            $message = 'House not found';
            $code = Response::HTTP_NOT_FOUND;
            $this->aangNutritionalProfileService->create($personId, []);
            $this->aangPersonService->delete($personId);

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($createPersonHouseResponse->failed()) {
            $this->aangNutritionalProfileService->create($personId, []);
            $this->aangPersonService->delete($personId);
            throw new UnexpectedErrorException;
        }

        return [
            'message' => 'Resident created successfully',
            'code' => Response::HTTP_CREATED,
        ];
    }

    public function update(int $personId, array $data): array
    {
        $getPersonResponse = $this->aangPersonService->get($personId);
        if ($getPersonResponse->notFound()) {
            $message = 'Person not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($getPersonResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $person = $getPersonResponse->json();
        $personBackup = [];

        foreach ($person as $property => $value) {
            $personBackup[$property] = $value;
        }

        $person = array_merge($person, $data);
        $updatePersonResponse = $this->aangPersonService->update($personId, $person);

        if ($updatePersonResponse->unprocessableEntity()) {
            $message = $updatePersonResponse->json('message');
            $code = $updatePersonResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($updatePersonResponse->failed()) {
            $this->aangPersonService->update($personId, $personBackup);
            throw new UnexpectedErrorException;
        }

        $updateNutritionalProfileResponse = $this->aangNutritionalProfileService->update($personId, $data);

        if ($updateNutritionalProfileResponse->badRequest()) {
            // TODO: Corregir mensaje.
            $message = 'BAD REQUEST';
            $code = Response::HTTP_BAD_REQUEST;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($updateNutritionalProfileResponse->notFound()) {
            $message = 'Nutritional profile not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($updateNutritionalProfileResponse->failed()) {
            $this->aangPersonService->update($personId, $personBackup);
            throw new UnexpectedErrorException;
        }

        return [
            'message' => 'Resident updated successfully',
            'code' => Response::HTTP_OK,
        ];
    }

    public function delete(int $houseId, int $residentId): array
    {
        $deleteResidentResponse = $this->aangResidentService->delete($houseId, $residentId);
        if ($deleteResidentResponse->notFound()) {
            $message = 'The house or the resident does not exists or resident does not belong to house';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($deleteResidentResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => 'Resident deleted successfully',
            'code' => Response::HTTP_OK,
        ];
    }
}

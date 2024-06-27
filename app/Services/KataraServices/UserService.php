<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\NutritionalProfileServiceInterface as AangNutritionalProfileServiceInterface;
use App\Contracts\Services\AangServices\PersonServiceInterface as AangPersonServiceInterface;
use App\Contracts\Services\AangServices\UserServiceInterface as AangUserServiceInterface;
use App\Contracts\Services\KataraServices\UserServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly AangUserServiceInterface $aangUserService,
        private readonly AangPersonServiceInterface $aangPersonService,
        private readonly AangNutritionalProfileServiceInterface $aangNutritionalProfileService
    ) {}

    public function get(int $userId): array
    {
        $response = $this->aangUserService->get($userId);

        if ($response->notFound()) {
            $message = 'User not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($response->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $response->json(),
            'code' => $response->status()
        ];
    }

    public function list(): array
    {
        $response = $this->aangUserService->list();

        if ($response->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $response->json(),
            'code' => $response->status()
        ];
    }

    public function create(array $data = []): array
    {
        $personCreateResponse = $this->aangPersonService->create($data);

        if ($personCreateResponse->unprocessableEntity()) {
            $message = $personCreateResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($personCreateResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $personUrlParts = explode('/', $personCreateResponse->header('Location'));
        $personId = (int) end($personUrlParts);
        $personGetResponse = $this->aangPersonService->get($personId);

        if ($personGetResponse->notFound()) {
            $message = 'Person not found';
            $code = Response::HTTP_NOT_FOUND;
            $this->aangPersonService->delete($personId);

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($personGetResponse->failed()) {
            $this->aangPersonService->delete($personId);
            throw new UnexpectedErrorException();
        }

        $person = $personGetResponse->json();
        $data['person_id'] = $personId;

        $userCreateResponse = $this->aangUserService->create($data);

        if ($userCreateResponse->unprocessableEntity()) {
            $message = $userCreateResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $this->aangPersonService->delete($personId);

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($userCreateResponse->failed()) {
            $this->aangPersonService->delete($personId);
            throw new UnexpectedErrorException();
        }

        $userUrlParts = explode('/', $userCreateResponse->header('Location'));
        $userId = (int) end($userUrlParts);
        $userGetResponse = $this->aangUserService->get($userId);

        if ($userGetResponse->notFound()) {
            $message = 'User not found';
            $code = Response::HTTP_NOT_FOUND;
            $this->aangPersonService->delete($personId);
            $this->aangUserService->disable($userId);

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($userGetResponse->failed()) {
            $this->aangPersonService->delete($personId);
            $this->aangUserService->disable($userId);
            throw new UnexpectedErrorException();
        }

        $user = $userGetResponse->json();
        $nutritionalProfileCreateResponse = $this->aangNutritionalProfileService->create($person['id'], $data);

        if ($nutritionalProfileCreateResponse->unprocessableEntity()) {
            $message = $nutritionalProfileCreateResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $this->aangPersonService->delete($personId);
            $this->aangUserService->disable($userId);

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($nutritionalProfileCreateResponse->notFound()) {
            $message = 'Person for nutritional profile not found';
            $code = Response::HTTP_NOT_FOUND;
            $this->aangPersonService->delete($personId);
            $this->aangUserService->disable($userId);

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($nutritionalProfileCreateResponse->failed()) {
            $this->aangPersonService->delete($personId);
            $this->aangUserService->disable($userId);
            throw new UnexpectedErrorException();
        }

        return [
            'message' => 'User created successfully',
            'code' => Response::HTTP_CREATED
        ];
    }

    public function update(int $id, array $data): array
    {
        $data['person_id'] = $id;
        $getUserResponse = $this->aangUserService->get($id);

        if ($getUserResponse->notFound()) {
            $message = 'User not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($getUserResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $user = $getUserResponse->json();
        $userUpdateResponse = $this->aangUserService->update($id, $data);

        if ($userUpdateResponse->unprocessableEntity()) {
            $message = $userUpdateResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($userUpdateResponse->notFound()) {
            $message = 'User not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($userUpdateResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $personUpdateResponse = $this->aangPersonService->update($id, $data);

        if ($personUpdateResponse->unprocessableEntity()) {
            $message = $personUpdateResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($personUpdateResponse->notFound()) {
            $message = 'Person not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($personUpdateResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        $nutritionalProfileUpdateResponse = $this->aangNutritionalProfileService->update($user['person']['id'], $data);

        if($nutritionalProfileUpdateResponse->notFound()) {
            $message = 'Person not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($nutritionalProfileUpdateResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => 'User updated successfully',
            'code' => Response::HTTP_NO_CONTENT
        ];
    }

    public function enable(int $id): array
    {
        $response = $this->aangUserService->enable($id);

        if ($response->notFound()) {
            $message = 'User not found';
            $code =Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => Response::HTTP_NOT_FOUND
            ];
        } else if ($response->badRequest()) {
            $message = 'User already enabled';
            $code = Response::HTTP_BAD_REQUEST;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($response->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => 'User enabled successfully',
            'code' => RESPONSE::HTTP_NO_CONTENT
        ];
    }

    public function disable(int $id): array
    {
        $response = $this->aangUserService->disable($id);

        if ($response->notFound()) {
            $message = 'User not found';
            $code = Response::HTTP_NOT_FOUND;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($response->badRequest()) {
            $message = 'User already disabled';
            $code = Response::HTTP_BAD_REQUEST;

            return [
                'message' => $message,
                'code' => $code
            ];
        } else if ($response->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => 'User disabled successfully',
            'code' => RESPONSE::HTTP_NO_CONTENT
        ];
    }

}

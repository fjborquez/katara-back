<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\NutritionalProfileServiceInterface as AangNutritionalProfileServiceInterface;
use App\Contracts\Services\AangServices\UserServiceInterface as AangUserServiceInterface;
use App\Contracts\Services\KataraServices\NutritionalProfileServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class NutritionalProfileService implements NutritionalProfileServiceInterface
{
    public function __construct(
        private readonly AangNutritionalProfileServiceInterface $aangNutritionalProfileService,
        private readonly AangUserServiceInterface $aangUserService
    ) {}

    public function get(int $userId): array
    {
        $getUserResponse = $this->aangUserService->get($userId);

        if ($getUserResponse->notFound()) {
            $message = 'User not found.';
            $code = $getUserResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($getUserResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $user = $getUserResponse->json();
        $getNutritionalProfileResponse = $this->aangNutritionalProfileService->get($user['person']['id']);

        if ($getNutritionalProfileResponse->notFound()) {
            $message = 'Nutritional profile not found.';
            $code = $getNutritionalProfileResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($getNutritionalProfileResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $getNutritionalProfileResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }

    public function delete(int $userId, int $productCategoryId): array
    {
        $getUserResponse = $this->aangUserService->get($userId);

        if ($getUserResponse->notFound()) {
            $message = 'User not found.';
            $code = $getUserResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($getUserResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        $user = $getUserResponse->json();
        $deleteNutritionalProfileResponse = $this->aangNutritionalProfileService->delete($user['person']['id'], $productCategoryId);

        if ($deleteNutritionalProfileResponse->notFound()) {
            $message = 'Nutritional profile not found.';
            $code = $deleteNutritionalProfileResponse->status();

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($deleteNutritionalProfileResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => 'The nutritional profile was deleted.',
            'code' => Response::HTTP_OK,
        ];
    }
}

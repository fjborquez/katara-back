<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\AuthTokenServiceInterface as AangServiceAuthTokenServiceInterface;
use App\Contracts\Services\KataraServices\AuthTokenServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenService implements AuthTokenServiceInterface
{
    public function __construct(
        private readonly AangServiceAuthTokenServiceInterface $aangAuthTokenService
    ) {}

    public function create(array $data = []): array
    {
        $oauthTokenResponse = $this->aangAuthTokenService->create($data);

        if ($oauthTokenResponse->unprocessableEntity()) {
            $message = $oauthTokenResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($oauthTokenResponse->unauthorized()) {
            $message = 'invalid credentials';
            $code = Response::HTTP_UNAUTHORIZED;

            return [
                'message' => $message,
                'code' => $code,
            ];
        } elseif ($oauthTokenResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $oauthTokenResponse->json(),
            'code' => Response::HTTP_CREATED,
        ];

    }
}

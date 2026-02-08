<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\OauthTokenServiceInterface as AangServiceOauthTokenServiceInterface;
use App\Contracts\Services\KataraServices\OauthTokenServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class OauthTokenService implements OauthTokenServiceInterface
{
    public function __construct(
        private readonly AangServiceOauthTokenServiceInterface $aangOauthTokenService
    ) {}

    public function create(array $data = []): array
    {
        $data['grant_type'] = 'password';
        $data['client_id'] = env('AANG_OAUTH_TOKEN_CLIENT_ID');
        $data['client_secret'] = env('AANG_OAUTH_TOKEN_CLIENT_SECRET');

        $oauthTokenResponse = $this->aangOauthTokenService->create($data);
        dd($oauthTokenResponse);

        if ($oauthTokenResponse->unprocessableEntity()) {
            $message = $oauthTokenResponse->json('message');
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

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

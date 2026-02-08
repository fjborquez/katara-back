<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\OauthTokenServiceInterface as AangServiceOauthTokenServiceInterface;
use App\Contracts\Services\KataraServices\OauthTokenServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class OauthTokenService implements OauthTokenServiceInterface
{
    public function __construct(
        private readonly AangServiceOauthTokenServiceInterface $aangOauthTokenService
    ) {}

    public function create(array $data = []): array
    {
        $data['grant_type'] = 'password';
        $data['client_id'] = Config::get('aang.oauth_token_client_id');
        $data['client_secret'] = Config::get('aang.oauth_token_client_secret');

        dd($data);

        $oauthTokenResponse = $this->aangOauthTokenService->create($data);

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

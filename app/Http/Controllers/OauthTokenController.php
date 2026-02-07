<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\OauthTokenServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OauthTokenRequest;
use Symfony\Component\HttpFoundation\Response;


class OauthTokenController extends Controller
{
    private $fields = ['username', 'password'];

    public function __construct(
        private readonly OauthTokenServiceInterface $oauthTokenService
    ) {}

    public function create(OauthTokenRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->oauthTokenService->create($validated);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

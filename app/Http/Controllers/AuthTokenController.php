<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\AuthTokenServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\AuthTokenRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenController extends Controller
{
    private $fields = ['email', 'password'];

    public function __construct(
        private readonly AuthTokenServiceInterface $authTokenService
    ) {}

    public function create(AuthTokenRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->authTokenService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

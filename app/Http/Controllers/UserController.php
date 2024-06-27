<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\UserServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\UserRequest;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private $fields = ['name', 'lastname', 'date_of_birth', 'email', 'password', 'nutritionalProfile'];

    public function __construct(
        private readonly UserServiceInterface $userService
    ) {}

    public function create(UserRequest $request) {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->userService->create($validated);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $userId, UserRequest $request) {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->userService->update($userId, $validated);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get(int $userId)
    {
        try {
            $response = $this->userService->get($userId);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function list()
    {
        try {
            $response = $this->userService->list();
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function enable(int $userId)
    {
        try {
            $response = $this->userService->enable($userId);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function disable(int $userId)
    {
        try {
            $response = $this->userService->disable($userId);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

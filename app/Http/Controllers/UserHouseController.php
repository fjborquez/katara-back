<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\UserHouseServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\UserHouseRequest;
use Symfony\Component\HttpFoundation\Response;

class UserHouseController extends Controller
{
    private $fields = ['description', 'city_id', 'is_default'];

    public function __construct(private readonly UserHouseServiceInterface $houseServiceInterface) {}

    public function list(int $userId) {
        try {
            $response = $this->houseServiceInterface->list($userId);
            return response()->json(['message' => $response['message']], Response::HTTP_OK);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(int $userId, UserHouseRequest $request) {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->houseServiceInterface->create($userId, $validated);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $userId, UserHouseRequest $request) {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->houseServiceInterface->update($userId, $validated);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function enable(int $userId, int $houseId) {
        try {
            $response = $this->houseServiceInterface->enable($houseId);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function disable(int $userId, int $houseId) {
        try {
            $response = $this->houseServiceInterface->disable($houseId);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

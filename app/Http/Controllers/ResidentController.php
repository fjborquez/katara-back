<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ResidentServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\ResidentRequest;
use Symfony\Component\HttpFoundation\Response;

class ResidentController extends Controller
{
    public function __construct(
        private readonly ResidentServiceInterface $residentService
    ) {}

    public function get(int $userId, int $houseId, int $residentId)
    {
        try {
            $response = $this->residentService->get($userId, $houseId, $residentId);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function list(int $userId, int $houseId)
    {
        try {
            $response = $this->residentService->list($houseId);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(int $userId, int $houseId, ResidentRequest $data)
    {
        try {
            $response = $this->residentService->create($userId, $houseId, $data->all());

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $userId, int $houseId, int $residentId, ResidentRequest $data)
    {
        try {
            $response = $this->residentService->update($residentId, $data->all());

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $userId, int $houseId, int $residentId)
    {
        try {
            $response = $this->residentService->delete($houseId, $residentId);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

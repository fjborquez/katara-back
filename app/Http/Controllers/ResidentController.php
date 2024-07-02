<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ResidentServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\ResidentRequest;

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
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function list(int $houseId)
    {
        try {
            $response = $this->residentService->list($houseId);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function create(int $userId, int $houseId, ResidentRequest $data)
    {
        try {
            $response = $this->residentService->create($userId, $houseId, $data->all());

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function update(int $userId, int $houseId, int $residentId, ResidentRequest $data)
    {
        try {
            $response = $this->residentService->update($residentId, $data->all());

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }
}

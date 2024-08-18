<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ConsumptionLevelServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ConsumptionLevelController extends Controller
{
    public function __construct(
        private readonly ConsumptionLevelServiceInterface $consumptionLevelService
    ) {}

    public function list()
    {
        try {
            $response = $this->consumptionLevelService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

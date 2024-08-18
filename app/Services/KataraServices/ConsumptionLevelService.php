<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\ConsumptionLevelServiceInterface as AangConsumptionLevelServiceInterface;
use App\Contracts\Services\KataraServices\ConsumptionLevelServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class ConsumptionlevelService implements ConsumptionLevelServiceInterface {
    public function __construct(
        private readonly AangConsumptionLevelServiceInterface $aangConsumptionLevelService
    ) {}

    public function list(): array {
        $consumptionLevelListResponse = $this->aangConsumptionLevelService->list();

        if ($consumptionLevelListResponse->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $consumptionLevelListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

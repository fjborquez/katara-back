<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\UnitOfMeasurementServiceInterface;
use App\Contracts\Services\TophServices\UnitOfMeasurementServiceInterface as TophUnitOfMeasurementServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class UnitOfMeasurementService implements UnitOfMeasurementServiceInterface
{
    public function __construct(
        private readonly TophUnitOfMeasurementServiceInterface $tophUnitOfMeasurementService
    ) {}

    public function list(array $filter = []): array
    {
        $unitOfMeasurementListResponse = $this->tophUnitOfMeasurementService->list($filter);

        if ($unitOfMeasurementListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $unitOfMeasurementListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\CityServiceInterface as AangCityServiceInterface;
use App\Contracts\Services\KataraServices\CityServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class CityService implements CityServiceInterface
{
    public function __construct(
        private readonly AangCityServiceInterface $aangCityLevelService
    ) {}

    public function list(): array
    {
        $cityListResponse = $this->aangCityLevelService->list();

        if ($cityListResponse->failed()) {
            throw new UnexpectedErrorException;
        }

        return [
            'message' => $cityListResponse->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

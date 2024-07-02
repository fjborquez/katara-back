<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\AangServices\NutritionalRestrictionServiceInterface as AangServicesNutritionalRestrictionServiceInterface;
use App\Contracts\Services\KataraServices\NutritionalRestrictionServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class NutritionalRestrictionService implements NutritionalRestrictionServiceInterface
{
    public function __construct(
        private readonly AangServicesNutritionalRestrictionServiceInterface $aangNutritionalRestrictionService
    ) {}

    public function list(): array
    {
        $response = $this->aangNutritionalRestrictionService->list();

        if ($response->failed()) {
            throw new UnexpectedErrorException();
        }

        return [
            'message' => $response->json(),
            'code' => Response::HTTP_OK,
        ];
    }
}

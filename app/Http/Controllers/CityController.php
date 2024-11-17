<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\CityServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    public function __construct(
        private readonly CityServiceInterface $cityService
    ) {}

    public function list()
    {
        try {
            $response = $this->cityService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

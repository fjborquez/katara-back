<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\NutritionalRestrictionServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class NutritionalRestrictionController extends Controller
{
    public function __construct(
        private readonly NutritionalRestrictionServiceInterface $nutritionalRestrictionService
    ) {}

    public function list()
    {
        try {
            $response = $this->nutritionalRestrictionService->list();
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

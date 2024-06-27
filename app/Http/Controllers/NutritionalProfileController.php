<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\NutritionalProfileServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Symfony\Component\HttpFoundation\Response;

class NutritionalProfileController extends Controller
{
    public function __construct(
        private readonly NutritionalProfileServiceInterface $nutritionalProfileService
    ) {}

    public function get(int $userId) {
        try {
            $response = $this->nutritionalProfileService->get($userId);
            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

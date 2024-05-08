<?php

namespace App\Http\Controllers;

use App\Contracts\Services\NutritionalProfileService\NutritionalProfileServiceInterface;
use Exception;

class NutritionalProfile extends Controller
{
    public function __construct(
        private readonly NutritionalProfileServiceInterface $nutritionalProfileService,
    ) {}

    public function get(int $userId)
    {
        try {
            return $this->nutritionalProfileService->getProfile($userId);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

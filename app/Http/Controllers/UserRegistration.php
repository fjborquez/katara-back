<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserRegistrationService\UserRegistrationServiceInterface;
use App\Http\Requests\UserRegistrationRequest;
use Exception;

class UserRegistration extends Controller
{
    public function __construct(
        private readonly UserRegistrationServiceInterface $userRegistrationService
    ) {}

    public function register(UserRegistrationRequest $request) {
        try {
            $userRegistered = $this->userRegistrationService->register($request->all());

            if ($userRegistered) {
                return response()->json(['message' => 'Usuario registrado correctamente'], 201);
            } else {
                return response()->json(['message' => 'Error al registrar el usuario'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
}

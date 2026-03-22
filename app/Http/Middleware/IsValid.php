<?php

namespace App\Http\Middleware;

use App\Contracts\Services\AangServices\AuthTokenServiceInterface as AangAuthTokenServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsValid
{
    public function __construct(
        private readonly AangAuthTokenServiceInterface $aangAuthTokenService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $checkResponse = $this->aangAuthTokenService->check($request->bearerToken());

        if ($checkResponse->unauthorized()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}

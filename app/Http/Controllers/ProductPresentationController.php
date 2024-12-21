<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\ProductPresentationServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use App\Http\Requests\ProductPresentationRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductPresentationController extends Controller
{
    private $fields = ["description"];

    public function __construct(
        private readonly ProductPresentationServiceInterface $productPresentationService
    ) {}

    public function list()
    {
        try {
            $response = $this->productPresentationService->list();

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ProductPresentationRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->productPresentationService->create($validated);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            report($exception);

            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

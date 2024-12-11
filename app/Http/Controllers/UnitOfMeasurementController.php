<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\UnitOfMeasurementServiceInterface;
use App\Exceptions\UnexpectedErrorException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnitOfMeasurementController extends Controller
{
    public function __construct(
        private readonly UnitOfMeasurementServiceInterface $unitOfMeasurementService
    ) {}

    public function list(Request $request)
    {
        $data = $request->all();
        $params = [];

        if (array_key_exists('filter', $data) && array_key_exists('category.name', $data['filter'])) {
            $params['filter[category.name]'] = $data['filter']['category.name'];
        }

        try {
            $response = $this->unitOfMeasurementService->list($params);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (UnexpectedErrorException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

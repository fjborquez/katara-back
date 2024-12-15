<?php

namespace App\Http\Controllers;

use App\Contracts\Services\KataraServices\GoogleCloudLogWriterServiceInterface;
use App\Http\Requests\LogWriterRequest;
use Symfony\Component\HttpFoundation\Response;

class LogWriterController extends Controller
{
    private $fields = ['message'];

    public function __construct(private readonly GoogleCloudLogWriterServiceInterface $googleCloudLogWriterServiceInterface) {}

    public function create(LogWriterRequest $request)
    {
        $validated = $request->safe()->only($this->fields);

        try {
            $response = $this->googleCloudLogWriterServiceInterface->write($validated['message']);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (\Exception $exception) {
            report($exception);

            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}

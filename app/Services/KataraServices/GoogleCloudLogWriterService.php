<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\GoogleCloudLogWriterServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GoogleCloudLogWriterService implements GoogleCloudLogWriterServiceInterface
{
    public function write($message) {
        Log::alert($message);

        return [
            'message' => 'Logged',
            'code' => Response::HTTP_CREATED
        ];
    }
}
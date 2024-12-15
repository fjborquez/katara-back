<?php

namespace App\Services\KataraServices;

use App\Contracts\Services\KataraServices\GoogleCloudLogWriterServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GoogleCloudLogWriterService implements GoogleCloudLogWriterServiceInterface
{
    public function write($message)
    {
        $messageDecoded = json_decode($message, true);

        Log::error('Katara error: '.$messageDecoded['message']);

        return [
            'message' => 'Logged',
            'code' => Response::HTTP_CREATED,
        ];
    }
}

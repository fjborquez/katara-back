<?php

namespace App\Contracts\Services\KataraServices;

interface GoogleCloudLogWriterServiceInterface {
    public function write($message);
}

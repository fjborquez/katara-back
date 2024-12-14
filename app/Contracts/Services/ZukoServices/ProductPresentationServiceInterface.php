<?php

namespace App\Contracts\Services\ZukoServices;

use Illuminate\Http\Client\Response;

interface ProductPresentationServiceInterface
{
    public function list(): Response;
}

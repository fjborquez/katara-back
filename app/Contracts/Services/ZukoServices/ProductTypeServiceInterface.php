<?php

namespace App\Contracts\Services\ZukoServices;

use Illuminate\Http\Client\Response;

interface ProductTypeServiceInterface
{
    public function list(): Response;
}

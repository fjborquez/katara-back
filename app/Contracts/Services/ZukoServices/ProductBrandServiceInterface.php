<?php

namespace App\Contracts\Services\ZukoServices;

use Illuminate\Http\Client\Response;

interface ProductBrandServiceInterface
{
    public function list(): Response;
}

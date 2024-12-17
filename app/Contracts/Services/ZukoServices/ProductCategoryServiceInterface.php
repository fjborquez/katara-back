<?php

namespace App\Contracts\Services\ZukoServices;

use Illuminate\Http\Client\Response;

interface ProductCategoryServiceInterface
{
    public function list(array $params = []): Response;

    public function create(array $data = []): Response;
}

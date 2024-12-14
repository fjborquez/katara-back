<?php

namespace App\Services\ZukoServices;

use App\Contracts\Services\ZukoServices\ProductBrandServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ProductBrandService implements ProductBrandServiceInterface
{
    public function list(): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)
            ->get(Config::get('zuko.url').'/product-brand');
    }
}

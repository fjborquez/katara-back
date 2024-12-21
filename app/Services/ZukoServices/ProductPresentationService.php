<?php

namespace App\Services\ZukoServices;

use App\Contracts\Services\ZukoServices\ProductPresentationServiceInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ProductPresentationService implements ProductPresentationServiceInterface
{
    public function list(): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)
            ->get(Config::get('zuko.url').'/product-presentation');
    }

    public function create(array $data = []): Response
    {
        return Http::accept('application/json')->retry(3, 100, null, false)
            ->post(Config::get('zuko.url').'/product-presentation', $data);
    }
}

<?php

namespace App\Contracts\Services\AangServices;

use Illuminate\Http\Client\Response;

interface NutritionalRestrictionServiceInterface
{
    public function list(): Response;
}

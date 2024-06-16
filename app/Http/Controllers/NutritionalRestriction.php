<?php

namespace App\Http\Controllers;

use App\Contracts\Services\NutritionalRestrictionService\NutritionalRestrictionServiceInterface;

class NutritionalRestriction extends Controller
{
    public function __construct(private readonly NutritionalRestrictionServiceInterface $nutritionalRestrictionService)
    {}

    public function getList()
    {
        return $this->nutritionalRestrictionService->getAll();
    }
}

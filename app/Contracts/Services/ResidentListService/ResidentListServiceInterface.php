<?php

namespace App\Contracts\Services\ResidentListService;

interface ResidentListServiceInterface
{
    public function get(int $houseId);
}

<?php

namespace App\Contracts\Services\PersonHouseUpdateService;

interface PersonHouseUpdateServiceInterface
{
    public function update(int $id, array $data);
}

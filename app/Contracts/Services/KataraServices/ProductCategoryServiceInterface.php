<?php

namespace App\Contracts\Services\KataraServices;

interface ProductCategoryServiceInterface
{
    public function list(): array;

    public function create(array $data = []): array;
}

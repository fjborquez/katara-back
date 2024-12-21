<?php

namespace App\Contracts\Services\KataraServices;

interface ProductPresentationServiceInterface
{
    public function list(): array;

    public function create(array $data = []): array;
}

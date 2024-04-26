<?php

namespace App\Services\UserExternalService;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use stdClass;

class UserExternalService implements UserExternalServiceInterface
{
    public function createPerson(array $data = []): object
    {
        $person = new stdClass();
        $response = Http::accept('application/json')->retry(3, 100)->post(Config::get('aang.url') . '/person', $data);

        if ($response->successful())
        {
            $body = $response->json();
            $person->id = $body['person']['id'];
            $person->name = $body['person']['name'];
            $person->lastname = $body['person']['lastname'];
            $person->date_of_birth = $body['person']['date_of_birth'];
        }

        return $person;
    }

    public function createUser(array $data = []): object
    {
        $user = new stdClass();
        $response = Http::accept('application/json')->retry(3, 100)->post(Config::get('aang.url') . '/user', $data);

        if ($response->successful())
        {
            $body = $response->json();
            $user->id = $body['user']['id'];
            $user->email = $body['user']['email'];
        }

        return $user;
    }

    public function deletePerson(int $id): string
    {
        $response = Http::accept('application/json')->retry(3, 100)->delete(Config::get('aang.url') . '/person/' . $id);
        return $response->body();
    }
}

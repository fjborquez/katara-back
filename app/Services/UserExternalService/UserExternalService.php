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

    public function userList(): array
    {
        $response = Http::accept('application/json')->retry(3, 100)->get(Config::get('aang.url') . '/user');
        return $response->json();
    }

    public function personUpdate(int $id, array $data = []): bool
    {
        $response = Http::accept('application/json')->retry(3, 100)->put(Config::get('aang.url') . '/person/' . $id, $data);
        return $response->body();
    }

    public function userUpdate(int $id, array $data = []): bool
    {
        $response = Http::accept('application/json')->retry(3, 100)->put(Config::get('aang.url') . '/user/' . $id, $data);
        return $response->body();
    }

    public function getPerson(int $id): array
    {
        $response = Http::accept('application/json')->retry(3, 100)->get(Config::get('aang.url') . '/person/' . $id);
        return $response->json();
    }

    public function enable(int $id): void
    {
        Http::accept('application/json')->retry(3, 100)->put(Config::get('aang.url') . '/user/' . $id . '/enable');
    }

    public function disable(int $id): void
    {
        Http::accept('application/json')->retry(3, 100)->put(Config::get('aang.url') . '/user/' . $id . '/disable');
    }

    public function nutritionalRestrictionList(): array
    {
        $response = Http::accept('application/json')->retry(3, 100)->get(Config::get('aang.url') . '/nutritional-restriction');
        return $response->json();
    }

    public function nutritionalProfileCreate(int $id, array $data = []): void
    {
        Http::accept('application/json')->retry(3, 100)->post(Config::get('aang.url') . '/person/' . $id . '/nutritional-profile', $data);
    }

    public function getNutritionalProfile(int $id): array
    {
        $response = Http::accept('application/json')->retry(3, 100)->get(Config::get('aang.url') . '/person/' . $id . '/nutritional-profile');
        return $response->json();
    }

    public function getUser(int $id): object
    {
        $response = Http::accept('application/json')->retry(3, 100)->get(Config::get('aang.url') . '/user/' . $id);
        return json_decode($response);
    }

    public function updateNutritionalProfile(int $id, array $data = []): void
    {
        Http::accept('application/json')->retry(3, 100)->put(Config::get('aang.url') . '/person/'. $id . '/nutritional-profile', $data);
    }

    public function createHouse(array $data = []): object
    {

        $response = Http::accept('application/json')->retry(3, 100)->post(Config::get('aang.url') . '/house', $data);
        $body = $response->json();
        $house = new stdClass();
        $house->id = $body['house']['id'];
        $house->description = $body['house']['description'];
        $house->city_id = $body['house']['city_id'];
        return $house;
    }

    public function createPersonHouseRelation(int $personId, array $houses): void
    {
        Http::accept('application/json')->retry(3, 100)->post(Config::get('aang.url') . '/person/' . $personId . '/house', $houses);
    }
}

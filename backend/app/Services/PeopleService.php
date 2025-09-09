<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class PeopleService
{
    public function getPeopleBySearch(string $searchTerm): array
    {
        #TODO: use env var to swapi url
       $response = Http::get('https://www.swapi.tech/api/people/', [
           'name' => $searchTerm
       ]);

       return $response->json()['result'] ?? [];
    }

    public function getPersonById(int $id)
    {
        $response = Http::get("https://www.swapi.tech/api/people/{$id}");

        return $response->json()['result'] ?? null;
    }
}


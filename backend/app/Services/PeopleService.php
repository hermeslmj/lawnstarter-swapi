<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Dtos\PeopleDTO;


class PeopleService
{
    public function getPeopleBySearch(string $searchTerm): array
    {

        #TODO: use env var to swapi url
       $response = Http::get('https://www.swapi.tech/api/people/', [
           'name' => $searchTerm
       ]);

       $peopleListObj = json_decode($response->body(), true);
  
       $peopleDTOArray = array_map(function ($person) {
            //TODO: move it to a function that returns a PeopleDTO
            $movieData = $this->_getMoviesDataForPerson($person['properties']['films'] ?? []);
            return new PeopleDTO(
               $person['uid'] ?? '',
               $person['properties']['name'] ?? '',
               $person['properties']['gender'] ?? '',
               $person['properties']['eye_color'] ?? '',
               $person['properties']['hair_color'] ?? '',
               $person['properties']['height'] ?? '',
               $person['properties']['mass'] ?? '',
               $movieData
            );
       }, $peopleListObj['result'] ?? []);
     
       return $peopleDTOArray;
    }

    public function getPersonById(string $id)
    {
        $response = Http::get("https://www.swapi.tech/api/people/{$id}");
        $personObj = json_decode($response->body(), true);
        $movieData = $this->_getMoviesDataForPerson($personObj['result']['properties']['films'] ?? []);
        $personDTO = new PeopleDTO(
            $personObj['result']['uid'] ?? '',
            $personObj['result']['properties']['name'] ?? '',
            $personObj['result']['properties']['gender'] ?? '',
            $personObj['result']['properties']['eye_color'] ?? '',
            $personObj['result']['properties']['hair_color'] ?? '',
            $personObj['result']['properties']['height'] ?? '',
            $personObj['result']['properties']['mass'] ?? '',
            $movieData
        );
        return $personDTO;
    }

    private function _getMoviesDataForPerson(array $movieUrls): array
    {
        $movies = [];
        foreach ($movieUrls as $url) {
            $response = Http::get($url);
            if ($response->successful()) {
                $movieData = $response->json()['result'] ?? null;
                if ($movieData) {
                    $movies[] = ['id' => $movieData['uid'], 'title' => $movieData['properties']['title'] ?? ''];
                }
            }
        }
        return $movies;
    }
}
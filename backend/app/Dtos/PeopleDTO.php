<?php

namespace App\Dtos;

class PeopleDTO
{

    public string $uid;
    public string $name;
    public string $gender;
    public string $eyecolor;
    public string $haircolor;
    public string $height;
    public string $mass;
    public string $birthYear;
    public array $movies;

    /**
     * @param string $uid
     * @param string $name
     * @param string $gender
     * @param string $eyecolor
     * @param string $haircolor
     * @param string $height
     * @param string $mass
     * @param array $movies
     */
    public function __construct(
        string $uid,
        string $name,
        string $gender,
        string $eyecolor,
        string $haircolor,
        string $height,
        string $mass,
        string $birthYear,
        array $movies
    ) {
        $this->uid = $uid;
        $this->name = $name;
        $this->gender = $gender;
        $this->eyecolor = $eyecolor;
        $this->haircolor = $haircolor;
        $this->height = $height;
        $this->mass = $mass;
        $this->birthYear = $birthYear;
        $this->movies = $movies;
    }
    
}

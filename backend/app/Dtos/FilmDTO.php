<?php

namespace App\Dtos;

class FilmDTO
{
    public string $uid;
    public string $title;
    public string $openingCrawl;
    public array $characters;

    /**
     * @param string $uid
     * @param string $title
     * @param string $openingCrawl
     * @param array $characters
     */
    public function __construct(string $uid, string $title, string $openingCrawl, array $characters)
    {
        $this->uid = $uid;
        $this->title = $title;
        $this->openingCrawl = $openingCrawl;
        $this->characters = $characters;
    }
}

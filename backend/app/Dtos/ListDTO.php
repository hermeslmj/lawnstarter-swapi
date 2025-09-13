<?php

namespace App\Dtos;

class ListDTO
{
    public string $uid;
    public string $title;

    /**
     * Create a new ListDTO instance.
     *
     * @param string $id
     * @param string $title
     */
    public function __construct(string $uid, string $title)
    {
        $this->uid = $uid;
        $this->title = $title;
    }
}

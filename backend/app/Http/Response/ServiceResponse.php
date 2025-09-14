<?php

namespace App\Http\Response;

class ServiceResponse
{
    public function __construct(
        public mixed $data,
        public string $message,
        public bool $success,
        public int $code = 200
    ) {}
}
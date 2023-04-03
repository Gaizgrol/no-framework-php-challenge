<?php

declare(strict_types=1);

namespace Http;

class Response
{
    public function status(int $code)
    {
        http_response_code($code);
        return $this;
    }

    public function send(mixed $body = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        exit(json_encode($body));
    }
}

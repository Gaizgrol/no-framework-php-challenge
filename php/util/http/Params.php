<?php

declare(strict_types=1);

namespace Http;

abstract class Params
{
    protected $data = [];

    abstract public static function fromRequest(): Params;

    public function __construct(array $keyValues)
    {
        $this->data = $keyValues;
    }

    public function get(): array
    {
        return $this->data;
    }
}

class Query extends Params
{
    public static function fromRequest(): Params
    {
        return new Query($_GET);
    }
}

class Body extends Params
{
    public static function fromRequest(): Params
    {
        $allowedHttpMethodsWithBody = ['POST', 'PUT', 'PATCH'];
        $method = $_SERVER['REQUEST_METHOD'];

        $body = null;

        if (in_array($method, $allowedHttpMethodsWithBody)) {
            $body = json_decode(file_get_contents('php://input'), true, 512);
        }

        return new static($body ? $body : []);
    }
}

class Path extends Params
{
    public static function fromRequest(): Params
    {
        return new Path($_SERVER['PATH_PARAMS']);
    }
}

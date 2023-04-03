<?php

namespace App;

use Logger;
use Http\Body;
use Http\Path;
use Http\Query;
use Http\Response;

class ProductController
{
    public function __construct()
    {
    }

    public function index(Query $pagination, Response $response)
    {
        Logger::log('ProductController', 'update');
    }

    public function show(Path $fragments, Response $response)
    {
        Logger::log('ProductController', 'index');
    }

    public function create(Body $product, Response $response)
    {
        Logger::log('ProductController', 'create');
    }

    public function update(Path $fragments, Body $product, Response $response)
    {
        Logger::log('ProductController', 'update');
    }
}

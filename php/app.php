<?php

declare(strict_types=1);

require 'util/Logger.php';
require 'util/dependencies/AutoLoad.php';

use DTO\UpdateProductDTO;
use Http\Response;
use Http\Status;
use Http\Router;

AutoLoad::all('util');
AutoLoad::all('modules');

// $path = explode('?', $_SERVER['REQUEST_URI'])[0];
// require 'routes.php';
// Router::matchPaths($path, $routes);

try {
    // Router::processRequest();
} catch (Throwable $th) {
    $response = new Response();
    $response->status(Status::SERVER_ERROR)->send($th->getMessage());
}

// $product = new Product();
// $product->name = 'unheeeeeeeeee';
// $product->price = 2000;
// $product->save();

// Logger::log('table', $all = Product::findAll(0, 15));

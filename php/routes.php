<?php

declare(strict_types=1);

$routes = [
    ['GET' => 'product', [App\ProductController::class, 'index']],
    ['GET' => 'product/:id', [App\ProductController::class, 'show']],
    ['POST' => 'product', [App\ProductController::class, 'create']],
    ['PUT' => 'product/:id', [App\ProductController::class, 'update']],
];

<?php

declare(strict_types=1);

class DIContainerBuilder
{
    private $declarations = [];

    public function define(string $token, object $instance)
    {
        $declarations[$token] = $instance;
    }

    public function build()
    {
        return;
    }
}

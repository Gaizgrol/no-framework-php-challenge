<?php

declare(strict_types=1);

function classIsOrIsDerivedFrom(mixed $objOrClass, string $className)
{
    return $objOrClass == $className || is_subclass_of($objOrClass, $className);
}

function getMethodParams(object|string $class, string $method)
{
    $classInfo = new ReflectionClass($class);
    $metadata = $classInfo->getMethod($method);
    return array_map(function (ReflectionParameter $param) {
        return [
            'type' => $param->getType()->getName(),
            'name' => $param->getName()
        ];
    }, $metadata->getParameters());
}

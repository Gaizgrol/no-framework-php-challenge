<?php

declare(strict_types=1);

namespace Http;

class Router
{
    private static ?\Closure $action;

    private static function fragmentPath(string $path)
    {
        return array_values(
            array_filter(
                explode('/', $path),
                function (string $element) {
                    return !!$element;
                }
            )
        );
    }

    public static function processRequest(): void
    {
        $callback = self::$action;
        if ($callback) {
            $callback();
        }
    }

    public static function matchPaths(string $url, array $mappedUrls)
    {
        $matched = [
            'path' => null,
            'params' => []
        ];

        $urlFragments = self::fragmentPath($url);
        $urlFragmentCount = count($urlFragments);

        $method = null;

        foreach ($mappedUrls as $urlCandidate => [$class, $function]) {

            $method = $function;
            $params = [];

            $candidateFragments = self::fragmentPath($urlCandidate);

            if ($urlFragmentCount != count($candidateFragments)) {
                continue;
            }

            $urlMatched = true;

            for ($i = 0; $i < $urlFragmentCount; $i++) {
                $candidateFragment = $candidateFragments[$i];
                $urlFragment = $urlFragments[$i];

                if ($candidateFragment[0] == ':') {
                    $params[substr($candidateFragment, 1)] = $urlFragment;
                    continue;
                }

                if ($candidateFragment != $urlFragment) {
                    $urlMatched = false;
                    break;
                }
            }

            if ($urlMatched) {
                $matched['path'] = $urlCandidate;
                $matched['params'] = $params;
                break;
            }
        }

        $_SERVER['PATH_PARAMS'] = $matched['params'];

        if ($matched['path'] && $method) {
            self::$action = function () use ($method, $matched) {
                http_response_code(200);
                $method($matched);
            };
        } else {
            http_response_code(404);
        }
    }
}

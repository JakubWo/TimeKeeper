<?php

namespace src\Service\RoutingService;

use src\Service\ErrorService\ErrorService;

class RoutingService
{
    private array $routes;

    public function getRoute(string $path_code): ?string
    {
        $path = $this->codeIntoPath($path_code);
        if ($path !== 'assets' && !empty($path)) {
            return $path;
        } else if ($path === 'assets') {
            ErrorService::generate('Couldn\'t load asset');
            return null;
        }

        // Changes URL for every wrong route request.
        header("Location: /error");
        return null;
    }

    private function codeIntoPath(string $path_code): ?string
    {
        if (empty($this->routes)) {
            $this->routes = yaml_parse_file("config/routes.yaml");
        }
        $path_parts = explode('-', $path_code);
        $path = $this->routes[$path_parts[0]][$path_parts[1]];

        if (empty($path) && in_array($path_parts[0], ['style', 'js', 'image'])) {
            $path = 'assets';
        }

        return $path;
    }
}
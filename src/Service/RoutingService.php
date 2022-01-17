<?php

namespace src\Service\RoutingService;

class RoutingService
{
    private array $routes;

    public function getRoute(string $path_code): string
    {
        $path = $this->codeIntoPath($path_code);
        if (!empty($path)) {
            return $path;
        }
        // Changes URL for every wrong route request.
        header("Location: /error");
    }

    private function codeIntoPath(string $path_code): ?string
    {
        if (empty($this->routes)) {
            $this->routes = yaml_parse_file("config/routes.yaml");
        }
        $path_parts = explode('-', $path_code);

        return $this->routes[$path_parts[0]][$path_parts[1]];
    }
}
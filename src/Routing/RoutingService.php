<?php

namespace src\Routing;

class RoutingService{
    private array $routes;

    public function getRoute($path_code) : string
    {
        $path = $this->codeIntoPath($path_code);

        if (!empty($path)) {
            return $path;
        }

        $_SESSION['error'] = '404 - not found';
        return $this->codeIntoPath('error-404');
    }

    private function codeIntoPath($path_code) : ?string
    {
        if(empty($this->routes)) {
            $this->getAllRoutes();
        }
        $path_parts = explode('-', $path_code);

        return $this->routes[$path_parts[0]][$path_parts[1]];
    }

    private function getAllRoutes() : void
    {
        $raw_routes = file_get_contents("config/routes.json");
        $this->routes = json_decode($raw_routes, true);
    }
}
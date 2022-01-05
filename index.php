<?php
session_start();

// -----  ROUTING -----
include('src/Service/RoutingService.php');

use src\Service\RoutingService\RoutingService;

if (!isset($GLOBALS['routingService'])) {
    $GLOBALS['routingService'] = new RoutingService();
}

// checking session after every redirection
require($GLOBALS['routingService']->getRoute('listener-session_checker'));

if (in_array($_SERVER['REQUEST_URI'], ['/api/start', '/api/stop', '/api/break', '/api/logout'])) {
    require($GLOBALS['routingService']->getRoute('api-listener'));

} else {
    if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] == '/main' || $_SERVER['REQUEST_URI'] == '/login') {
        if (isset($_SESSION['user_id'])) {
            $_SERVER['REQUEST_URI'] = '/main';
        } else {
            $_SERVER['REQUEST_URI'] = '/login';
        }
    }
    require($GLOBALS['routingService']->getRoute('default-' . $_SERVER['REQUEST_URI']));
}

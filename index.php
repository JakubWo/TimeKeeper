<?php
session_start();

include("src/Service/RoutingService.php");
use src\Service\RoutingService\RoutingService;

if(!isset($GLOBALS['routingService'])) {
    $_SESSION['siteMode'] = yaml_parse_file("config/parameters.yaml")['siteMode'];
//    $_SESSION['siteMode'] = 'PROD';
    $GLOBALS['routingService'] = new RoutingService();
}

if($_SERVER['REQUEST_URI'] === '/') {
    $_SERVER['REQUEST_URI'] .= 'login';
}

require($GLOBALS['routingService']->getRoute('default-'.$_SERVER['REQUEST_URI']));
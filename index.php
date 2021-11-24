<?php
session_start();

$configuration = yaml_parse_file("config/parameters.yaml");

$siteMode = $configuration['siteMode'];

include("src/Service/RoutingService/RoutingService.php");
//include("src/Service/ErrorService/ErrorService.php"); Czy nie powinien być odpalany w każdym miejscu osobno? Lepiej raczej.


use src\Service\RoutingService\RoutingService;
//use src\Service\ErrorService\ErrorService;
//use src\Service\DatabaseService\DatabaseService;

$routingService = new RoutingService();
//$errorService = new ErrorService();
//$dbService = new DatabaseService($configuration['database']);

if($_SERVER['REQUEST_URI'] === '/') {
    $_SERVER['REQUEST_URI'] .= 'login';
}

unset($configuration);
require($routingService->getRoute('default-'.$_SERVER['REQUEST_URI']));
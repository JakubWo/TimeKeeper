<?php

session_start();

include("src/Service/RoutingService/RoutingService.php");
use src\Service\RoutingService\RoutingService;
$routing = new RoutingService();


if($_SERVER['REQUEST_URI'] === '/'){
    require($routing->getRoute('template-login'));
} else {
    require($routing->getRoute('default-'.$_SERVER['REQUEST_URI']));
}

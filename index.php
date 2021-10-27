<?php

session_start();

include("src/Service/RoutingService/RoutingService.php");
use src\Service\RoutingService\RoutingService;
$routing = new RoutingService();

//echo $_SERVER['REQUEST_URI'];
//echo "<br>";
//echo 'default-'.$_SERVER['REQUEST_URI'];

//if(!empty(explode('/', $_SERVER['REQUEST_URI'])[2])){
//    echo $_SERVER['REQUEST_URI'];
//}

if($_SERVER['REQUEST_URI'] === '/'){
    require($routing->getRoute('template-login'));
} else {
    require($routing->getRoute('default-'.$_SERVER['REQUEST_URI']));
}
//include_once($routing->getRoute('tesmplate-main'));

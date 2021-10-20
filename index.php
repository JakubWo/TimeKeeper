<?php

include ("src/Routing/RoutingService.php");
use src\Routing\RoutingService;
$routing = new RoutingService();

include_once($routing->getRoute('tesmplate-main'));


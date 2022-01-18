<?php

// -----  ROUTING -----
require_once('src/Service/ErrorService.php');
require_once('src/Service/RoutingService.php');

use src\Service\RoutingService\RoutingService;

$routingService = new RoutingService();

require_once($routingService->getRoute('service-db'));
require_once($routingService->getRoute('service-api'));
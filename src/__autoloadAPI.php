<?php

include_once($GLOBALS['routingService']->getRoute('api-controller'));
include_once($GLOBALS['routingService']->getRoute('api-loginAction'));
include_once($GLOBALS['routingService']->getRoute('api-logoutAction'));
include_once($GLOBALS['routingService']->getRoute('api-startAction'));
include_once($GLOBALS['routingService']->getRoute('api-stopAction'));
include_once($GLOBALS['routingService']->getRoute('api-breakAction'));
include_once($GLOBALS['routingService']->getRoute('api-getWorkdaysAction'));
include_once($GLOBALS['routingService']->getRoute('api-getWorkdayAction'));
include_once($GLOBALS['routingService']->getRoute('api-getEventsAction'));
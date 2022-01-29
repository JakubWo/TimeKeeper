<?php

require_once($GLOBALS['routingService']->getRoute('api-controller'));
require_once($GLOBALS['routingService']->getRoute('api-loginAction'));
require_once($GLOBALS['routingService']->getRoute('api-logoutAction'));
require_once($GLOBALS['routingService']->getRoute('api-startAction'));
require_once($GLOBALS['routingService']->getRoute('api-stopAction'));
require_once($GLOBALS['routingService']->getRoute('api-breakAction'));
require_once($GLOBALS['routingService']->getRoute('api-getWorkdaysAction'));
require_once($GLOBALS['routingService']->getRoute('api-getWorkdayAction'));
require_once($GLOBALS['routingService']->getRoute('api-getEventsAction'));
require_once($GLOBALS['routingService']->getRoute('api-acceptWorkdayAction'));
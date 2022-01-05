<?php

include($GLOBALS['routingService']->getRoute('service-api'));

use src\Service\ApiService\ApiService;

run();


/*
 *
 */
function run()
{
    switch ($_SERVER['REQUEST_URI']) {
        case '/api/start':
            ApiService::start();
            break;
        case '/api/stop':
            ApiService::stop();
            break;
        case '/api/break':
            ApiService::break();
            break;
        case '/api/logout':
            ApiService::logout(); // tymczasowe rozwiązanie
    }
}
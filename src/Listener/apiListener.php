<?php

include($GLOBALS['routingService']->getRoute('service-db'));
include($GLOBALS['routingService']->getRoute('service-error'));
include($GLOBALS['routingService']->getRoute('service-api'));

use src\Service\DatabaseService\DatabaseService;
use src\Service\ErrorService\ErrorService;
use src\Service\ApiService\ApiService;


run();


/*
 *
 */
function run(): void
{
    $api_response = null;
    if (isset($_SESSION['user_id'])) {

        switch ($_SERVER['REQUEST_URI']) {
            case '/api/start':
                $api_response = ApiService::start();
                break;
            case '/api/stop':
                ApiService::stop();
                break;
            case '/api/break':
                ApiService::break();
                break;
            case '/api/logout':
                ApiService::logout(); // tymczasowe rozwiązanie
                break;
        }

    } else {
        $api_response['error'] = ErrorService::generate('No user is currently logged in');
    }

    header('Content-Type: application/json');
    echo json_encode($api_response);
}
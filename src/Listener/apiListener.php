<?php

include($GLOBALS['routingService']->getRoute('service-db'));
include($GLOBALS['routingService']->getRoute('service-error'));

include($GLOBALS['routingService']->getRoute('api-controller'));

include($GLOBALS['routingService']->getRoute('api-loginAction'));
include($GLOBALS['routingService']->getRoute('api-logoutAction'));
include($GLOBALS['routingService']->getRoute('api-startAction'));
include($GLOBALS['routingService']->getRoute('api-breakAction'));
include($GLOBALS['routingService']->getRoute('api-stopAction'));
include($GLOBALS['routingService']->getRoute('api-getWorkdaysAction'));
include($GLOBALS['routingService']->getRoute('api-getWorkdayAction'));

use src\API\ApiActions\getWorkdayAction;
use src\API\ApiActions\getWorkdaysAction;
use src\API\ApiController\ApiController;
use src\Service\ErrorService\ErrorService;
use src\API\ApiActions\loginAction;
use src\API\ApiActions\logoutAction;
use src\API\ApiActions\startAction;
use src\API\ApiActions\breakAction;
use src\API\ApiActions\stopAction;

run();

function run(): void
{
    $requestURI = $_SERVER['REQUEST_URI'];

    try {

        if (!isset($_SESSION['user_id']) && $requestURI === '/api/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $api_response = loginAction::run();

        } elseif (isset($_SESSION['user_id'])) {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                switch ($requestURI) {
                    case '/api/login':
                        $api_response = ApiController::errorResponse('Already logged in as ' . $_SESSION['username']);
                        break;
                    case '/api/start':
                        $api_response = startAction::run();
                        break;
                }

            } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'HEAD') {

                switch (explode('?', $_SERVER['REQUEST_URI'])[0]) {
                    case '/api/getWorkdays':
                        $api_response = getWorkdaysAction::run();
                        break;
                    case '/api/getWorkday':
                        $api_response = getWorkdayAction::run();
                        break;
                }

            } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                switch ($requestURI) {
                    case '/api/break':
                        $api_response = breakAction::run();
                        break;
                    case '/api/stop':
                        $api_response = stopAction::run();
                        break;
                }

            } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                switch ($requestURI) {
                    case '/api/logout':
                        $api_response = logoutAction::run();
                        break;
                }

            } else {
                $api_response = ApiController::errorResponse('Unsupported http request method', 501);
            }
            if (empty($api_response)) {
                $api_response = ApiController::errorResponse('Undefined action', 404);
            }

        } else {
            $api_response = ApiController::errorResponse('You have to be logged in to use API', 401);
        }

    } catch
    (Exception $exception) {
        ErrorService::generate(
            'API action failed.',
            $exception->getMessage(),
            $exception->getTrace(),
            true
        );

        $api_response = ApiController::errorResponse('Unexpected error', 500);
    }

    $response = json_encode(["response" => $api_response]);

    header('Content-Type: application/json');
    header('Content-Length: ' . strlen($response));
    header('charset=utf-8');

    echo $response;
}
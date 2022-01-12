<?php

include($GLOBALS['routingService']->getRoute('service-api'));
include($GLOBALS['routingService']->getRoute('service-db'));
include($GLOBALS['routingService']->getRoute('service-error'));

include($GLOBALS['routingService']->getRoute('api-loginAction'));
include($GLOBALS['routingService']->getRoute('api-logoutAction'));
include($GLOBALS['routingService']->getRoute('api-startAction'));
include($GLOBALS['routingService']->getRoute('api-breakAction'));
include($GLOBALS['routingService']->getRoute('api-stopAction'));

use src\Service\ApiService\ApiService;
use src\Service\ErrorService\ErrorService;
use src\Service\ApiActions\loginAction;
use src\Service\ApiActions\logoutAction;
use src\Service\ApiActions\startAction;
use src\Service\ApiActions\breakAction;
use src\Service\ApiActions\stopAction;

run();

function run(): void
{
    try {
        if (!isset($_SESSION['user_id']) && $_SERVER['REQUEST_URI'] === '/api/login') {
            $api_response = loginAction::run();

        } elseif (isset($_SESSION['user_id'])) {
            switch ($_SERVER['REQUEST_URI']) {
                case '/api/login':
                    $api_response = ApiService::errorResponse('Already logged in');
                    break;
                case '/api/logout':
                    $api_response = logoutAction::run();
                    break;
                case '/api/start':
                    $api_response = startAction::run();
                    break;
                case '/api/stop':
                    $api_response = stopAction::run();
                    break;
                case '/api/break':
                    $api_response = breakAction::run();
                    break;
                default:
                    $api_response = ApiService::errorResponse('Undefined action');
            }

        } else {
            $api_response = ApiService::errorResponse('You have to be logged in to use API');
        }

    } catch (PDOException $PDOException) {
        ErrorService::generate(
            'API action failed.',
            $PDOException->getMessage(),
            $PDOException->getTrace(),
            true
        );

        $api_response = ApiService::errorResponse('Database error');

    } catch (Exception $exception) {
        $exceptionMessage = $exception->getMessage();

        ErrorService::generate(
            'API action failed.',
            $exceptionMessage,
            $exception->getTrace(),
            true
        );

        $api_response = ApiService::errorResponse('Unexpected error');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["response" => $api_response]);
}
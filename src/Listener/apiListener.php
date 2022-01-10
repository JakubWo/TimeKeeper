<?php

include($GLOBALS['routingService']->getRoute('service-db'));
include($GLOBALS['routingService']->getRoute('service-error'));
include($GLOBALS['routingService']->getRoute('service-api'));

use src\Service\ApiService\ApiService;
use src\Service\ErrorService\ErrorService;

run();

/*
 *
 */
function run(): void
{
    try {
        $api_response = null;
        if (isset($_SESSION['user_id'])) {

            switch ($_SERVER['REQUEST_URI']) {
                case '/api/start':
                    $api_response = ApiService::start();
                    break;
                case '/api/stop':
                    $api_response = ApiService::stop();
                    break;
                case '/api/break':
                    $api_response = ApiService::break();
                    break;
                case '/api/logout':
                    $api_response = ApiService::logout();
                    break;
            }

        } else {
            $api_response = [
                'error' => [
                    'title' => 'No user is currently logged in'
                ]
            ];
        }

    } catch (PDOException $PDOException) {
        ErrorService::generate(
            'API action failed.',
            $PDOException->getMessage(),
            $PDOException->getTrace(),
            true
        );

        $api_response = [
            'error' => [
                'title' => $PDOException->errorInfo[2]
            ]
        ];

    } catch (Exception $exception) {
        ErrorService::generate(
            'API action failed.',
            $exception->getMessage(),
            $exception->getTrace(),
            true
        );

        $api_response = [
            'error' => [
                'title' => $exception->getMessage()
            ]
        ];
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["response" => $api_response]);
}
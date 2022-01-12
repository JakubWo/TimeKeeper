<?php

include($GLOBALS['routingService']->getRoute('service-db'));
include($GLOBALS['routingService']->getRoute('service-error'));
include($GLOBALS['routingService']->getRoute('service-api'));
include($GLOBALS['routingService']->getRoute('service-auth'));

use src\Service\ApiService\ApiService;
use src\Service\ErrorService\ErrorService;

run();

/*
 *
 */
function run(): void
{
    try {
        if (!isset($_SESSION['user_id']) && $_SERVER['REQUEST_URI'] === '/api/login') {
            $api_response = ApiService::login();

        } elseif (isset($_SESSION['user_id'])) {
            switch ($_SERVER['REQUEST_URI']) {
                case '/api/login':
                    $api_response = [
                        'error' => [
                            'title' => 'Already logged in'
                        ]
                    ];
                    break;
                case '/api/logout':
                    $api_response = ApiService::logout();
                    break;
                case '/api/start':
                    $api_response = ApiService::start();
                    break;
                case '/api/stop':
                    $api_response = ApiService::stop();
                    break;
                case '/api/break':
                    $api_response = ApiService::break();
                    break;
                default:
                    throw new Exception('Undefined action');
            }

        } else {
            $api_response = [
                'error' => [
                    'title' => 'You have to be logged in to use API'
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
                'title' => 'Database error'
            ]
        ];

    } catch (Exception $exception) {
        $exceptionMessage = $exception->getMessage();

        ErrorService::generate(
            'API action failed.',
            $exceptionMessage,
            $exception->getTrace(),
            true
        );

        if (strpos($exceptionMessage, 'Action failed') !== 0) {
            $exceptionMessage = 'Action failed';
        }

        $api_response = [
            'error' => [
                'title' => $exceptionMessage,
            ]
        ];
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["response" => $api_response]);
}
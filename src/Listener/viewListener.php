<?php

use src\Service\ErrorService\ErrorService;
use src\Service\ViewGeneratorService\ViewGeneratorService;

run();
function run()
{
    // With manipulation of headers, someone can break in here, but it is public anyways.
    // No data leak can happen here, but it's blocked just because it's ugly.
    if (
        !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest' ||
        strpos($_SERVER['HTTP_REFERER'], getenv('HTTP_HOST')) === false ||
        !isset($_SESSION['user_id']) ||
        $_SERVER['REQUEST_METHOD'] !== 'GET'
    ) {
        header('Location: /error');
    }

    try {
        $generatedView = null;
        $requestURI = $_SERVER['REQUEST_URI'];

        switch (explode('?', $requestURI)[0]) {
            case '/view/workday':
                $generatedView = ViewGeneratorService::generateWorkday();
                break;
            case '/view/event':
                $generatedView = ViewGeneratorService::generateEvent();
                break;
        }

        if (empty($generatedView)) {
            $generatedView = ViewGeneratorService::errorResponse('Unresolved view');
        }

    } catch (Exception $exception) {
        ErrorService::generate(
            'View generation failed.',
            $exception->getMessage(),
            $exception->getTrace(),
            true
        );

        $generatedView = ViewGeneratorService::errorResponse($exception->getMessage());
    }


    echo $generatedView;

}
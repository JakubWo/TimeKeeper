<?php

// listens to \login_authorization

include($GLOBALS['routingService']->getRoute('service-auth'));
include($GLOBALS['routingService']->getRoute('service-db'));
include($GLOBALS['routingService']->getRoute('service-error'));

use src\Service\AuthService\AuthService;
use src\Service\DatabaseService\DatabaseService;
use src\Service\ErrorService\ErrorService;

authenticateUser();

function authenticateUser(): void
{
    $GLOBALS['dbService'] = new DatabaseService();
    AuthService::authenticate();

    if (!$_SESSION['isLoggedIn']) {
        unset($GLOBALS['dbService']);
        unset($_SESSION['isLoggedIn']);

        $_SESSION['login_result'] = 'Login and/or password is incorrect.';
        header('Location: /login');

    } else {
        // Just to see login result.
        ErrorService::manualError(
            'Logged in',
            $_SESSION['isLoggedIn'],
            'User id: ' . $_SESSION['user_id']
        );
        header('Location: /error');
    }

}

<?php

// listens to \login_authorization

include($GLOBALS['routingService']->getRoute('service-auth'));
include($GLOBALS['routingService']->getRoute('service-db'));

use src\Service\AuthService\AuthService;

authenticateUser();

function authenticateUser(): void
{
    AuthService::authenticate();

    if (!$_SESSION['isLoggedIn']) {
        unset($GLOBALS['dbService']);
        unset($_SESSION['isLoggedIn']);

        $_SESSION['loginResult'] = 'Login and/or password is incorrect.';
    } else {
        session_regenerate_id(true);
    }
    header('Location: /');
}

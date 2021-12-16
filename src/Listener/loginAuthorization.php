<?php

// listens to \login_authorization

include($GLOBALS['routingService']->getRoute('service-auth'));
include($GLOBALS['routingService']->getRoute('service-db'));

use src\Service\AuthService\AuthService;
use src\Service\DatabaseService\DatabaseService;

authenticateUser();

function authenticateUser() : void {
    $GLOBALS['dbService'] = new DatabaseService(
        yaml_parse_file($GLOBALS['routingService']->getRoute('config-parameters'))['database']);

    AuthService::authenticate();

    if(!$_SESSION['isLoggedIn']){
        unset($GLOBALS['dbService']);
        unset($_SESSION['isLoggedIn']);
        $_SESSION['login_result'] = 'Login and/or password is incorrect.';
        header('Location: /login');

    } else {

        $_SESSION['error']['title'] = 'Logged in';
        $_SESSION['error']['message'] = $_SESSION['isLoggedIn'];
        $_SESSION['error']['details'] = 'User id: '.$_SESSION['user_id'];
        header('Location: /error');
    }

}

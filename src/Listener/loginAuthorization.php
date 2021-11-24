<?php

include($GLOBALS['routingService']->getRoute('service-auth'));
include($GLOBALS['routingService']->getRoute('service-db'));

use src\Service\AuthService\AuthService;
use src\Service\DatabaseService\DatabaseService;

authenticateUser($_POST);

function authenticateUser(array $login_params){
    $authService = new AuthService();
    $GLOBALS['dbService'] = new DatabaseService(yaml_parse_file($GLOBALS['routingService']->getRoute('config-parameters'))['database']);

    if(!$authService->authenticate($login_params)){
        unset($GLOBALS['dbService']);
        $_SESSION['login_result'] = "Couldn't log in";
        require($GLOBALS['routingService']->getRoute('default-/login'));
    }

    print_r("END");die;
//    require($GLOBALS['routingService']->getRoute('default-/error'));

}

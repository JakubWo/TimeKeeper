<?php
session_start();
require_once('src/__autoload.php');

// checking session after every redirection
require($GLOBALS['routingService']->getRoute('listener-session_checker'));


if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    require_once('src/__autoloadAPI.php');
    require_once($GLOBALS['routingService']->getRoute('listener-api'));
} else {
    if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] == '/main' || $_SERVER['REQUEST_URI'] == '/login') {
        if (isset($_SESSION['user_id'])) {
            $_SERVER['REQUEST_URI'] = '/main';
        } else {
            $_SERVER['REQUEST_URI'] = '/login';
        }
    }
    require_once($GLOBALS['routingService']->getRoute('default-' . $_SERVER['REQUEST_URI']));
}

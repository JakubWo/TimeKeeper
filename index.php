<?php
// -----  SESSION -----
// Create new session if there is no creationTime.
if (!isset($_SESSION['creationTime'])) {
    session_start();
    $_SESSION['creationTime'] = time();

// Delete old and create new session if current session lasts longer than hour.
} else if (time() - $_SESSION['creationTime'] > 3600) {
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['creationTime'] = time();

// If session last activity was 30 minutes ago delete old and create new session.
} else if (time() - $_SESSION['lastActivityTime'] > 1800) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['creationTime'] = time();

// If session last activity was 15 minutes ago change session ID.
} else if (time() - $_SESSION['lastActivityTime'] > 900) {
    session_regenerate_id(true);
}
$_SESSION['lastActivityTime'] = time();
// ----- /SESSION -----


// -----  ROUTING -----
include("src/Service/RoutingService.php");

use src\Service\RoutingService\RoutingService;

if (!isset($GLOBALS['routingService'])) {
    $_SESSION['siteMode'] = yaml_parse_file("config/parameters.yaml")['siteMode'];
    $GLOBALS['routingService'] = new RoutingService();
}

if ($_SERVER['REQUEST_URI'] === '/') {
    $_SERVER['REQUEST_URI'] .= 'login';
}

require($GLOBALS['routingService']->getRoute('default-' . $_SERVER['REQUEST_URI']));
// ----- /ROUTING -----

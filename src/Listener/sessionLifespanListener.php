<?php

// Create new session if there is no creationTime.
if (!isset($_SESSION['creationTime'])) {
    session_start();
    $_SESSION['creationTime'] = time();
    $_SESSION['siteMode'] = yaml_parse_file("config/parameters.yaml")['siteMode'];

// Delete old and create new session if current session lasts longer than 8h 30min (30600s).
} else if (time() - $_SESSION['creationTime'] > 30600) {
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['creationTime'] = time();
    $_SESSION['siteMode'] = yaml_parse_file("config/parameters.yaml")['siteMode'];

// If session last activity was 30(1800s) minutes ago delete old and create new session.
} else if (time() - $_SESSION['lastActivityTime'] > 1800) {
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['creationTime'] = time();
    $_SESSION['siteMode'] = yaml_parse_file("config/parameters.yaml")['siteMode'];
}

$_SESSION['lastActivityTime'] = time();
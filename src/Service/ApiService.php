<?php

namespace src\Service\ApiService;

use src\Service\DatabaseService\DatabaseService;
use src\Service\ErrorService\ErrorService;

class ApiService
{
    //TYMCZASOWO
    public static function logout()
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        // przy loginie logoutcie musi być
        session_regenerate_id(true);
    }

    public static function start()
    {

    }

    public static function stop()
    {
    }

    public static function break()
    {
    }
}
<?php

namespace src\Service\ApiActions;

use src\Service\ApiService\ApiService;

class logoutAction extends ApiService
{
    public static function run(): array
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);

        return parent::successResponse('Logged out');
    }
}
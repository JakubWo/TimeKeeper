<?php

namespace src\API\ApiActions;

use src\API\ApiController\ApiController;

class logoutAction extends ApiController
{
    public static function run(): array
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);

        return parent::successPostResponse('Logged out');
    }
}
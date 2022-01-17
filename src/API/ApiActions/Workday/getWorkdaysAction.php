<?php

namespace src\API\ApiActions;

use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class getWorkdaysAction extends ApiController
{
    public static function run(): array
    {
        $dbService = new DatabaseService();
        $userId = $_SESSION['user_id'];

        $amountOfWorkdays = null;
        if (isset($_GET['last'])) {
            $amountOfWorkdays = filter_input(
                INPUT_GET,
                'last',
                FILTER_VALIDATE_INT,
                FILTER_NULL_ON_FAILURE
            );

            if ($amountOfWorkdays === null || $amountOfWorkdays < 1) {
                return self::errorResponse('Invalid last parameter');
            }
        }

        $workdays = $dbService->getWorkdays($userId, $amountOfWorkdays);

        for ($i = 0; $i < count($workdays); $i++) {
            $workdays[$i]['notes'] = self::notesToArray($workdays[$i]['notes']);
        }

        return $workdays;
    }
}
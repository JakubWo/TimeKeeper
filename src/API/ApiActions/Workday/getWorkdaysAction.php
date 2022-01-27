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

        $amountOfWorkdays = filter_input(
            INPUT_GET,
            'last',
            FILTER_VALIDATE_INT
        );

        $startingFrom = filter_input(
            INPUT_GET,
            'from',
            FILTER_VALIDATE_INT
        );

        if ($amountOfWorkdays === false || ($amountOfWorkdays < 1 && $amountOfWorkdays !== null)) {
            return self::errorResponse('Invalid last parameter');
        } elseif ($startingFrom === false || $startingFrom < 0) {
            return self::errorResponse('Invalid from parameter');
        }

        $workdays = $dbService->getWorkdays($userId, $amountOfWorkdays, $startingFrom);

        for ($i = 0; $i < count($workdays); $i++) {
            $workdays[$i]['notes'] = self::notesToArray($workdays[$i]['notes']);
        }
        return $workdays;
    }
}
<?php

namespace src\API\ApiActions;

use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class getEventsAction extends ApiController
{
    public static function run(): array
    {
        $dbService = new DatabaseService();
        $userId = $_SESSION['user_id'];

        $workdayId = filter_input(
            INPUT_GET,
            'workday_id',
            FILTER_VALIDATE_INT,
        );

        if (empty($workdayId) || $dbService->getWorkdaysById([$workdayId])[0]['user_id'] != $userId) {
            return self::errorResponse('Invalid workday_id parameter');
        }

        return self::reformatEvents($dbService->getWorkdayEvents($workdayId));
    }
}
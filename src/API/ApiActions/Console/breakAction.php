<?php

namespace src\API\ApiActions;

use Exception;
use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class breakAction extends ApiController
{
    /**
     * @throws Exception
     */
    public static function run(): array
    {
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $workdayId = $dbService->getWorkdays($userId, 1)[0]['workday_id'];
        $lastEventType = $dbService->getWorkdayEvents($workdayId)[0]['event_type'];

        if ($lastEventType === 'break') {
            return self::errorResponse('Already on break');
        } elseif ($lastEventType === 'stop') {
            return self::errorResponse('Cannot take break before starting or after ending a workday');
        }

        if (!$dbService->breakEvent($workdayId)) {
            return self::errorResponse('Action failed');
        }

        return self::successPostResponse('Start break', 200, []);
    }

}
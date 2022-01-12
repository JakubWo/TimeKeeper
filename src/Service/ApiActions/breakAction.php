<?php

namespace src\Service\ApiActions;

use Exception;
use src\Service\ApiService\ApiService;
use src\Service\DatabaseService\DatabaseService;

class breakAction extends ApiService
{
    /**
     * @throws Exception
     */
    public static function run(): array
    {
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $workdayId = $dbService->getUserLastWorkdayId($userId);
        $lastEventType = $dbService->getWorkdayLastEventType($workdayId);

        if ($lastEventType === 'break') {
            return self::errorResponse('Already on break');
        } elseif ($lastEventType === 'stop') {
            return self::errorResponse('Cannot take break before starting or after ending a workday');
        }

        if (!$dbService->breakEvent($workdayId)) {
            return self::errorResponse('Action failed');
        }

        http_response_code(201);
        return self::successResponse('Start break');
    }

}
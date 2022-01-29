<?php

namespace src\API\ApiActions;

use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class getWorkdayAction extends ApiController
{
    public static function run(): array
    {
        if (!isset($_GET['workday_id'])) {
            return self::errorResponse('Invalid workday id parameter');
        }

        $input = $_GET['workday_id'];

        if (!is_array($input)) {
            $workday_ids[] = $input;
        } else {
            $workday_ids = $input;
        }

        $dbService = new DatabaseService();
        $userId = $_SESSION['user_id'];
        $ids = [];

        foreach ($workday_ids as $id) {
            $current_id = filter_var(
                $id,
                FILTER_VALIDATE_INT,
            );
            if ($current_id === false) {
                return self::errorResponse('Invalid workday id parameter: ' . $id);
            }

            $ids[] = $current_id;
        }

        $workdays = $dbService->getWorkdaysById($ids);
        for ($i = 0; $i < count($workdays); $i++) {
            if ($userId != $workdays[$i]['user_id']) {
                return self::errorResponse('Invalid workday id parameter: ' . $workdays[$i]['workday_id'], 403);
            }

            $workdays[$i]['notes'] = self::notesToArray($workdays[$i]['notes']);
            unset($workdays[$i]['user_id']);
        }

        if (empty($workdays)) {
            return self::errorResponse('Invalid workday parameter/s.', 403);
        }
        return $workdays;
    }
}
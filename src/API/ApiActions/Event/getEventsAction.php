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

        if (empty($workdayId)) {
            return self::errorResponse('Invalid workday_id parameter');
        }

        $targetUserId = $dbService->getWorkdaysById([$workdayId])[0]['user_id'];
        if ($targetUserId != $userId) {
            if ($_SESSION['user']['privileges'] === false) {
                return self::errorResponse('Permission denied', 403);
            }

            $selectedUserTeam = $dbService->getTeamByMemberId($targetUserId)['team_name'];
            $loggedUserTeamsPermissions = $dbService->getTeamsPermissions($userId);

            $isFound = false;
            foreach ($loggedUserTeamsPermissions as $teamsPermission) {
                if ($teamsPermission['team_name'] === $selectedUserTeam) {
                    if ($teamsPermission['permission'] === 'member') {
                        return self::errorResponse('Permission denied.', 403);
                    }
                    $isFound = true;
                }
            }

            if ($isFound === false) {
                return self::errorResponse('Permission denied.', 403);
            }
        }

        return self::reformatEvents($dbService->getWorkdayEvents($workdayId));
    }
}
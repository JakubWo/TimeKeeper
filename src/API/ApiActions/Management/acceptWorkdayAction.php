<?php

namespace src\API\ApiActions;

use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class acceptWorkdayAction extends ApiController
{
    public static function run(): array
    {
        $dbService = new DatabaseService();
        $userId = $_SESSION['user_id'];

        if ($_SESSION['user']['privileges'] === false) {
            return self::errorResponse('Permission denied', 403);
        }

        $workdayId = filter_input(
            INPUT_POST, 'workday_id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if (empty($workdayId)) {
            return self::errorResponse("Invalid workday_id parameter.");
        }

        $workday = $dbService->getWorkdaysById([$workdayId])[0];

        $targetUserId = $workday['user_id'];
        $targetUserTeam = $dbService->getTeamByMemberId($targetUserId)['team_name'];

        $loggedUserTeamsPermissions = $dbService->getTeamsPermissions($userId);
        $isFound = false;

        foreach ($loggedUserTeamsPermissions as $teamsPermission) {
            if ($teamsPermission['team_name'] === $targetUserTeam) {
                if (!in_array($teamsPermission['permission'], ['supervisor', 'manager'])) {
                    return self::errorResponse('Permission denied.', 403);
                }
                $isFound = true;
            }
        }

        if ($isFound === false) {
            return self::errorResponse('Permission denied.', 403);
        }

        if ($workday['is_accepted'] === '1') {
            return self::errorResponse("Already accepted.");
        }

        if (!$dbService->acceptWorkday($workdayId)) {
            return self::errorResponse('Action failed');
        }

        return parent::successPostResponse('Accepted');
    }
}
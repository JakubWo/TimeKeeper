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

        $selectedUsername = filter_input(
            INPUT_GET,
            'username',
            FILTER_SANITIZE_STRING
        );

        if ($amountOfWorkdays === false || ($amountOfWorkdays < 1 && $amountOfWorkdays !== null)) {
            return self::errorResponse('Invalid last parameter');
        } elseif ($startingFrom === false || $startingFrom < 0) {
            return self::errorResponse('Invalid from parameter');
        }

        if ($selectedUsername !== null) {
            if ($_SESSION['user']['privileges'] === false) {
                return self::errorResponse('Permission denied', 403);
            } elseif (empty($selectedUsername)) {
                return self::errorResponse('Invalid user name parameter');
            }

            $selectedUserTeam = $dbService->getTeamByMemberUsername($selectedUsername)['team_name'];
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

            $userId = $dbService->getOtherId($selectedUsername);
        }


        $workdays = $dbService->getWorkdays($userId, $amountOfWorkdays, $startingFrom);

        for ($i = 0; $i < count($workdays); $i++) {
            $workdays[$i]['notes'] = self::notesToArray($workdays[$i]['notes']);

            if ($workdays[$i]['total_work_time'] !== null) {
                $time = self::secondsToTime($workdays[$i]['total_work_time']);
                $workdays[$i]['total_work_time'] = sprintf('%02d:%02d:%02d', $time['hours'], $time['minutes'], $time['seconds']);
            } else {
                $workdays[$i]['total_work_time'] = "in progress";
            }
        }

        return $workdays;
    }
}
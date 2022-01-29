<?php

namespace src\Service\ViewGeneratorService;

use DateTime;
use DateTimeZone;
use src\Service\DatabaseService\DatabaseService;
use src\Service\ApiService\ApiService;

class ViewGeneratorService
{
    public static function generateWorkday(): string
    {
        $amountOfWorkdays = filter_input(
            INPUT_GET, 'amountOfWorkdays', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        $timesAppended = filter_input(
            INPUT_GET, 'timesAppended', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        $action = filter_input(
            INPUT_GET, 'action', FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);

        $username = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING);

        $rowStyle = filter_input(INPUT_GET, 'rowStyle', FILTER_VALIDATE_INT);

        if (empty($amountOfWorkdays) || $amountOfWorkdays < 1 || $timesAppended === null || empty($action) ||
            $rowStyle === false || $rowStyle < 0 || $rowStyle > 1 || $username === false) {
            return self::errorResponse('Generator error');
        }

        $from = $timesAppended * $amountOfWorkdays;

        if ($action === 'update' || $action === 'start') {
            $from = 0;
        }

        $requestOptions = ['last' => $amountOfWorkdays, 'from' => $from];
        if (!empty($username)) {
            $requestOptions['username'] = $username;
        }

        $workdays = ApiService::request('getWorkdays', $requestOptions);

        if (empty($workdays)) {
            if ($action === 'append') {
                // First load
                if ($timesAppended === 0) {
                    return self::errorResponse("Empty workday history, or some unexpected error occurred.");
                }
                return self::errorResponse("No more records to show.");
            }
            return self::errorResponse("Empty workday history, or some unexpected error occurred.");
        }

        $dateTime = new DateTime();

        $rows = null;
        foreach ($workdays as $workday) {
            $notes = null;
            foreach ($workday['notes'] as $key => $val) {
                $notes .= "$key: $val<br>";
            }
            $workday['notes'] = $notes;

            $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
            $dateTime->setTimestamp(strtotime($workday['workday_date']));
            $dateTime->setTimezone(new DateTimeZone($_SESSION['user']['timezone']));

            $workday['workday_date'] = $dateTime->format('Y-m-d H:i:s');
            $workday['workday_type'] = strtoupper($workday['workday_type']);

            $dbService = new DatabaseService();

            $selectedUserTeam = $dbService->getTeamByMemberUsername($username)['team_name'];
            $loggedUserTeamsPermissions = $dbService->getTeamsPermissions($_SESSION['user_id']);

            $isFound = false;
            foreach ($loggedUserTeamsPermissions as $teamsPermission) {
                if ($teamsPermission['team_name'] === $selectedUserTeam) {
                    if (in_array($teamsPermission['permission'], ['supervisor', 'manager'])) {
                        $isFound = true;
                    }
                    break;
                }
            }


            if (!$workday['is_accepted']) {
                if ($_SESSION['user']['privileges'] === true && $isFound) {
                    $workday['is_accepted'] = '<td><img class="img" src="' . $GLOBALS['routingService']->getRoute('image-notAccepted') .
                        '" alt="x"></td><td><button class="acceptation_button">Accept</button></td>';
                } else {
                    $workday['is_accepted'] = '<td class="is_accepted"><img class="img" src="' .
                        $GLOBALS['routingService']->getRoute('image-notAccepted') . '" alt="x"></td>';
                }

            } else {
                $workday['is_accepted'] = '<td class="is_accepted"><img class="img" src="'
                    . $GLOBALS['routingService']->getRoute('image-accepted') . '" alt="v"></td>';
            }


            $rows .= '<details class="row' . $rowStyle % 2 . ' workday_row workday_' . $workday['workday_id'] . '">' .
                '<summary><table><tr>';

            foreach (['workday_id', 'workday_date', 'total_work_time', 'workday_type', 'notes'] as $header) {
                $rows .= " <td>$workday[$header]</td> ";
            }

            $rows .= $workday['is_accepted'] . '</tr></table></summary><span></span></details>';
            $rowStyle += 1;
        }

        return $rows;
    }

    public
    static function generateEvent(): string
    {
        $workdayId = filter_input(
            INPUT_GET, 'workday_id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if (empty($workdayId)) {
            return self::errorResponse("Generator error");
        }

        $events = ApiService::request('getEvents', ['workday_id' => $workdayId]);

        $row = '<table class="event_table"><tr><th>Time</th><th>Event</th></tr>';
        foreach ($events as $date => $event) {
            foreach ($event as $item) {
                $row .= '<tr><td>' . $date . '</td><td>' . $item . '</td></tr>';
            }
        }

        return $row . '</table>';
    }

    public
    static function errorResponse(string $reason = 'Unknown error', int $code = 500): string
    {
        http_response_code($code);
        return $reason;
    }
}
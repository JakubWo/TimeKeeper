<?php

namespace src\Service\ApiService;

use DateTime;
use DateTimeZone;
use Exception;
use src\Service\AuthService\AuthService;
use src\Service\DatabaseService\DatabaseService;

class ApiService
{
    // constants and configurations
    // should be moved somewhere else, or kept there for now
    private const REGULAR = 1;
    private const IRREGULAR = 2;

    private const REGULARITY_MAX_DIFF_STARTING_TIME = 900;
    private const REGULARITY_MAX_WORK_TIME_DIFF = 3600;
    private const REGULARITY_MAX_BREAK_TIME = 3600;

    private const STANDARD_WORK_TIME = 28800;
    private const MAX_BREAK_TIME = 900;


    public static function login(): array
    {
        return AuthService::authenticate();
    }

    public static function logout(): array
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);

        return [
            'result' => 'Success',
            'action' => 'Logged out',
            'data' => []
        ];
    }

    /**
     * @throws Exception
     */
    public static function start(): array
    {
        $dateTime = new DateTime();
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $lastWorkdayId = $dbService->getUserLastWorkdayId($userId);
        $lastEventType = $dbService->getWorkdayLastEventType($lastWorkdayId);

        if ($lastEventType === 'start') {
            throw new Exception('Action failed: Cannot make another start event before ending last workday');
        } elseif ($lastEventType === 'break') {
            if ($dbService->stopBreakEvent($lastWorkdayId)) {
                http_response_code(201);
                return [
                    'result' => 'Success',
                    'action' => 'Stop break',
                    'data' => []
                ];
            } else {
                throw new Exception('Action failed');
            }
        }

        $userInputTimeZone = $_POST['time_zone'];
        $userInputTime = $_POST['time_input'];

        $userDbTimeZone = $dbService->getUserTimeZone($userId);
        if ($userDbTimeZone === null) {
            throw new Exception('Action failed: Database error');
        }

        $userTime = $dateTime->format('Y-m-d H:i:s');

        if (!empty($userInputTimeZone)) {
            if (!is_string($userInputTimeZone)) {
                throw new Exception('Action failed: Invalid time_zone format');
            } else {
                $dateTime->setTimezone(new DateTimeZone($userInputTimeZone));
            }
        } else {
            $dateTime->setTimezone(new DateTimeZone($userDbTimeZone));
        }


        $note = [];
        $isWorkdayAccepted = 1;
        $workdayType = self::REGULAR;
        if (!empty($userInputTime)) {
            $workdayType = self::IRREGULAR;

            $note['CustomStart'] = strtotime($userInputTime) - strtotime('00:00');
            if (!preg_match('/^\d{2}:\d{2}$/', $userInputTime)) {
                throw new Exception('Action failed: Invalid custom time input');
            }

            $userInputTimeParts = explode(':', $userInputTime);
            $dateTime->setTime($userInputTimeParts[0], $userInputTimeParts[1], '00');
            $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

            $userInputTime = $dateTime->format('Y-m-d H:i:s');

            $timeOff = abs(strtotime($userTime) - strtotime($userInputTime));
            if ($timeOff > self::REGULARITY_MAX_DIFF_STARTING_TIME) {
                $note['CustomStartTimeOff'] = $timeOff;
                $isWorkdayAccepted = 0;
            }
        } else {
            $userInputTime = $userTime;
        }

        if (!empty($userInputTimeZone) && $userInputTimeZone !== $userDbTimeZone) {
            $note['DifferentTimezone'] = $userInputTimeZone;
            $workdayType = self::IRREGULAR;
        }

        if (!$dbService->startEvent([
            $userId,
            $userInputTime,
            $workdayType,
            $isWorkdayAccepted,
            self::notesToString($note)
        ])) {
            throw new Exception('Action failed');
        }

        http_response_code(201);
        return [
            'result' => 'Success',
            'action' => 'Start workday',
            'data' => [
                'start_time' => $userInputTime,
                'workday_type' => $workdayType,
                'is_accepted' => $isWorkdayAccepted,
                'notes' => $note
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public static function break(): array
    {
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $workdayId = $dbService->getUserLastWorkdayId($userId);
        $lastEventType = $dbService->getWorkdayLastEventType($workdayId);

        if ($lastEventType === 'break') {
            throw new Exception('Action failed: Already on break');
        } elseif ($lastEventType === 'stop') {
            throw new Exception('Action failed: Cannot take break before starting or after ending a workday');
        }

        if (!$dbService->breakEvent($workdayId)) {
            throw new Exception('Action failed');
        }

        http_response_code(201);
        return [
            'result' => 'Success',
            'action' => 'Start break',
            'data' => []
        ];
    }

    /**
     * @throws Exception
     */
    public static function stop(): array
    {
        $dateTime = new DateTime();
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $workdayId = $dbService->getWorkdays($userId, 1)[0]['workday_id'];; // swap that with get workdays
        $lastWorkdayEvents = $dbService->getWorkdayEvents($workdayId);

        $times[] = $dateTime->format('Y-m-d H:i:s');
        if ($lastWorkdayEvents[0]['event_type'] === 'stop') {
            throw new Exception('Action Failed: Cannot stop not started workday');
        }

        foreach ($lastWorkdayEvents as $event) {
            $times[] = $event['event_timestamp'];
        }

        $totalWorkTimeInSeconds = 0;
        $totalBreakTimeInSeconds = 0;

        $numberOfTimes = count($times);

        for ($i = $numberOfTimes % 2; $i < $numberOfTimes; $i += 2) {
            $totalWorkTimeInSeconds += (strtotime($times[$i]) - strtotime($times[$i + 1]));
        }
        for ($i = ($numberOfTimes + 1) % 2; $i < $numberOfTimes - 1; $i += 2) {
            $totalBreakTimeInSeconds += (strtotime($times[$i]) - strtotime($times[$i + 1]));
        }

        $totalWorkTimeInSeconds += min([self::MAX_BREAK_TIME, $totalBreakTimeInSeconds]);

        $note = [];
        $workdayType = self::REGULAR;
        $isWorkdayAccepted = 1;
        if ($totalBreakTimeInSeconds > self::REGULARITY_MAX_BREAK_TIME) {
            $note['LongBreak'] = $totalBreakTimeInSeconds;
            $workdayType = self::IRREGULAR;
        }

        if ($totalWorkTimeInSeconds - self::STANDARD_WORK_TIME > self::REGULARITY_MAX_WORK_TIME_DIFF) {
            $note['OverWork'] = $totalWorkTimeInSeconds - self::REGULARITY_MAX_WORK_TIME_DIFF;
            $workdayType = self::IRREGULAR;

        } elseif (self::STANDARD_WORK_TIME - $totalWorkTimeInSeconds > self::REGULARITY_MAX_WORK_TIME_DIFF) {
            $note['UnderHours'] = self::STANDARD_WORK_TIME - $totalWorkTimeInSeconds;
            $workdayType = self::IRREGULAR;
            $isWorkdayAccepted = 0;

        } elseif ($totalWorkTimeInSeconds < self::STANDARD_WORK_TIME) {
            $note['UnderHours'] = self::STANDARD_WORK_TIME - $totalWorkTimeInSeconds;
            $workdayType = self::IRREGULAR;
        }


        // swap STOP EVENT db procedure actions to check things in here
//        $workday = $dbService->getWorkdays($userId, 1);

//        if ($workday[0]['workday_type'] === 'irregular') {
//            echo "XD";
//        }
//        die;


        if (!$dbService->stopEvent([
            $workdayId,
            $totalWorkTimeInSeconds,
            $workdayType,
            $isWorkdayAccepted,
            self::notesToString($note)
        ])) {
            throw new Exception('Action failed');
        }


        http_response_code(201);
        return [
            'result' => 'Success',
            'action' => 'Stop workday',
            'data' => [
                'total_work_time' => self::secondsToTime($totalWorkTimeInSeconds),
                'total_break_time' => self::secondsToTime($totalBreakTimeInSeconds),
                'workday_type' => $workdayType,
                'is_accepted' => $isWorkdayAccepted,
                'notes' => $note
            ]
        ];
    }

    private
    static function secondsToTime(int $totalTime): string
    {
        $hours = floor($totalTime / 3600);
        $minutes = floor($totalTime / 60 % 60);
        $seconds = floor($totalTime % 60);

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    private
    static function notesToString(array $notes): string
    {
        $note = '';
        foreach ($notes as $key => $val) {
            $note .= "$key:$val;";
        }
        return $note;
    }
}
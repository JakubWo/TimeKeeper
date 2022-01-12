<?php

namespace src\Service\ApiActions;

use DateTime;
use DateTimeZone;
use Exception;
use src\Service\ApiService\ApiService;
use src\Service\DatabaseService\DatabaseService;

class startAction extends ApiService
{
    /**
     * @throws Exception
     */
    public static function run(): array
    {
        $dateTime = new DateTime();
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $lastWorkdayId = $dbService->getUserLastWorkdayId($userId);
        $lastEventType = $dbService->getWorkdayLastEventType($lastWorkdayId);

        if ($lastEventType === 'start') {
            return self::errorResponse('Cannot make another start event before ending last workday');
        } elseif ($lastEventType === 'break') {
            if ($dbService->stopBreakEvent($lastWorkdayId)) {
                http_response_code(201);
                return self::successResponse('Stop break');
            } else {
                return self::errorResponse('Action failed');
            }
        }

        $userInputTimeZone = $_POST['time_zone'];
        $userInputTime = $_POST['time_input'];

        $userDbTimeZone = $dbService->getUserTimeZone($userId);
        $userTime = $dateTime->format('Y-m-d H:i:s');

        try {
            $timezoneError = 'Invalid user timezone configuration, please contact admin';
            if (!empty($userInputTimeZone)) {
                if (!is_string($userInputTimeZone)) {
                    $timezoneError = 'Invalid timezone format';
                    throw new Exception('');
                } else {
                    $timezoneError = 'Invalid timezone';
                    $dateTime->setTimezone(new DateTimeZone($userInputTimeZone));
                }
            } elseif ($userDbTimeZone !== null) {
                $dateTime->setTimezone(new DateTimeZone($userDbTimeZone));
            } else {
                throw new Exception('');
            }
        } catch (Exception $exception) {
            return self::errorResponse($timezoneError);
        }


        $note = [];
        $isWorkdayAccepted = 1;
        $workdayType = self::REGULAR;
        if (!empty($userInputTime)) {
            $workdayType = self::IRREGULAR;

            $note['CustomStart'] = strtotime($userInputTime) - strtotime('00:00');
            if (!preg_match('/^\d{2}:\d{2}$/', $userInputTime)) {
                return self::errorResponse('Invalid custom time format');
            }

            $userInputTimeParts = explode(':', $userInputTime);
            $dateTime->setTime($userInputTimeParts[0], $userInputTimeParts[1], '00');
            $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

            $userInputTime = $dateTime->format('Y-m-d H:i:s');

            $userInputTimeInSeconds = strtotime($userInputTime);
            $userTimeInSeconds = strtotime($userTime);

            $timeOff = $userTimeInSeconds - $userInputTimeInSeconds;
            if ($timeOff < 0) {
                return self::errorResponse('Cannot start workday ahead');
            } else if ($timeOff > self::REGULARITY_MAX_DIFF_STARTING_TIME) {
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
            return self::errorResponse('Action failed');
        }

        http_response_code(201);
        return self::successResponse(
            'Start workday',
            [
                'start_time' => $userInputTime,
                'workday_type' => $workdayType,
                'is_accepted' => $isWorkdayAccepted,
                'notes' => $note
            ]
        );
    }

}
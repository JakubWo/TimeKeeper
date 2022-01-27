<?php

namespace src\API\ApiActions;

use DateTime;
use DateTimeZone;
use Exception;
use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class startAction extends ApiController
{
    /**
     * @throws Exception
     */
    public static function run(): array
    {
        $dateTime = new DateTime();
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $lastWorkdayId = $dbService->getWorkdays($userId, 1)[0]['workday_id'];
        if ($lastWorkdayId !== null) {
            $lastEventType = $dbService->getWorkdayEvents($lastWorkdayId)[0]['event_type'];

            if ($lastEventType === 'start') {
                return self::errorResponse('Cannot make another start event before ending last workday');
            } elseif ($lastEventType === 'break') {
                if ($dbService->stopBreakEvent($lastWorkdayId)) {
                    return self::successPostResponse('Stop break', 200, []);
                } else {
                    return self::errorResponse('Action failed');
                }
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
        $userInputTimeInSeconds = strtotime($userInputTime) - strtotime('00:00');

        $note = [];
        $isWorkdayAccepted = 1;
        $workdayType = self::REGULAR;
        if (!empty($userInputTime)) {
            $workdayType = self::IRREGULAR;

            $note['CustomStart'] = $userInputTimeInSeconds;
            if (!preg_match('/^\d{2}:\d{2}$/', $userInputTime)) {
                return self::errorResponse('Invalid custom time format');
            }

            $userInputTimeParts = self::secondsToTime($userInputTimeInSeconds);
            $dateTime->setTime(
                $userInputTimeParts['hours'],
                $userInputTimeParts['minutes'],
                $userInputTimeParts['seconds']
            );

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
            !empty($note) ? self::notesToString($note) : ''
        ])) {
            return self::errorResponse('Action failed');
        }

        return self::successPostResponse(
            'Start workday',
            201,
            [
                'start_time' => $userInputTime,
                'workday_type' => $workdayType,
                'is_accepted' => $isWorkdayAccepted,
                'notes' => $note
            ]
        );
    }

}
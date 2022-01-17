<?php

namespace src\API\ApiActions;

use DateTime;
use Exception;
use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class stopAction extends ApiController
{
    /**
     * @throws Exception
     */
    public static function run(): array
    {
        $dateTime = new DateTime();
        $dbService = new DatabaseService();

        $userId = $_SESSION['user_id'];
        $workdayId = $dbService->getWorkdays($userId, 1)[0]['workday_id']; // swap that with get workdays
        $lastWorkdayEvents = $dbService->getWorkdayEvents($workdayId);

        $times[] = $dateTime->format('Y-m-d H:i:s');
        if ($lastWorkdayEvents[0]['event_type'] === 'stop') {
            return self::errorResponse('Cannot stop not started workday');
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
            return self::errorResponse('Action failed');
        }

        $totalWorkTime = self::secondsToTime($totalWorkTimeInSeconds);
        $totalBreakTime = self::secondsToTime($totalBreakTimeInSeconds);

        return self::successPostResponse(
            'Stop workday',
            200,
            [
                'total_work_time' => sprintf(
                    '%02d:%02d:%02d',
                    $totalWorkTime['hours'],
                    $totalWorkTime['minutes'],
                    $totalWorkTime['seconds']
                ),
                'total_break_time' => sprintf(
                    '%02d:%02d:%02d',
                    $totalBreakTime['hours'],
                    $totalBreakTime['minutes'],
                    $totalBreakTime['seconds']
                ),
                'workday_type' => $workdayType,
                'is_accepted' => $isWorkdayAccepted,
                'notes' => $note
            ]
        );
    }
}
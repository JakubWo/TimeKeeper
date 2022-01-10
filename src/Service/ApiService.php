<?php

// It's not a real API, but it should be rewritten into one later on.

namespace src\Service\ApiService;

use DateTime;
use DateTimeZone;
use Exception;
use src\Service\DatabaseService\DatabaseService;
use src\Service\ErrorService\ErrorService;

class ApiService
{
    private const REGULAR = 1;
    private const IRREGULAR = 2;

    //TYMCZASOWO
    public static function logout(): string
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        // przy loginie logoutcie musi być
        session_regenerate_id(true);
        return 'logout';
    }

    public static function start(): ?string
    {
        try {
            $dateTime = new DateTime();

            $dbService = new DatabaseService();

            $userInputTimeZone = $_POST['time_zone'];
            $userInputTime = $_POST['time_input'];

            // Taking client side data.
            if (!is_string($userInputTimeZone)) {
                throw new Exception('Invalid time_zone format');
            }

            $dateTime->setTimezone(new DateTimeZone($userInputTimeZone));
            $userTime = $dateTime->format('Y-m-d H:i');

            // Checking user time zone in database in case of manipulations, or some other situations.
            $userDbTimeZone = $dbService->checkUserTimeZone($_SESSION['user_id']);
            if ($userDbTimeZone === null) {
                throw new Exception("Database error");
            }


            $isWorkdayAccepted = 1;
            if (!empty($userInputTime) || $userInputTimeZone !== $userDbTimeZone) {
                $workdayType = self::IRREGULAR;

                if (!preg_match('/^\d{2}:\d{2}$/', $userInputTime)) {
                    throw new Exception('Invalid custom time input');
                }

                $userInputTime = $dateTime->format('Y-m-d') . ' ' . $userInputTime;

                if (abs(strtotime($userTime) - strtotime($userInputTime)) > 900) {
                    $isWorkdayAccepted = 0;
                }

            } else {
                $userInputTime = $userTime;
                $workdayType = self::REGULAR;
            }

            if ($dbService->startEvent([$_SESSION['user_id'], $userInputTime, $workdayType, $isWorkdayAccepted])) {
                $actionResult['result'] = 'Success';
            } else {
                throw new Exception('Action failed');
            }
        } catch (\PDOException $PDOException) {
            ErrorService::generate(
                'Action failed.',
                $PDOException->getMessage(),
                $PDOException->getTrace(),
                true
            );

            // Only for prod
            $actionResult['error']['title'] = $PDOException->errorInfo[2];;
        } catch (Exception $exception) {
            $actionResult['error'] = ErrorService::generate(
                'API Error!',
                $exception->getMessage(),
                $exception->getTrace()
            );
        }

        return json_encode($actionResult);
    }

    public
    static function stop()
    {
    }

    public
    static function break()
    {
    }
}
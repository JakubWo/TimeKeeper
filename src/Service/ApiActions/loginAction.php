<?php

namespace src\Service\ApiActions;

use DateTime;
use Exception;
use src\Service\ApiService\ApiService;
use src\Service\DatabaseService\DatabaseService;

class loginAction extends ApiService
{
    /**
     * @throws Exception
     */
    public static function run(): array
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $dbService = new DatabaseService();
        $now = (new DateTime())->getTimestamp();

        $blockedIp = $dbService->getBlockedIp($_SERVER['REMOTE_ADDR']);
        $ipInDatabase = $_SERVER['REMOTE_ADDR'] === $blockedIp['ip'];

        if ($ipInDatabase && $now < $blockedIp['blocked_until']) {
            return self::errorResponse('Blocked for ' . ($blockedIp['blocked_until'] - $now) . ' seconds');
        }

        $invalidData = '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $invalidData .= 'Invalid Email;';
        }
        if (strlen($password) === 0) {
            $invalidData .= 'Invalid Password';
        }

        if (strlen($invalidData) > 0) {
            return self::errorResponse($invalidData);
        }

        $userId = $dbService->getCheckUserCredentials([$email, $password]);

        if (empty($userId)) {
            $dbService->updateBlockedIpInfo($_SERVER['REMOTE_ADDR'], $now + 5, $ipInDatabase);
            return self::errorResponse('Email and/or password is incorrect');
        }

        $_SESSION['user_id'] = $userId;
        session_regenerate_id(true);
        return self::successResponse('Logged in');
    }
}


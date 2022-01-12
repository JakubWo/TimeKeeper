<?php

namespace src\Service\AuthService;

use DateTime;
use Exception;
use src\Service\DatabaseService\DatabaseService;

class AuthService
{

    /**
     * @throws Exception
     */
    public static function authenticate(): array
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $dbService = new DatabaseService();
        $now = (new DateTime())->getTimestamp();

        $blockedIp = $dbService->getBlockedIp($_SERVER['REMOTE_ADDR']);
        $ipInDatabase = $_SERVER['REMOTE_ADDR'] === $blockedIp['ip'];

        if ($ipInDatabase && $now < $blockedIp['blocked_until']) {
            throw new Exception('Action failed: Blocked for ' .
                ($blockedIp['blocked_until'] - $now) . ' seconds');
        }

        $invalidData = 'Action failed: ';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $invalidData .= 'Invalid Email;';
        }
        if (strlen($password) === 0) {
            $invalidData .= 'Invalid Password';
        }

        if (strlen($invalidData) > 15) {
            throw new Exception($invalidData);
        }

        $userId = $dbService->getCheckUserCredentials([$email, $password]);

        if (empty($userId)) {
            $dbService->updateBlockedIpInfo($_SERVER['REMOTE_ADDR'], $now + 5, $ipInDatabase);
            throw new Exception('Action failed: Email and/or password is incorrect');
        }

        $_SESSION['user_id'] = $userId;
        session_regenerate_id(true);
        return [
            'result' => 'Success',
            'action' => 'Logged in',
            'data' => []
        ];
    }
}

<?php

namespace src\API\ApiActions;

use DateTime;
use Exception;
use src\API\ApiController\ApiController;
use src\Service\DatabaseService\DatabaseService;

class loginAction extends ApiController
{
    /**
     * @throws Exception
     */
    public static function run(): array
    {
        $userIp = $_SERVER['REMOTE_ADDR'];

        $email = $_POST['email'];
        $password = $_POST['password'];

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

        $dbService = new DatabaseService();
        $now = (new DateTime())->getTimestamp();

        $blockedIp = $dbService->getBlockedIp($userIp, $email);

        if (!empty($blockedIp) && $now < $blockedIp['blocked_until']) {
            return self::errorResponse('Blocked for ' . ($blockedIp['blocked_until'] - $now) . ' seconds', 403);
        }

        $userId = $dbService->getCheckUserCredentials([$email, $password]);

        if (empty($userId)) {
            $dbService->updateBlockedIpInfo($_SERVER['REMOTE_ADDR'], $email, $now + 5, !empty($blockedIp));
            return self::errorResponse('Email and/or password is incorrect');
        }

        $basicData = $dbService->getBasicUserData($userId);
        $teamPermissions = $dbService->getTeamsPermissions($userId);
        if (empty($teamPermissions) && $basicData['is_admin'] === false) {
            return self::errorResponse('Not a member of any team, please contact Administrator');
        }

        $_SESSION['user']['privileges'] = false;
        foreach ($teamPermissions as $permission) {
            if ($permission['permission'] !== 'member') {
                $_SESSION['user']['privileges'] = true;
                break;
            }
        }

        $_SESSION['user_id'] = $userId;
        $_SESSION['user']['timezone'] = $basicData['time_zone'];
        $_SESSION['user']['balance'] = $basicData['balance'];
        $_SESSION['user']['username'] = explode('@', $email)[0];

        session_regenerate_id(true);
        return self::successPostResponse('Logged in');
    }
}


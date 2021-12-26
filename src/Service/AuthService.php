<?php

namespace src\Service\AuthService;

use src\Service\DatabaseService\DatabaseService;
use src\Service\ErrorService\ErrorService;

class AuthService
{

    public static function authenticate(): void
    {
        if (!isset($GLOBALS['dbService'])) {
            $GLOBALS['dbService'] = new DatabaseService();
        }

        $db = $GLOBALS['dbService']->getDb();

        try {
            $st = $db->prepare('SELECT check_user(?, ?)');
            $st->execute([$_POST['email'], $_POST['password']]);
            $user_id = $st->fetchColumn();
        } catch (\PDOException $PDOException) {
            ErrorService::PDOError(
                'Database login authorization exception.',
                $PDOException->getMessage(),
                $PDOException->getTrace()
            );
            header('Location: /error',);
        }

        if (!empty($user_id)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['isLoggedIn'] = true;
        } else {
            $_SESSION['isLoggedIn'] = false;
        }
    }
}

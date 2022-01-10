<?php

namespace src\Service\AuthService;

use src\Service\DatabaseService\DatabaseService;
use src\Service\ErrorService\ErrorService;

class AuthService
{

    public static function authenticate(): void
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // validation!

        $dbService = new DatabaseService();
        $db = $dbService->getDb();

        try {
            $st = $db->prepare('SELECT check_user(?, ?)');
            $st->execute([$email, $password]);
            $user_id = $st->fetchColumn();
        } catch (\PDOException $PDOException) {
            ErrorService::generate(
                'Database login authorization exception.',
                $PDOException->getMessage(),
                $PDOException->getTrace(),
                true,
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

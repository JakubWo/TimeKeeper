<?php

namespace src\Service\AuthService;

class AuthService{

    public static function authenticate() : void
    {
        $db = $GLOBALS['dbService']->getDb();

        try {
            $st = $db->prepare('SELECT check_user(?, ?)');
            $st->execute([$_POST['email'], $_POST['password']]);
            $user_id = $st->fetchColumn();
        } catch (\PDOException $PDOException) {
            //pass
        }
        if (!empty($user_id)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['isLoggedIn'] = true;
        } else {
            $_SESSION['isLoggedIn'] = false;
        }
    }
}

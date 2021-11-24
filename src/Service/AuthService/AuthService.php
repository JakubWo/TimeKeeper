<?php

namespace src\Service\AuthService;

class AuthService{

    function authenticate(array $login_params) : bool{
        $db = $GLOBALS['dbService'];
//        $result = $db->query('SELECT * FROM tk_user');
//        print_r('XD');
//        print_r($result);die;

        $d = $db->getDb();
        $st = $d -> prepare('SELECT * FROM tk_user WHERE id=?');
        $st -> execute(array('3'));

        print_r($st->fetchAll());

//        echo $_POST['email'];
//        echo $_POST['password'];

        return true;
    }

}
